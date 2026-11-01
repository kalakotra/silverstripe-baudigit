<?php

namespace Kalakotra\Baudigit\Models;

use SilverStripe\ORM\DataObject;
use SilverStripe\Model\List\GroupedList;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use SilverStripe\Forms\DropdownField;
use SilverStripe\i18n\i18nEntityProvider;
use Kalakotra\Baudigit\Utils\DataObjectPermissions;
use SilverStripe\Security\PermissionProvider;

class ProjectTask extends DataObject implements PermissionProvider, i18nEntityProvider
{
    use DataObjectPermissions;

    private static $table_name = 'baudigit_ProjectTask';

    private static $singular_name = 'Projektaufgabe';

    private static $plural_name = 'Projektaufgaben';

    private static $default_sort = 'Status ASC, StartDate ASC';

    public function provideI18nEntities()
    {
        return [
            __CLASS__ . '.SINGULARNAME' => 'Projektaufgabe',
            __CLASS__ . '.PLURALNAME' => 'Projektaufgaben',
            __CLASS__ . '.PLURALS' => [
                'one' => 'Projektaufgabe',
                'other' => '{count} Projektaufgaben',
            ],
        ];
    }

    private static $db = [
        'Status' => 'Int', // 0=ToDo, 1=InProgress, 2=Done
        'Title' => 'Varchar(255)',
        'Description' => 'Text',
        'StartDate' => 'Date',
        'EndDate' => 'Date',
        'EstimatedHours' => 'Decimal(10,2)',
        'NeedsConfirmation' => 'Boolean', // need to have sign field
    ];

    private static $has_one = [
        'Project' => Project::class,
    ];

    private static $has_many = [
        'TaskHours' => ProjectTaskHours::class,
    ];

    private static $many_many = [
        'AssignedTo' => Member::class,
    ];

    private static $owns = [
        'TaskHours',
    ];

    private static $cascade_deletes = [
        'TaskHours',
    ];

    public function getStatusLabel()
    {
        switch ($this->Status) {
            case 0:
                return 'Zu erledigen';
            case 1:
                return 'In Arbeit';
            case 2:
                return 'Erledigt';
            default:
                return 'Unbekannt';
        }
    }


    /**
     * Check if the current user can sign hours for this task
     * needs to be today between start and end date
     * and status needs to be InProgress (1)
     * @return bool
     */
    public function canSignHours(): bool {
        $currentUser = Security::getCurrentUser();
        if (!$currentUser) {
            return false;
        }

        // check if user is assigned to this task
        $isAssigned = $this->AssignedTo()->filter('ID', $currentUser->ID)->exists();
        if (!$isAssigned) {
            return false;
        }

        // check status
        if ($this->Status !== 2) { // InProgress
            //return false;
        }

        // check date
        $today = date('Y-m-d');
        if ($this->StartDate && $today < $this->StartDate) {
            //return false;
        }
        if ($this->EndDate && $today > $this->EndDate) {
            //return false;
        }

        return true;
    }

    public function getSumHours(): float|int {
        $total = 0;
        foreach ($this->TaskHours() as $hour) {
            $total += $hour->Hours;
        }
        return $total;
    }

    public function getHoursStatus() {
        if ($this->EstimatedHours > 0) {
            $sumHours = $this->getSumHours();
            if ($sumHours < $this->EstimatedHours) {
                return 'badge bg-success';
            } elseif ($sumHours == $this->EstimatedHours) {
                return 'badge bg-success';
            } else {
                return 'badge bg-danger';
            }
        }
    }

    public function getProjectTasksGroupedByMember() {
        return GroupedList::create($this->TaskHours()->sort('LoggedByID, Created'));
    }

}