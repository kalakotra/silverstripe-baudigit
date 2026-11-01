<?php

namespace Kalakotra\Baudigit\Pages;

use Kalakotra\Baudigit\Utils\PagePermissions;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use Kalakotra\Baudigit\Controllers\InvoicePageController;
use SilverStripe\Security\PermissionProvider;

class InvoicePage extends \Page implements PermissionProvider
{
    use PagePermissions;

    private static $table_name = 'baudigit_InvoicePage';

    private static $singular_name = 'Rechnungs-Seite';
    private static $plural_name = 'Rechnungs-Seiten';

    // define controller for this page type
    private static $controller_name = InvoicePageController::class;

    private static array $db = [
        'ShowOnlyStatus' => 'Boolean',
        'StatusValue' => 'Int', //0=Draft, 1=Sent, 2=Paid
    ];

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab('Root.Main', CheckboxField::create('ShowOnlyStatus', 'Nur Angebote mit folgendem Status anzeigen:'), 'Content');
        $fields->addFieldToTab('Root.Main', DropdownField::create('StatusValue', 'Status Wert', [
            0 => 'Entwurf',
            1 => 'Gesendet',
            2 => 'Bezahlt',
        ]), 'Content');

        return $fields;
    }
}
