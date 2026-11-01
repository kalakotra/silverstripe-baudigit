<?php

namespace Kalakotra\Utils;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;
use SilverStripe\SiteConfig\SiteConfig;

trait PagePermissions
{
    // PERMISSIONS

    // NOTE: Do not implement canView(), canEdit() and canDelete() permissions
    //       on descendants of SiteTree. These permissions are set directly
    //       on the Object via SiteTree::$canView and SiteTree::$canEdit

    /**
     * Implements custom canCreate permissions, based on the SiteTree::canCreate() function
     *
     * the private static property $unique is checked to determine if the page can't be instantiated more than one time
     *
     * This function should return true if the current user can create new pages of this class, regardless of class. It
     * can be overloaded to customise the security model for an application.
     *
     * By default, permission to create at the root level is based on the SiteConfig configuration, and permission to
     * create beneath a parent is based on the ability to edit that parent page.
     *
     * Use {@link canAddChildren()} to control behaviour of creating children under this page.
     *
     * @uses $can_create
     * @uses DataExtension->canCreate()
     *
     * @param Member $member
     * @param array $context Optional array which may contain array('Parent' => $parentObj)
     *                       If a parent page is known, it will be checked for validity.
     *                       If omitted, it will be assumed this is to be created as a top level page.
     * @return bool True if the current user can create pages on this class.
     */
    public function canCreate($member = null, $context = array())
    {
        // EXTENDED canCreate():
        // If page should be $unique and a page of that type already exists > return false
        if(property_exists(get_class($this), 'unique')){
            if(static::$unique == true && static::get()->count() > 0){
                return false;
            }
        }

        if (!$member) {
            $member = Security::getCurrentUser();
        }

        // Check parent (custom canCreate option for SiteTree)
        // Block children not allowed for this parent type
        $parent = isset($context['Parent']) ? $context['Parent'] : null;
        $strictParentInstance = ($parent && $parent instanceof SiteTree);
        if ($strictParentInstance && !in_array(static::class, $parent->allowedChildren())) {
            return false;
        }

        // Standard mechanism for accepting permission changes from extensions
        $extended = $this->extendedCan(__FUNCTION__, $member, $context);
        if ($extended !== null) {
            return $extended;
        }

        // EXTENDED canCreate():
        // Check if Member has specific permission to create this page
        return Permission::check('CREATE_'.$this->getCapitalizedClassName(), 'any', $member);

        // EXTENDED canCreate()
        // We provide no fallback if $member has no specific permission to create this page
        // Fall over to inherited permissions
//        if ($strictParentInstance && $parent->exists()) {
//            return $parent->canAddChildren($member);
//        } else {
//            // This doesn't necessarily mean we are creating a root page, but that
//            // we don't know if there is a parent, so default to this permission
//            return SiteConfig::current_site_config()->canCreateTopLevel($member);
//        }
    }

    public function providePermissions() {

        return array(
            'CREATE_'.$this->getCapitalizedClassName() => array(
                'name' => 'Kann '.$this->plural_name().' erstellen',
                'category' => $this->plural_name(),
                'sort' => 10
            )
        );
    }

    public function getCapitalizedClassName(){
        $className = __CLASS__;

        // get ClassName without namespace (only the last part)
        $classNameParts = explode("\\", $className);
        $classNameWithoutNamespace = $classNameParts[count($classNameParts)-1];
        // transform classname to uppercase
        $classNameWithoutNamespace = strtoupper($classNameWithoutNamespace);

        return $classNameWithoutNamespace;
    }
}
