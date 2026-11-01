<?php

namespace {

use SilverStripe\Forms\TextField;

    use SilverStripe\CMS\Model\SiteTree;

    class Page extends SiteTree
    {
        private static $db = [
            'MenuSeparator' => 'Varchar',
            'MenuIcon' => 'Varchar',
        ];

        private static $has_one = [];

        public function getCMSFields()
        {
            $fields = parent::getCMSFields();

            $fields->addFieldToTab('Root.Main', TextField::create(
                'MenuSeparator',
                'Menü-Trennzeichen'
            )->setDescription('Optional: Ein Trennzeichen, das im Menü angezeigt wird.'));
            $fields->addFieldToTab('Root.Main', TextField::create(
                'MenuIcon',
                'Menü-Icon'
            )->setDescription('Optional: Ein Icon-Name (z.B. aus FontAwesome), das im Menü angezeigt wird.'));
            return $fields;
        }
    }
}
