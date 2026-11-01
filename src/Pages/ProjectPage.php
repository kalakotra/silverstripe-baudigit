<?php

namespace Kalakotra\Baudigit\Pages;

use Kalakotra\Baudigit\Utils\PagePermissions;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use Kalakotra\Baudigit\Controllers\ProjectPageController;
use SilverStripe\Security\PermissionProvider;

class ProjectPage extends \Page implements PermissionProvider
{
    use PagePermissions;

    private static $table_name = 'baudigit_ProjectPage';

    private static $singular_name = 'Projekt-Seite';
    private static $plural_name = 'Projekt-Seiten';

    // define controller for this page type
    private static $controller_name = ProjectPageController::class;

    private static array $db = [
        'ShowOnlyStatus' => 'Boolean',
        'StatusValue' => 'Int', //0=Planned/New, 1=InProgress, 2=Completed, 3=Billed, 4=Canceled
    ];

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab('Root.Main', CheckboxField::create('ShowOnlyStatus', 'Nur Angebote mit folgendem Status anzeigen:'), 'Content');
        $fields->addFieldToTab('Root.Main', DropdownField::create('StatusValue', 'Status Wert', [
            0 => 'Neu',
            1 => 'Laufend',
            2 => 'Abgeschlossen',
            3 => 'Abgerechnet',
            4 => 'Storniert',
        ]), 'Content');

        return $fields;
    }
}