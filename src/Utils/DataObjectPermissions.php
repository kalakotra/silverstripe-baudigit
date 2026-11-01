<?php

namespace Kalakotra\Utils;

use SilverStripe\Security\Permission;

trait DataObjectPermissions
{
    /**
     * Implements custom canCreate permissions
     * @param Member $member
     * @return boolean
     */
    public function canCreate($member = null, $context = array()) {
        return Permission::check('CREATE_'.$this->getCapitalizedClassName(), 'any', $member);
    }

    /**
     * Implements custom canView permissions
     * @param Member $member
     * @return boolean
     */
    public function canView($member = null) {
        return Permission::check('VIEW_'.$this->getCapitalizedClassName(), 'any', $member);
    }

    /**
     * Implements custom canEdit permissions
     * @param Member $member
     * @return boolean
     */
    public function canEdit($member = null) {
        return Permission::check('EDIT_'.$this->getCapitalizedClassName(), 'any', $member);
    }

    /**
     * Implements custom canDelete permissions
     * @param Member $member
     * @return boolean
     */
    public function canDelete($member = null) {
        return Permission::check('DELETE_'.$this->getCapitalizedClassName(), 'any', $member);
    }

    public function providePermissions() {

        return [
            'VIEW_'.$this->getCapitalizedClassName() => [
                'name' => 'Kann '.$this->plural_name().' betrachten',
                'category' => $this->plural_name(),
                'sort' => 110
            ],
            'EDIT_'.$this->getCapitalizedClassName() => [
                'name' => 'Kann '.$this->plural_name().' bearbeiten',
                'category' => $this->plural_name(),
                'sort' => 120
            ],
            'CREATE_'.$this->getCapitalizedClassName() => [
                'name' => 'Kann '.$this->plural_name().' erstellen',
                'category' => $this->plural_name(),
                'sort' => 130
            ],
            'DELETE_'.$this->getCapitalizedClassName() => [
                'name' => 'Kann '.$this->plural_name().' löschen',
                'category' => $this->plural_name(),
                'sort' => 140
            ]
        ];

    }

    public function getCapitalizedClassName(){
        $className = __CLASS__;
        $classNameArray = explode("\\", $className);
        $classNameWithoutNamespace = $classNameArray[count($classNameArray)-1];
        $classNameWithoutNamespace = strtoupper($classNameWithoutNamespace);

        return $classNameWithoutNamespace;
    }
}