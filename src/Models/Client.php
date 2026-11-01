<?php

namespace Kalakotra\Baudigit\Models;

use Kalakotra\Baudigit\Models\Event;
use Kalakotra\Baudigit\Models\ClientObject;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\i18n\i18nEntityProvider;
use Kalakotra\Baudigit\Utils\DataObjectPermissions;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;

class Client extends DataObject implements PermissionProvider, i18nEntityProvider
{
    use DataObjectPermissions;

    private static $table_name = 'baudigit_Client';

    private static $singular_name = 'Kunde';
    private static $plural_name = 'Kunden';

    private static $default_sort = 'Company ASC, Name ASC';

    public function provideI18nEntities()
    {
        return [
            __CLASS__ . '.SINGULARNAME' => 'Kunde',
            __CLASS__ . '.PLURALNAME' => 'Kunden',
            __CLASS__ . '.PLURALS' => [
                'one' => 'Kunde',
                'other' => '{count} Kunden',
            ],
        ];
    }

    private static $db = [
        'Company'    => 'Varchar',
        'Name'       => 'Varchar',
        'Email'      => 'Varchar',
        'Phone'      => 'Varchar(50)',
        'Address'    => 'Varchar(255)',
        'PostalCode' => 'Varchar(20)',
        'City'       => 'Varchar(100)',
        // XRechnung BT-10: Leitweg-ID (Käuferreferenz), z.B. "991-12345678-06"
        // Optional — falls leer, wird die Rechnungsnummer als Fallback verwendet
        'LeitwegID'  => 'Varchar(50)',
    ];

    private static $has_many = [
        'ClientObjects' => ClientObject::class,
        //'Projects' => Project::class,
    ];

    private static $summary_fields = [
        'Company',
        'Name',
        'Email',
        'Phone',
        'Address',
        'PostalCode',
        'City',
    ];

    // german field labels
    private static $field_labels = [
        'Company' => 'Firma',
        'Name' => 'Name',
        'Email' => 'E-Mail',
        'Phone' => 'Telefon',
        'Address' => 'Adresse',
        'PostalCode' => 'PLZ',
        'City' => 'Stadt',
    ];

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->removeByName('Projects');

        // XRechnung Leitweg-ID — optional, falls Behörde/öffentlicher Auftraggeber
        $fields->addFieldToTab(
            'Root.Main',
            \SilverStripe\Forms\TextField::create('LeitwegID', 'Leitweg-ID (XRechnung BT-10)')
                ->setDescription('Nur für öffentliche Auftraggeber (z.B. "991-12345678-06"). Bei privaten Kunden leer lassen.')
        );

        // change gridfieldconfig for ClientObjects
        $gridFieldConfig = GridFieldConfig_RecordEditor::create();
        $gridField = $fields->dataFieldByName('ClientObjects');
        if ($gridField) {
            $gridField->setConfig($gridFieldConfig);

            $fields->removeByName('ClientObjects');
            $fields->addFieldToTab('Root.Kundenobjekte', $gridField);
        }

        return $fields;
    }

    public function getTitle()
    {
        return $this->Company . ' - ' . $this->Name;
    }

    public function getFullInfo() {
        $myInfo = $this->Company;
        if ($this->Address) {
            $myInfo .= ', <br />' . $this->Address;
        }
        if ($this->PostalCode) {
            $myInfo .= ', <br />' . $this->PostalCode;
        }
        if ($this->City) {
            $myInfo .= ' ' . $this->City;
        }
        return DBField::create_field('HTMLText', $myInfo);
    }
}