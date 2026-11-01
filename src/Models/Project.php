<?php

namespace Kalakotra\Baudigit\Models;

use Kalakotra\Baudigit\Models\Offer;
use Kalakotra\Baudigit\Models\Client;
use Kalakotra\Baudigit\Models\Invoice;
use Kalakotra\Baudigit\Models\ProjectTask;
use Kalakotra\Baudigit\Models\ProjectMember;
use SilverStripe\Assets\File;
use Kalakotra\Baudigit\Models\ProjectDocument;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Member;
use SilverStripe\Forms\DropdownField;
use Kalakotra\Baudigit\Models\ProjectDocumentCategory;
use SilverStripe\i18n\i18nEntityProvider;
use Kalakotra\Baudigit\Utils\DataObjectPermissions;
use SilverStripe\Security\PermissionProvider;


class Project extends DataObject implements PermissionProvider, i18nEntityProvider
{
    use DataObjectPermissions;

    private static $table_name = 'baudigit_Project';

    private static $singular_name = 'Projekt';

    private static $plural_name = 'Projekte';

    private static $default_sort = 'Status ASC, Created DESC';

    public function provideI18nEntities()
    {
        return [
            __CLASS__ . '.SINGULARNAME' => 'Projekt',
            __CLASS__ . '.PLURALNAME' => 'Projekte',
            __CLASS__ . '.PLURALS' => [
                'one' => 'Projekt',
                'other' => '{count} Projekte',
            ],
        ];
    }

    private static $db = [
        'Status' => 'Int', // 0=Planned/New, 1=InProgress, 2=Completed, 3=Billed, 4=Canceled
        'Title' => 'Varchar(255)',
        'Description' => 'Text',
        'StartDate' => 'Date',
        'EndDate' => 'Date',
        'HourlyRate' => 'Decimal(10,2)', // Stundensatz (€/h) für Profitabilitätsberechnung
    ];

    private static $has_one = [
        'ClientObject' => ClientObject::class,
        'Offer' => Offer::class,
    ];

    private static $has_many = [
        'ProjectDocuments' => ProjectDocument::class,
        'ProjectTasks' => ProjectTask::class,
    ];

    private static $many_many = [
        'Documents' => File::class,
    ];

    private static $owns = [
        'Documents',
    ];

    private static $cascade_deletes = [
        'ProjectDocuments',
        'ProjectTasks',
    ];

    private static $many_many_extraFields = [
        'Documents' => [
            'Category' => 'Int',
        ],
    ];

    private static $summary_fields = [
        'Title',
        'ClientObject.Name',
    ];

    // german field labels
    private static $field_labels = [
        'Title' => 'Titel',
        'Description' => 'Beschreibung',
        'Date' => 'Datum',
        'Client.Company' => 'Kunde',
        'ClientObject.Name' => 'Kundenobjekt',
    ];

    public function getStatusLabel()
    {
        switch ($this->Status) {
            case 0:
                return 'Neu';
            case 1:
                return 'Laufend';
            case 2:
                return 'Abgeschlossen';
            case 3:
                return 'Abgerechnet';
            case 4:
                return 'Storniert';
            default:
                return 'Unbekannt';
        }
    }

    public function getGermanStatusOptions(): array
    {
        return [
            0 => 'Neu',
            1 => 'Laufend',
            2 => 'Abgeschlossen',
            3 => 'Abgerechnet',
            4 => 'Storniert',
        ];
    }

    public function getGermanStatusOptionsDataList()
    {

        $myList = ArrayList::create();
        foreach ($this->getGermanStatusOptions() as $key => $value) {
            $myItem = [
                'isActive' => ($this->Status == $key) ? 'active' : '',
                'Key' => $key,
                'Title' => $value,
            ];
            $myList->push($myItem);
        }
        return $myList;
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
            'Status',
            'ClientObjectID'
        ]);

        if ($this->ID) {
            $fields->addFieldToTab('Root.Main', DropdownField::create(
                'Status',
                'Status',
                [
                    0 => 'Neu',
                    1 => 'Laufend',
                    2 => 'Abgeschlossen',
                    3 => 'Abgerechnet',
                    4 => 'Storniert',
                ]
            ), 'Title');
        }

        if ($this->ClientID) {
            $fields->addFieldToTab('Root.Main', DropdownField::create(
                'ClientObjectID',
                'Kundenobjekt',
                $this->Client()->ClientObjects()->map('ID', 'Name')->toArray()
            ), );
        }

        
        return $fields;
    }

    public function getUploadFolders() {
        return [
            'doc' => 'projects/' . $this->ID . '/documents',
            'task' => 'projects/' . $this->ID . '/tasks',
        ];
    }

    /**
     * Get all members assigned to this project
     */
    public function getAssignedMembers()
    {
        /*return Member::get()->innerJoin('app_ProjectMember', 'app_ProjectMember.MemberID = Member.ID')
            ->where(['app_ProjectMember.ProjectID' => $this->ID]);*/
    }

    /**
     * Get members assigned to project within a specific date range
     */
    public function getMembersForDateRange($startDate, $endDate)
    {
        /*return $this->ProjectMembers()->filter([
            'StartDate:LessThanOrEqual' => $endDate,
            'EndDate:GreaterThanOrEqual' => $startDate
        ]);*/
    }

    /**
     * Check if a member is assigned to this project during a specific period
     */
    public function isMemberAssigned($memberID, $startDate = null, $endDate = null)
    {
        $filter = ['MemberID' => $memberID];
        
        if ($startDate && $endDate) {
            $filter['StartDate:LessThanOrEqual'] = $endDate;
            $filter['EndDate:GreaterThanOrEqual'] = $startDate;
        }
        
        //return $this->ProjectMembers()->filter($filter)->count() > 0;
    }

    // ─── Invoices ────────────────────────────────────────────────────────────────

    public function Invoices()
    {
        return Invoice::get()->filter('ProjectID', $this->ID);
    }

    // ─── Profitabilität ──────────────────────────────────────────────────────────

    /**
     * Gesamter fakturierter Betrag (Brutto) aller verknüpften Rechnungen.
     */
    public function getTotalInvoiced(): float
    {
        $total = 0.0;
        foreach (Invoice::get()->filter('ProjectID', $this->ID) as $invoice) {
            $total += (float) $invoice->getTotalBrutto(true);
        }
        return round($total, 2);
    }

    /**
     * Gesamte gebuchte Stunden über alle Aufgaben dieses Projekts.
     */
    public function getTotalHoursLogged(): float
    {
        $hours = 0.0;
        foreach ($this->ProjectTasks() as $task) {
            foreach ($task->TaskHours() as $entry) {
                $hours += (float) $entry->Hours;
            }
        }
        return round($hours, 2);
    }

    /**
     * Gesamte Lohnkosten: Stunden × Stundensatz.
     */
    public function getTotalLaborCost(): float
    {
        $rate = (float) $this->HourlyRate;
        if ($rate <= 0) {
            return 0.0;
        }
        return round($this->getTotalHoursLogged() * $rate, 2);
    }

    /**
     * Gewinn = fakturiert − Lohnkosten (bei positivem Stundensatz).
     */
    public function getProfitAmount(): float
    {
        return round($this->getTotalInvoiced() - $this->getTotalLaborCost(), 2);
    }

    /**
     * Marge in Prozent (0 wenn nichts fakturiert wurde).
     */
    public function getProfitMarginPercent(): float
    {
        $invoiced = $this->getTotalInvoiced();
        if ($invoiced <= 0) {
            return 0.0;
        }
        return round(($this->getProfitAmount() / $invoiced) * 100, 1);
    }

    /**
     * Check if project can be closed // if all tasks are completed
     * @return bool
     */
    public function canBeClosed(): bool {
        $filter = ['Status:not' => 2]; // not completed
        return $this->ProjectTasks()->filter($filter)->count() > 0;
    }

    /**
     * check if projectTasks & ProjectDocuments cann be added
     * @return bool
     */
    public function canAddTasksAndDocuments(): bool {
        return !in_array($this->Status, [2,3,4]); // not Completed, Billed, Canceled
    }
}