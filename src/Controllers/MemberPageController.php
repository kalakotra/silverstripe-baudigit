<?php

declare(strict_types=1);

namespace Kalakotra\Baudigit\Controllers;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\PasswordField;
use SilverStripe\Forms\Validation\RequiredFieldsValidator;
use SilverStripe\Security\Member;
use SilverStripe\Security\Group;
use SilverStripe\Security\Security;

class MemberPageController extends \PageController
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
    ];

    private static array $url_handlers = [
        'add'       => 'add',
        'edit/$ID!' => 'edit',
    ];

    // ─── Template helpers ─────────────────────────────────────────────────────────

    public function readAllInputs()
    {
        $members     = Member::get()->filter('Groups.ID', [1, 3]);
        $currentUser = Security::getCurrentUser();

        if ($currentUser) {
            $members = $members->exclude('ID', $currentUser->ID);
        }

        return $members;
    }

    public function getGroupOptions(): array
    {
        return [
            1 => 'Administrator',
            3 => 'Arbeiter',
        ];
    }

    public function getMemberGroupName(Member $member): string
    {
        $groups = $member->Groups()->filter('ID', [1, 3]);
        if ($groups->count() > 0) {
            return match ((int) $groups->first()->ID) {
                1       => 'Administrator',
                3       => 'Arbeiter',
                default => 'Unbekannt',
            };
        }
        return 'Keine Gruppe';
    }

    // ─── Object lookup ────────────────────────────────────────────────────────────

    private function actMember(): ?Member
    {
        $id = $this->resourceID();
        if (!$id) {
            return null;
        }
        $member = Member::get()->byID($id);
        return ($member && $member->exists()) ? $member : null;
    }

    // ─── Forms ───────────────────────────────────────────────────────────────────

    public function add()
    {
        $formFields = FieldList::create(
            CheckboxField::create('Active', 'Aktiv')->setFieldHolderTemplate('FormField_customCheckbox')->addExtraClass('form-check-input'),
            TextField::create('FirstName', 'Vorname')->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-control'),
            TextField::create('Surname', 'Nachname')->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-control'),
            EmailField::create('Email', 'E-Mail')->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-control'),
            PasswordField::create('Password', 'Passwort')->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-control')->setAttribute('autocomplete', 'new-password'),
            PasswordField::create('PasswordConfirm', 'Passwort bestätigen')->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-control')->setAttribute('autocomplete', 'new-password'),
            DropdownField::create('GroupID', 'Benutzergruppe', $this->getGroupOptions())->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-select')->setEmptyString('-- bitte wählen --'),
        );

        $form = Form::create(
            $this, 'add', $formFields,
            FieldList::create(FormAction::create('doAdd', 'Speichern')->addExtraClass('btn btn-sm btn-success px-4 mb-2')),
            RequiredFieldsValidator::create(['FirstName', 'Surname', 'Email', 'Password', 'PasswordConfirm', 'GroupID'])
        );
        $form->setFormAction($this->Link('add'));

        return $form;
    }

    public function doAdd(array $data, Form $form)
    {
        if ($data['Password'] !== $data['PasswordConfirm']) {
            $this->setNotifySessionData('bad', 'bi bi-exclamation-triangle', 'Die Passwörter stimmen nicht überein.');
            return $this->redirectBack();
        }

        if (Member::get()->filter('Email', $data['Email'])->exists()) {
            $this->setNotifySessionData('bad', 'bi bi-exclamation-triangle', 'Ein Benutzer mit dieser E-Mail-Adresse existiert bereits.');
            return $this->redirectBack();
        }

        $member            = new Member();
        $member->FirstName = $data['FirstName'];
        $member->Surname   = $data['Surname'];
        $member->Email     = $data['Email'];
        $member->Active    = !empty($data['Active']);
        $member->changePassword($data['Password']);
        $member->write();

        if (isset($data['GroupID']) && is_numeric($data['GroupID'])) {
            $group = Group::get()->byID((int) $data['GroupID']);
            if ($group) {
                $member->Groups()->add($group);
            }
        }

        $this->setNotifySessionData('good', 'bi bi-check-circle', 'Benutzer erfolgreich erstellt.');
        return $this->redirect($this->Link('edit/' . $member->ID));
    }

    public function edit()
    {
        $member = $this->actMember();
        if (!$member) {
            return $this->httpError(404);
        }

        $formFields = $this->add()->Fields();
        $formFields->push(HiddenField::create('MemberID', 'ID')->setValue($member->ID));

        // Pre-select current group
        $currentGroupID = null;
        $memberGroups   = $member->Groups()->filter('ID', [1, 3]);
        if ($memberGroups->count() > 0) {
            $currentGroupID = $memberGroups->first()->ID;
        }
        $formFields->fieldByName('GroupID')?->setValue($currentGroupID);

        $form = Form::create(
            $this, 'edit', $formFields,
            FieldList::create(FormAction::create('doEdit', 'Speichern')->addExtraClass('btn btn-sm btn-success px-4 mb-2')),
            RequiredFieldsValidator::create(['FirstName', 'Surname', 'Email', 'GroupID'])
        );
        $form->loadDataFrom($member);
        $form->setFormAction($this->Link('edit/' . $member->ID));

        return $form;
    }

    public function doEdit(array $data, Form $form)
    {
        $id     = isset($data['MemberID']) && is_numeric($data['MemberID']) ? (int) $data['MemberID'] : null;
        $member = $id ? Member::get()->byID($id) : null;

        if (!$member || !$member->exists()) {
            $this->setNotifySessionData('bad', 'bi bi-exclamation-triangle', 'Fehler beim Speichern des Benutzers.');
            return $this->redirectBack();
        }

        if (!empty($data['Password']) && $data['Password'] !== $data['PasswordConfirm']) {
            $this->setNotifySessionData('bad', 'bi bi-exclamation-triangle', 'Die Passwörter stimmen nicht überein.');
            return $this->redirectBack();
        }

        if (Member::get()->filter('Email', $data['Email'])->exclude('ID', $member->ID)->exists()) {
            $this->setNotifySessionData('bad', 'bi bi-exclamation-triangle', 'Ein anderer Benutzer mit dieser E-Mail-Adresse existiert bereits.');
            return $this->redirectBack();
        }

        $member->FirstName = $data['FirstName'];
        $member->Surname   = $data['Surname'];
        $member->Email     = $data['Email'];
        $member->Active    = !empty($data['Active']);

        if (!empty($data['Password'])) {
            $member->changePassword($data['Password']);
        }
        $member->write();

        if (isset($data['GroupID']) && is_numeric($data['GroupID'])) {
            // Remove from all admin/worker groups then add the chosen one
            foreach ($member->Groups()->filter('ID', [1, 3]) as $g) {
                $member->Groups()->remove($g);
            }
            $newGroup = Group::get()->byID((int) $data['GroupID']);
            if ($newGroup) {
                $member->Groups()->add($newGroup);
            }
        }

        $this->setNotifySessionData('good', 'bi bi-check-circle', 'Benutzer erfolgreich gespeichert.');
        return $this->redirectBack();
    }

    // ─── Template getters ─────────────────────────────────────────────────────────

    public function getCurrentMemberRecord(): ?Member
    {
        return $this->actMember();
    }
}
