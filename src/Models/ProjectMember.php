<?php

namespace Kalakotra\Baudigit\Models;

use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Member;
use SilverStripe\i18n\i18nEntityProvider;
use Kalakotra\Baudigit\Utils\DataObjectPermissions;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\DropdownField;

class ProjectMember extends DataObject implements PermissionProvider, i18nEntityProvider
{
    use DataObjectPermissions;

    private static $table_name = 'baudigit_ProjectMember';

    private static $singular_name = 'Projekt-Mitarbeiter';
    private static $plural_name = 'Projekt-Mitarbeiter';

    private static $default_sort = 'StartDate ASC';

    public function provideI18nEntities()
    {
        return [
            __CLASS__ . '.SINGULARNAME' => 'Projekt-Mitarbeiter',
            __CLASS__ . '.PLURALNAME' => 'Projekt-Mitarbeiter',
            __CLASS__ . '.PLURALS' => [
                'one' => 'Projekt-Mitarbeiter',
                'other' => '{count} Projekt-Mitarbeiter',
            ],
        ];
    }

    private static $db = [
        'StartDate' => 'Date',
        'EndDate' => 'Date',
        'WorkDays' => 'Varchar(20)' // Stores working days as comma-separated values (1=Monday, 2=Tuesday, etc.)
    ];

    private static $has_one = [
        'Project' => Project::class,
        'Member' => Member::class,
    ];

    private static $summary_fields = [
        'Project.Title' => 'Projekt',
        'Member.Name' => 'Mitarbeiter',
        'StartDate.Nice' => 'Startdatum',
        'EndDate.Nice' => 'Enddatum',
        'getWorkDaysDisplay' => 'Arbeitstage'
    ];

    private static $field_labels = [
        'Project.Title' => 'Projekt',
        'Member.Name' => 'Mitarbeiter',
        'StartDate' => 'Startdatum',
        'EndDate' => 'Enddatum',
        'WorkDays' => 'Arbeitstage'
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
            'ProjectID',
            'MemberID',
            'WorkDays'
        ]);

        // Add Project dropdown if creating new
        if (!$this->ProjectID) {
            $fields->addFieldToTab('Root.Main', DropdownField::create(
                'ProjectID',
                'Projekt',
                Project::get()->map('ID', 'Title')->toArray()
            )->setEmptyString('-- bitte wählen --'), 'StartDate');
        }

        // Add Member dropdown
        $fields->addFieldToTab('Root.Main', DropdownField::create(
            'MemberID',
            'Mitarbeiter',
            Member::get()->map('ID', 'Name')->toArray()
        )->setEmptyString('-- bitte wählen --'), 'StartDate');

        // Add working days checkboxes
        $fields->addFieldToTab('Root.Main', $this->getWorkDaysField(), 'StartDate');

        return $fields;
    }

    public function getWorkDaysField()
    {
        $options = [
            '1' => 'Montag',
            '2' => 'Dienstag', 
            '3' => 'Mittwoch',
            '4' => 'Donnerstag',
            '5' => 'Freitag'
        ];

        $selectedDays = $this->WorkDays ? explode(',', $this->WorkDays) : ['1', '2', '3', '4', '5'];

        return \SilverStripe\Forms\CheckboxSetField::create(
            'WorkDaysArray',
            'Arbeitstage',
            $options
        )->setValue($selectedDays);
    }

    public function getWorkDaysDisplay()
    {
        if (!$this->WorkDays) {
            return 'Montag - Freitag';
        }

        $days = explode(',', $this->WorkDays);
        $dayNames = [
            '1' => 'Mo',
            '2' => 'Di',
            '3' => 'Mi', 
            '4' => 'Do',
            '5' => 'Fr'
        ];

        $displayDays = [];
        foreach ($days as $day) {
            if (isset($dayNames[$day])) {
                $displayDays[] = $dayNames[$day];
            }
        }

        return implode(', ', $displayDays);
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        // Convert WorkDaysArray to WorkDays string
        if (isset($_POST['WorkDaysArray']) && is_array($_POST['WorkDaysArray'])) {
            $this->WorkDays = implode(',', $_POST['WorkDaysArray']);
        } elseif (!$this->WorkDays) {
            // Default to Monday-Friday if not set
            $this->WorkDays = '1,2,3,4,5';
        }
    }

    public function validate()
    {
        $result = parent::validate();

        // Validate date range
        if ($this->StartDate && $this->EndDate) {
            if (strtotime($this->StartDate) > strtotime($this->EndDate)) {
                $result->addError('Startdatum muss vor dem Enddatum liegen.');
            }
        }

        // Validate against project dates if project is set
        if ($this->ProjectID && $this->Project()->exists()) {
            $project = $this->Project();
            
            if ($project->StartDate && $this->StartDate && strtotime($this->StartDate) < strtotime($project->StartDate)) {
                $result->addError('Mitarbeiter-Startdatum kann nicht vor dem Projekt-Startdatum liegen.');
            }
            
            if ($project->EndDate && $this->EndDate && strtotime($this->EndDate) > strtotime($project->EndDate)) {
                $result->addError('Mitarbeiter-Enddatum kann nicht nach dem Projekt-Enddatum liegen.');
            }
        }

        return $result;
    }

    public function getTitle()
    {
        if ($this->Member()->exists() && $this->Project()->exists()) {
            return $this->Member()->getName() . ' - ' . $this->Project()->Title;
        }
        return 'Projekt-Mitarbeiter';
    }

    public function canView($member = null)
    {
        return true;
    }

    public function canEdit($member = null)
    {
        return true;
    }

    public function canDelete($member = null)
    {
        return true;
    }

    public function canCreate($member = null, $context = [])
    {
        return true;
    }
}