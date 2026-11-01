<?php

namespace Kalakotra\Baudigit\Models;

use SilverStripe\ORM\DataObject;
use SilverStripe\i18n\i18nEntityProvider;
use Kalakotra\Baudigit\Utils\DataObjectPermissions;
use SilverStripe\Security\PermissionProvider;

class ProjectDocumentCategory extends DataObject implements PermissionProvider, i18nEntityProvider
{
    use DataObjectPermissions;

    private static $table_name = 'baudigit_ProjectDocumentCategory';

    private static $singular_name = 'Projektdokument Kategorie';

    private static $plural_name = 'Projektdokument Kategorien';

    private static $default_sort = 'Name ASC';

    public function provideI18nEntities()
    {
        return [
            __CLASS__ . '.SINGULARNAME' => 'Projektdokument Kategorie',
            __CLASS__ . '.PLURALNAME' => 'Projektdokument Kategorien',
            __CLASS__ . '.PLURALS' => [
                'one' => 'Projektdokument Kategorie',
                'other' => '{count} Projektdokument Kategorien',
            ],
        ];
    }

    private static $db = [
        'Name' => 'Varchar(255)',
    ];


}