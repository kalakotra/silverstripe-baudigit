<?php

namespace Kalakotra\Baudigit\Models;

use SilverStripe\Assets\File;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use Kalakotra\Baudigit\Models\ProjectDocumentCategory;
use SilverStripe\i18n\i18nEntityProvider;
use Kalakotra\Baudigit\Utils\DataObjectPermissions;
use SilverStripe\Security\PermissionProvider;

class ProjectDocument extends DataObject implements PermissionProvider, i18nEntityProvider
{
    use DataObjectPermissions;

    private static $table_name = 'baudigit_ProjectDocument';

    private static $singular_name = 'Projekt-Dokumentation';
    private static $plural_name = 'Projekt-Dokumentationen';

    private static $default_sort = 'Title ASC';

    public function provideI18nEntities()
    {
        return [
            __CLASS__ . '.SINGULARNAME' => 'Projekt-Dokumentation',
            __CLASS__ . '.PLURALNAME' => 'Projekt-Dokumentationen',
            __CLASS__ . '.PLURALS' => [
                'one' => 'Projekt-Dokumentation',
                'other' => '{count} Projekt-Dokumentationen',
            ],
        ];
    }

    private static $db = [
        'Title' => 'Varchar(255)',
        'VisibleToWorker' => 'Boolean',
    ];

    private static $has_one = [
        'Project' => Project::class,
        'ProjectDocumentCategory' => ProjectDocumentCategory::class,
    ];

    private static $many_many = [
        'Documents' => File::class,
    ];

    private static $many_many_extraFields = [
        /*'Documents' => [
            'WorkerVisible' => 'Int',
        ],*/
    ];

    private static $owns = [
        'Documents',
    ];

    private static $cascade_deletes = [
        //'Documents',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        

        return $fields;
    }

    public function getVisibilityLabel()
    {
        return $this->VisibleToWorker ? 'Ja' : 'Nein';
    }

    public function validate()
    {
        $result = parent::validate();

        // Validate title is not empty // german
        if (!$this->Title || trim($this->Title) === '') {
            $result->addError('Der Titel ist erforderlich.');
        }

        return $result;
    }
}