<?php

declare(strict_types=1);

namespace Kalakotra\Baudigit\Controllers;

use Kalakotra\Baudigit\Models\Offer;
use Kalakotra\Baudigit\Models\Client;
use Kalakotra\Baudigit\Models\Invoice;
use Kalakotra\Baudigit\Models\Project;
use Kalakotra\Baudigit\Models\ProjectTask;
use Kalakotra\Baudigit\Models\ClientObject;
use SilverStripe\Forms\Form;
use Kalakotra\Baudigit\Models\ProjectMember;
use SilverStripe\Assets\File;
use SilverStripe\ORM\DataList;
use Kalakotra\Baudigit\Models\ProjectDocument;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FileField;
use SilverStripe\Forms\TextField;
use SilverStripe\Security\Member;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\ListboxField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\Validation\RequiredFieldsValidator;
use Kalakotra\Baudigit\Models\ProjectDocumentCategory;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\GroupedDropdownField;

class ProjectPageController extends \PageController
{
    use \Kalakotra\Baudigit\Traits\FeatureToggleTrait;

    protected function init(): void
    {
        parent::init();
        if ($err = $this->checkEnabled()) {
            return;
        }
    }
    private static array $allowed_actions = [
        'add',
        'doAdd',
        'edit',
        'doEdit',
        'changeStatus',
        'addMemberForm',
        'doAddMember',
        'editMemberForm',
        'doEditMember',
        'deleteMember',
        'uploadDocuments',
        'doUploadDocuments',
        'addDocumentForm',
        'doAddDocument',
        'editDocumentForm',
        'doEditDocument',
        'deleteDocument',
        'addTaskForm',
        'doAddTask',
        'editTaskForm',
        'doEditTask',
        'deleteTask',
    ];

    private static array $url_handlers = [
        'add'                           => 'add',
        'edit/$ID!'                     => 'edit',
        'edit/$ID!/member/$SubID'       => 'edit',
        'edit/$ID!/document/$SubID'     => 'edit',
        'edit/$ID!/task/$SubID'         => 'edit',
        'change-status/$ID!/$Status!'   => 'changeStatus',
        'member/add'                    => 'addMemberForm',
        'member/$SubID!'                => 'editMemberForm',
        'member/delete/$SubID!'         => 'deleteMember',
        'document/add'                  => 'addDocumentForm',
        'document/$SubID!'              => 'editDocumentForm',
        'document/delete/$SubID!'       => 'deleteDocument',
        'documents/upload'              => 'uploadDocuments',
        'task/add'                      => 'addTaskForm',
        'task/$SubID!'                  => 'editTaskForm',
        'task/delete/$SubID!'           => 'deleteTask',
    ];

    // ─── Project lookup ──────────────────────────────────────────────────────────

    private function actProject(): ?Project
    {
        $id = $this->resourceID();
        if (!$id) {
            return null;
        }
        $project = Project::get()->byID($id);
        return ($project && $project->exists()) ? $project : null;
    }

    // ─── Template helpers ─────────────────────────────────────────────────────────

    public function readAllInputs(): DataList
    {
        $showOnlyStatus = $this->data()->ShowOnlyStatus;
        $statusValue    = $this->data()->StatusValue;
        if ($showOnlyStatus) {
            return Project::get()->filter('Status', $statusValue);
        }
        return Project::get();
    }

    /** Which accordion tab is open based on current URL segment. */
    public function checkActiveAccordion(): string
    {
        $request = $this->getRequest();
        if ($request->param('SubID') && str_contains($request->getURL(), '/document/')) {
            return 'documents';
        }
        if ($request->param('SubID') && str_contains($request->getURL(), '/member/')) {
            return 'members';
        }
        if ($request->param('SubID') && str_contains($request->getURL(), '/task/')) {
            return 'tasks';
        }
        return 'details';
    }

    /** Currently-edited ProjectMember (from URL /member/$SubID). */
    private function actMember(): ?ProjectMember
    {
        $id = $this->subResourceID();
        if (!$id) {
            return null;
        }
        $member = ProjectMember::get()->byID($id);
        return ($member && $member->exists()) ? $member : null;
    }

    /** Currently-edited ProjectDocument (from URL /document/$SubID). */
    private function actDocument(): ?ProjectDocument
    {
        $id = $this->subResourceID();
        if (!$id) {
            return null;
        }
        $doc = ProjectDocument::get()->byID($id);
        return ($doc && $doc->exists()) ? $doc : null;
    }

    /** Currently-edited ProjectTask (from URL /task/$SubID). */
    private function actTask(): ?ProjectTask
    {
        $id = $this->subResourceID();
        if (!$id) {
            return null;
        }
        $task = ProjectTask::get()->byID($id);
        return ($task && $task->exists()) ? $task : null;
    }

    /** Grouped client list for dropdown. */
    private function buildClientGroupedList(): array
    {
        $grouped = [];
        foreach (Client::get() as $client) {
            $objects = ClientObject::get()->filter('ClientID', $client->ID);
            if ($objects->count() > 0) {
                $grouped[$client->Company] = $objects->map('ID', 'getFullName')->toArray();
            }
        }
        return $grouped;
    }

    // ─── Project add/edit ─────────────────────────────────────────────────────────

    public function add()
    {
        $formFields = FieldList::create(
            TextField::create('Title', 'Titel')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextareaField::create('Description', 'Beschreibung')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control')->setRows(4),
            GroupedDropdownField::create('ClientObjectID', 'Kunde', $this->buildClientGroupedList())->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-select select2me')->setEmptyString('-- bitte wählen --'),
            DateField::create('StartDate', 'Startdatum')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            DateField::create('EndDate', 'Enddatum')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            NumericField::create('HourlyRate', 'Stundensatz (€/h)')->setScale(2)->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control')->setAttribute('placeholder', '0,00'),
        );

        $actions = FieldList::create(
            FormAction::create('doAdd', 'Speichern')->addExtraClass('btn btn-sm btn-success px-4 mb-2')
        );

        // Pre-fill from offer if ?o=ID is given (legacy support; new links may also use this)
        $offerID = $this->getRequest()->getVar('o');
        if ($offerID && is_numeric($offerID)) {
            $offer = Offer::get()->byID((int) $offerID);
            if ($offer && $offer->exists()) {
                $formFields->push(HiddenField::create('OfferID', 'Angebot')->setValue($offer->ID));
                $actions->push(LiteralField::create('cancel', '<a href="javascript:history.back()" class="btn btn-sm btn-secondary px-4 mb-2 ms-2">Abbrechen</a>'));
            }
        }

        $form = Form::create($this, 'add', $formFields, $actions, RequiredFieldsValidator::create(['Title', 'ClientObjectID']));

        if (isset($offer) && $offer->exists()) {
            $form->loadDataFrom([
                'Title'          => 'Projekt zu Angebot ' . $offer->OfferNumber,
                'ClientObjectID' => $offer->ClientObjectID,
            ]);
        }

        return $form;
    }

    public function doAdd(array $data, Form $form)
    {
        $project = new Project();
        $form->saveInto($project);

        if (isset($data['OfferID']) && is_numeric($data['OfferID'])) {
            $offer = Offer::get()->byID((int) $data['OfferID']);
            if ($offer && $offer->exists()) {
                $offer->Status = 'Project';
                $offer->write();
                $project->OfferID = $offer->ID;
            }
        }

        $project->write();
        $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Projekt erfolgreich erstellt.');
        return $this->redirect($this->Link('edit/' . $project->ID));
    }

    public function edit()
    {
        $project = $this->actProject();
        if (!$project) {
            return $this->httpError(404);
        }

        $addForm    = $this->add();
        $formFields = $addForm->Fields();
        $formFields->push(HiddenField::create('ProjectID', 'ID')->setValue($project->ID));

        $actions = FieldList::create(
            FormAction::create('doEdit', 'Speichern')->addExtraClass('btn btn-sm btn-success px-4 mb-2')
        );

        $form = Form::create($this, 'edit', $formFields, $actions, RequiredFieldsValidator::create(['Title', 'ClientObjectID']));
        $form->loadDataFrom($project);
        $form->setFormAction($this->Link('edit/' . $project->ID));

        return $form;
    }

    public function doEdit(array $data, Form $form)
    {
        $project = $this->actProject();
        if (!$project) {
            $this->setNotifySessionData('error', 'bi bi-x-circle', 'Projekt nicht gefunden.');
            return $this->redirectBack();
        }

        $form->saveInto($project);
        $project->write();

        $this->setNotifySessionData('success', 'bi bi-check2-circle', _t('Kalakotra\\Baudigit\\Controllers\\ProjectPageController.PROJECT_SAVED', 'Projekt erfolgreich gespeichert.'));
        return $this->redirectBack();
    }

    public function changeStatus()
    {
        $project = $this->actProject();
        if ($project) {
            $status       = $this->getRequest()->param('Status');
            $validStatuses = array_keys($project->getGermanStatusOptions());
            if (is_numeric($status) && in_array((int) $status, $validStatuses)) {
                $project->Status = (int) $status;
                $project->write();
                $this->setNotifySessionData('success', 'bi bi-check2-circle', _t('Kalakotra\\Baudigit\\Controllers\\ProjectPageController.STATUS_CHANGED', 'Projektstatus erfolgreich geändert.'));
                return $this->redirectBack();
            }
        }

        $this->setNotifySessionData('error', 'bi bi-x-circle', 'Fehler beim Ändern des Projektstatus.');
        return $this->redirectBack();
    }

    // ─── Template getters ─────────────────────────────────────────────────────────

    public function getCurrentProject(): ?Project
    {
        return $this->actProject();
    }

    public function getCurrentMember(): ?ProjectMember
    {
        return $this->actMember();
    }

    public function getCurrentDocument(): ?ProjectDocument
    {
        return $this->actDocument();
    }

    public function getCurrentTask(): ?ProjectTask
    {
        return $this->actTask();
    }

    public function getAllProjectMembers()
    {
        $project = $this->actProject();
        return $project ? $project->ProjectMembers() : false;
    }

    public function getAllProjectDocuments()
    {
        $project = $this->actProject();
        return $project ? $project->ProjectDocuments() : false;
    }

    public function getAllProjectTasks()
    {
        $project = $this->actProject();
        return $project ? $project->ProjectTasks() : false;
    }

    public function getProjectDocumentCategories()
    {
        return ProjectDocumentCategory::get();
    }

    // ─── Member forms ─────────────────────────────────────────────────────────────

    public function addMemberForm()
    {
        $project = $this->actProject();
        if (!$project) {
            return false;
        }

        $allMembers = Member::get()->filter('Groups.ID', '3')->map('ID', 'Name')->toArray();
        $minDate    = $project->StartDate ?: date('Y-m-d');
        $maxDate    = $project->EndDate   ?: date('Y-m-d', strtotime('+5 year'));

        $formFields = FieldList::create(
            DropdownField::create('MemberID', 'Mitarbeiter', $allMembers)->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-select')->setEmptyString('-- bitte wählen --'),
            DateField::create('StartDate', 'Startdatum')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control')->setMinDate($minDate)->setMaxDate($maxDate),
            DateField::create('EndDate', 'Enddatum')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control')->setMinDate($minDate)->setMaxDate($maxDate),
            HiddenField::create('ProjectID', 'Projekt')->setValue($project->ID),
        );

        $form = Form::create(
            $this, 'addMemberForm', $formFields,
            FieldList::create(FormAction::create('doAddMember', 'Hinzufügen')->addExtraClass('btn btn-sm btn-success px-4 mb-2')),
            RequiredFieldsValidator::create(['MemberID', 'StartDate', 'EndDate'])
        );
        $form->setFormAction($this->Link('member/add'));

        return $form;
    }

    public function doAddMember(array $data, Form $form)
    {
        $projectID = isset($data['ProjectID']) && is_numeric($data['ProjectID']) ? (int) $data['ProjectID'] : null;
        $project   = $projectID ? Project::get()->byID($projectID) : null;

        if ($project && $project->exists()) {
            $pm            = new ProjectMember();
            $pm->ProjectID = $project->ID;
            $pm->MemberID  = (int) $data['MemberID'];
            $pm->StartDate = $data['StartDate'];
            $pm->EndDate   = $data['EndDate'];
            $pm->WorkDays  = isset($data['WorkDaysArray']) && is_array($data['WorkDaysArray'])
                ? implode(',', $data['WorkDaysArray'])
                : '1,2,3,4,5';
            $pm->write();

            $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Mitarbeiter erfolgreich hinzugefügt.');
            return $this->redirect($this->Link('edit/' . $project->ID . '/member/' . $pm->ID));
        }

        $this->setNotifySessionData('error', 'bi bi-x-circle', 'Fehler beim Hinzufügen des Mitarbeiters.');
        return $this->redirectBack();
    }

    public function editMemberForm()
    {
        $pm = $this->actMember();
        if (!$pm) {
            return $this->httpError(404);
        }

        $addFields  = $this->addMemberForm()->Fields();
        $formFields = $addFields;
        $formFields->push(HiddenField::create('MemberRecordID', 'ID')->setValue($pm->ID));

        $form = Form::create(
            $this, 'editMemberForm', $formFields,
            FieldList::create(FormAction::create('doEditMember', 'Speichern')->addExtraClass('btn btn-sm btn-success px-4 mb-2')),
            RequiredFieldsValidator::create(['MemberID', 'StartDate', 'EndDate'])
        );
        $form->loadDataFrom($pm);
        $form->setFormAction($this->Link('member/' . $pm->ID));

        return $form;
    }

    public function doEditMember(array $data, Form $form)
    {
        $id = isset($data['MemberRecordID']) && is_numeric($data['MemberRecordID']) ? (int) $data['MemberRecordID'] : null;
        $pm = $id ? ProjectMember::get()->byID($id) : null;

        if ($pm && $pm->exists()) {
            $pm->MemberID  = (int) $data['MemberID'];
            $pm->StartDate = $data['StartDate'];
            $pm->EndDate   = $data['EndDate'];
            $pm->WorkDays  = isset($data['WorkDaysArray']) && is_array($data['WorkDaysArray'])
                ? implode(',', $data['WorkDaysArray'])
                : '1,2,3,4,5';
            $pm->write();

            $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Mitarbeiter erfolgreich gespeichert.');
            return $this->redirect($this->Link('edit/' . $pm->ProjectID . '/member/' . $pm->ID));
        }

        $this->setNotifySessionData('error', 'bi bi-x-circle', 'Fehler beim Speichern des Mitarbeiters.');
        return $this->redirectBack();
    }

    public function deleteMember()
    {
        $memberID = $this->subResourceID();
        if (!$memberID) {
            return $this->httpError(400);
        }

        $pm = ProjectMember::get()->byID($memberID);
        if ($pm && $pm->exists()) {
            $projectID = $pm->ProjectID;
            $pm->delete();
            $pm->destroy();
            $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Mitarbeiter entfernt.');
            return $this->redirect($this->Link('edit/' . $projectID));
        }

        return $this->redirectBack();
    }

    // ─── Document forms ───────────────────────────────────────────────────────────

    public function uploadDocuments()
    {
        $project = $this->actProject();
        if (!$project) {
            return false;
        }

        $formFields = FieldList::create(
            HiddenField::create('ProjectID', 'Projekt')->setValue($project->ID),
            DropdownField::create('Category', 'Dokumenten-Kategorie',
                ProjectDocumentCategory::get()->map('ID', 'Name'))
                ->setFieldHolderTemplate('FormField_customholder')
                ->addExtraClass('form-select')
                ->setEmptyString('-- bitte wählen --'),
            FileField::create('Documents[]', 'Dateien')
                ->setFolderName($project->getUploadFolders()['doc'])
                ->setAttribute('multiple', true)
                ->addExtraClass('form-upload'),
        );

        $form = Form::create(
            $this, 'uploadDocuments', $formFields,
            FieldList::create(FormAction::create('doUploadDocuments', 'Hochladen')->addExtraClass('btn btn-sm btn-success px-4 mb-2')),
            RequiredFieldsValidator::create(['Category', 'Documents'])
        );
        $form->setFormAction($this->Link('documents/upload'));

        return $form;
    }

    public function doUploadDocuments(array $data, Form $form)
    {
        $projectID = isset($data['ProjectID']) && is_numeric($data['ProjectID']) ? (int) $data['ProjectID'] : null;
        $project   = $projectID ? Project::get()->byID($projectID) : null;

        if ($project && $project->exists() && isset($data['Documents']) && is_array($data['Documents'])) {
            $folder   = $project->getUploadFolders()['doc'];
            $fileCount = count($data['Documents']['name']);
            $keys      = array_keys($data['Documents']);

            for ($i = 0; $i < $fileCount; $i++) {
                $fileData = array_combine($keys, array_column($data['Documents'], $i));
                if (empty($fileData['tmp_name'])) {
                    continue;
                }
                $file = new File();
                $file->setFromLocalFile($fileData['tmp_name'], $folder . '/' . $fileData['name']);
                $file->Title = $fileData['name'];
                $file->write();
                $project->Documents()->add($file, ['Category' => $data['Category']]);
            }
            $project->write();

            $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Dateien erfolgreich hochgeladen.');
            return $this->redirect($this->Link('edit/' . $project->ID . '?activetab=project_documents'));
        }

        $this->setNotifySessionData('error', 'bi bi-x-circle', 'Fehler beim Hochladen der Dateien.');
        return $this->redirectBack();
    }

    public function addDocumentForm()
    {
        $project = $this->actProject();
        if (!$project) {
            return false;
        }

        $formFields = FieldList::create(
            HiddenField::create('ProjectID', 'Projekt')->setValue($project->ID),
            DropdownField::create('ProjectDocumentCategoryID', 'Dokumenten-Kategorie',
                ProjectDocumentCategory::get()->map('ID', 'Name'))
                ->setFieldHolderTemplate('FormField_customholder')
                ->addExtraClass('form-select')
                ->setEmptyString('-- bitte wählen --'),
            TextField::create('Title', 'Dokumentationstitel')->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-control'),
            CheckboxField::create('VisibleToWorker', 'Sichtbar für Arbeiter')->setFieldHolderTemplate('FormField_customholder')->setValue(true),
            FileField::create('Documents[]', 'Dateien')
                ->setFolderName($project->getUploadFolders()['doc'])
                ->setAttribute('multiple', true)
                ->addExtraClass('form-upload'),
        );

        $form = Form::create(
            $this, 'addDocumentForm', $formFields,
            FieldList::create(FormAction::create('doAddDocument', 'Speichern')->addExtraClass('btn btn-sm btn-success px-4 mb-2')),
            RequiredFieldsValidator::create(['ProjectDocumentCategoryID', 'Title'])
        );
        $form->setFormAction($this->Link('document/add'));

        return $form;
    }

    public function doAddDocument(array $data, Form $form)
    {
        $projectID = isset($data['ProjectID']) && is_numeric($data['ProjectID']) ? (int) $data['ProjectID'] : null;
        $project   = $projectID ? Project::get()->byID($projectID) : null;

        if ($project && $project->exists()) {
            $doc            = new ProjectDocument();
            $doc->ProjectID = $project->ID;
            $form->saveInto($doc);
            $doc->write();

            if (isset($data['Documents']) && is_array($data['Documents'])) {
                $folder    = 'project-documents/' . $doc->ProjectID;
                $fileCount = count($data['Documents']['name']);
                $keys      = array_keys($data['Documents']);

                for ($i = 0; $i < $fileCount; $i++) {
                    $fileData = array_combine($keys, array_column($data['Documents'], $i));
                    if (empty($fileData['tmp_name'])) {
                        continue;
                    }
                    $file = new File();
                    $file->setFromLocalFile($fileData['tmp_name'], $folder . '/' . $fileData['name']);
                    $file->Title = $fileData['name'];
                    $file->write();
                    $doc->Documents()->add($file);
                }
                $doc->write();
            }

            $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Dokumenten-Kategorie erfolgreich hinzugefügt.');
            return $this->redirect($this->Link('edit/' . $project->ID . '/document/' . $doc->ID));
        }

        $this->setNotifySessionData('error', 'bi bi-x-circle', 'Fehler beim Hinzufügen der Dokumenten-Kategorie.');
        return $this->redirectBack();
    }

    public function editDocumentForm()
    {
        $doc = $this->actDocument();
        if (!$doc) {
            return $this->httpError(404);
        }

        $formFields = $this->addDocumentForm()->Fields();
        $formFields->push(HiddenField::create('DocumentID', 'ID')->setValue($doc->ID));
        $formFields->removeByName('Documents[]');
        $formFields->push(
            FileField::create('Documents[]', 'Dateien')
                ->setFolderName($doc->Project()->getUploadFolders()['doc'] . '/' . $doc->ID)
                ->setAttribute('multiple', true)
        );

        $form = Form::create(
            $this, 'editDocumentForm', $formFields,
            FieldList::create(FormAction::create('doEditDocument', 'Speichern')->addExtraClass('btn btn-sm btn-success px-4 mb-2')),
            RequiredFieldsValidator::create(['Title'])
        );
        $form->loadDataFrom($doc);
        $form->setFormAction($this->Link('document/' . $doc->ID));

        return $form;
    }

    public function doEditDocument(array $data, Form $form)
    {
        $id  = isset($data['DocumentID']) && is_numeric($data['DocumentID']) ? (int) $data['DocumentID'] : null;
        $doc = $id ? ProjectDocument::get()->byID($id) : null;

        if ($doc && $doc->exists()) {
            $form->saveInto($doc);
            $doc->write();

            if (isset($data['Documents']) && is_array($data['Documents'])) {
                $folder    = 'project-documents/' . $doc->ProjectID;
                $fileCount = count($data['Documents']['name']);
                $keys      = array_keys($data['Documents']);

                for ($i = 0; $i < $fileCount; $i++) {
                    $fileData = array_combine($keys, array_column($data['Documents'], $i));
                    if (empty($fileData['tmp_name'])) {
                        continue;
                    }
                    $file = new File();
                    $file->setFromLocalFile($fileData['tmp_name'], $folder . '/' . $fileData['name']);
                    $file->Title = $fileData['name'];
                    $file->write();
                    $doc->Documents()->add($file);
                }
                $doc->write();
            }

            $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Dokumenten-Kategorie erfolgreich gespeichert.');
            return $this->redirectBack();
        }

        $this->setNotifySessionData('error', 'bi bi-x-circle', 'Fehler beim Speichern der Dokumenten-Kategorie.');
        return $this->redirectBack();
    }

    public function deleteDocument()
    {
        $docID = $this->subResourceID();
        if (!$docID) {
            return $this->httpError(400);
        }

        $doc = ProjectDocument::get()->byID($docID);
        if ($doc && $doc->exists()) {
            $projectID = $doc->ProjectID;
            $doc->delete();
            $doc->destroy();
            $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Dokument gelöscht.');
            return $this->redirect($this->Link('edit/' . $projectID));
        }

        return $this->redirectBack();
    }

    // ─── Task forms ───────────────────────────────────────────────────────────────

    public function addTaskForm()
    {
        $project = $this->actProject();
        if (!$project) {
            return false;
        }

        $allMembers = Member::get()->filter('Groups.ID', '3')->sort('Surname, FirstName');

        $formFields = FieldList::create(
            HiddenField::create('ProjectID', 'Projekt')->setValue($project->ID),
            DropdownField::create('Status', 'Status', [
                0 => 'Zu erledigen',
                1 => 'In Bearbeitung',
                2 => 'Erledigt',
            ])->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-select')->setValue(0),
            TextField::create('Title', 'Titel')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextareaField::create('Description', 'Beschreibung')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control')->setRows(4),
            DateField::create('StartDate', 'Startdatum')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            DateField::create('EndDate', 'Enddatum')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextField::create('EstimatedHours', 'Geschätzte Stunden')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            CheckboxSetField::create('AssignedTo', 'Zugewiesen an', $allMembers),
        );

        $form = Form::create(
            $this, 'addTaskForm', $formFields,
            FieldList::create(FormAction::create('doAddTask', 'Speichern')->addExtraClass('btn btn-sm btn-success px-4 mb-2')),
            RequiredFieldsValidator::create(['Title', 'StartDate', 'EndDate', 'AssignedTo'])
        );
        $form->setFormAction($this->Link('task/add'));

        return $form;
    }

    public function doAddTask(array $data, Form $form)
    {
        $projectID = isset($data['ProjectID']) && is_numeric($data['ProjectID']) ? (int) $data['ProjectID'] : null;
        $project   = $projectID ? Project::get()->byID($projectID) : null;

        if ($project && $project->exists()) {
            $task            = new ProjectTask();
            $task->ProjectID = $project->ID;
            $form->saveInto($task);
            $task->write();

            $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Aufgabe erfolgreich hinzugefügt.');
            return $this->redirect($this->Link('edit/' . $project->ID . '/task/' . $task->ID . '?activetab=project_tasks'));
        }

        $this->setNotifySessionData('error', 'bi bi-x-circle', 'Fehler beim Hinzufügen der Aufgabe.');
        return $this->redirectBack();
    }

    public function editTaskForm()
    {
        $task = $this->actTask();
        if (!$task) {
            return $this->httpError(404);
        }

        $formFields = $this->addTaskForm()->Fields();
        $formFields->push(HiddenField::create('TaskID', 'ID')->setValue($task->ID));

        $cancelLink = $this->Link('edit/' . $task->ProjectID . '?activetab=project_tasks');
        $actions = FieldList::create(
            LiteralField::create('cancel', '<a href="' . $cancelLink . '" class="btn btn-sm btn-secondary px-4 mb-2 me-2">Abbrechen</a>'),
            FormAction::create('doEditTask', 'Speichern')->addExtraClass('btn btn-sm btn-success px-4 mb-2'),
        );

        $form = Form::create($this, 'editTaskForm', $formFields, $actions, RequiredFieldsValidator::create(['Title', 'StartDate', 'EndDate', 'AssignedTo']));
        $form->loadDataFrom($task);
        $form->setFormAction($this->Link('task/' . $task->ID));

        return $form;
    }

    public function doEditTask(array $data, Form $form)
    {
        $id   = isset($data['TaskID']) && is_numeric($data['TaskID']) ? (int) $data['TaskID'] : null;
        $task = $id ? ProjectTask::get()->byID($id) : null;

        if ($task && $task->exists()) {
            $form->saveInto($task);
            $task->write();

            $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Aufgabe erfolgreich gespeichert.');
            return $this->redirect($this->Link('edit/' . $task->ProjectID . '/task/' . $task->ID . '?activetab=project_tasks'));
        }

        $this->setNotifySessionData('error', 'bi bi-x-circle', 'Fehler beim Speichern der Aufgabe.');
        return $this->redirectBack();
    }

    public function deleteTask()
    {
        $taskID = $this->subResourceID();
        if (!$taskID) {
            return $this->httpError(400);
        }

        $task = ProjectTask::get()->byID($taskID);
        if ($task && $task->exists()) {
            $projectID = $task->ProjectID;
            $task->delete();
            $task->destroy();
            $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Aufgabe gelöscht.');
            return $this->redirect($this->Link('edit/' . $projectID . '?activetab=project_tasks'));
        }

        return $this->redirectBack();
    }
}
