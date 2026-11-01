<?php

namespace Kalakotra\Baudigit\Pages;

use Kalakotra\Baudigit\Utils\PagePermissions;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use Kalakotra\Baudigit\Controllers\OfferPageController;
use SilverStripe\Security\PermissionProvider;

class OfferPage extends \Page implements PermissionProvider
{
    use PagePermissions;

    private static $table_name = 'baudigit_OfferPage';

    private static $singular_name = 'Angebots-Seite';
    private static $plural_name = 'Angebots-Seiten';

    // define controller for this page type
    private static $controller_name = OfferPageController::class;

    private static array $db = [
        'ShowOnlyStatus' => 'Boolean',
        'StatusValue' => 'Int', //0=Draft, 1=Sent, 2=Accepted, 3=Project, 4=Rejected
    ];

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab('Root.Main', CheckboxField::create('ShowOnlyStatus', 'Nur Angebote mit folgendem Status anzeigen:'), 'Content');
        $fields->addFieldToTab('Root.Main', DropdownField::create('StatusValue', 'Status Wert', [
            0 => 'Entwurf',
            1 => 'Gesendet',
            2 => 'Akzeptiert',
            3 => 'Projekt',
            4 => 'Abgelehnt',
        ]), 'Content');

        return $fields;
    }
}