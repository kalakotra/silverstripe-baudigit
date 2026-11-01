<?php
/*
namespace Kalakotra\Baudigit\Models;

use Kalakotra\Baudigit\Models\Client;
use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\DataObject;
use SilverStripe\i18n\i18nEntityProvider;
use Kalakotra\Baudigit\Utils\DataObjectPermissions;
use SilverStripe\Security\PermissionProvider;

class ArticleCategory extends DataObject implements PermissionProvider, i18nEntityProvider
{
    use DataObjectPermissions;

    private static $table_name = 'baudigit_ArticleCategory';

    private static $singular_name = 'Artikelkategorie';

    private static $plural_name = 'Artikelkategorien';

    private static $default_sort = 'Title ASC';

    public function provideI18nEntities()
    {
        return [
            __CLASS__ . '.SINGULARNAME' => 'Artikelkategorie',
            __CLASS__ . '.PLURALNAME' => 'Artikelkategorien',
            __CLASS__ . '.PLURALS' => [
                'one' => 'Artikelkategorie',
                'other' => '{count} Artikelkategorien',
            ],
        ];
    }

    private static $db = [
        'InternalID' => 'Varchar(100)',
        'Title' => 'Varchar(255)',
        'Description' => 'Text',
    ];

    private static $summary_fields = [
        'InternalID',
        'Title',
        'Description.Summary',
    ];

    private static $field_labels = [
        'InternalID' => 'Interne ID',
        'Title' => 'Titel',
        'Description' => 'Beschreibung',
    ];
}
    */