<?php 

namespace Kalakotra\Baudigit\Models;

use Kalakotra\Baudigit\Models\Client;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\i18n\i18nEntityProvider;
use Kalakotra\Baudigit\Utils\DataObjectPermissions;
use SilverStripe\Security\PermissionProvider;

class ClientObject extends DataObject implements PermissionProvider, i18nEntityProvider
{
    use DataObjectPermissions;

    private static $table_name = 'baudigit_ClientObject';

    private static $singular_name = 'Kundenobjekt';

    private static $plural_name = 'Kundenobjekte';

    private static $default_sort = 'Name ASC';

    public function provideI18nEntities()
    {
        return [
            __CLASS__ . '.SINGULARNAME' => 'Kundenobjekt',
            __CLASS__ . '.PLURALNAME' => 'Kundenobjekte',
            __CLASS__ . '.PLURALS' => [
                'one' => 'Kundenobjekt',
                'other' => '{count} Kundenobjekte',
            ],
        ];
    }

    private static $db = [
        'Name' => 'Varchar(255)',
        'Address' => 'Varchar(255)',
        'PostalCode' => 'Varchar(20)',
        'City' => 'Varchar(100)',

        'ContactPerson' => 'Varchar(255)',
        'ContactTelephone' => 'Varchar(50)',
        'ContactEmail' => 'Varchar(255)',
        'Description' => 'Text',
    ];

    private static $has_one = [
        'Client' => Client::class,
    ];

    private static $summary_fields = [
        'Name',
        'Address',
        'PostalCode',
        'City',
        'ContactPerson',
        'ContactTelephone',
        'ContactEmail',
    ];

    private static $field_labels = [
        'Name' => 'Name',
        'Address' => 'Adresse',
        'PostalCode' => 'PLZ',
        'City' => 'Stadt',
        'ContactPerson' => 'Ansprechpartner',
        'ContactTelephone' => 'Telefon',
        'ContactEmail' => 'E-Mail',
        'Description' => 'Beschreibung',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('ClientID'); // Remove the ClientID field if it exists

        return $fields;
    }

    public function getFullName()
    {
        return DBField::create_field(
            'HTMLVarchar',
            $this->Client()->Name . ' - ' . $this->Name . ' (' . $this->Address . ')'
        );
    }

    public function getNameForPDF()
    {
        $name = "";
        $separator = "";
        if ($this->Name) {
            $name .= $this->Name;
            $separator = ", ";
        }
        if ($this->Address) {
            $name .= $separator . $this->Address;
            $separator = ", ";
        }
        if ($this->PostalCode) {
            $name .= $separator . $this->PostalCode;
        }
        if ($this->City) {
            $name .= " " . $this->City;
        }
        return $name;
    }
}