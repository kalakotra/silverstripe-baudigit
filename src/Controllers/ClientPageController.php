<?php

declare(strict_types=1);

namespace Kalakotra\Baudigit\Controllers;

use Kalakotra\Baudigit\Models\Client;
use Kalakotra\Baudigit\Models\ClientObject;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\Validation\RequiredFieldsValidator;

class ClientPageController extends \PageController
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
        'addLocation',
        'doAddLocation',
        'editLocation',
        'doEditLocation',
    ];

    private static array $url_handlers = [
        'add'                       => 'add',
        'edit/$ID!'                 => 'edit',
        'edit/$ID!/location/$SubID' => 'edit',
        'location/add/$ID!'         => 'addLocation',   // /location/add/clientID
        'location/edit/$SubID!'     => 'editLocation',  // /location/edit/locationID
    ];

    // ─── Template helpers ─────────────────────────────────────────────────────────

    public function readAllInputs()
    {
        return Client::get();
    }

    public function readAllLocationInputs()
    {
        $client = $this->actClient();
        return $client ? $client->ClientObjects() : null;
    }

    /** True when viewing the location list panel (SubID = 0 sentinel is no longer needed; list is always shown). */
    public function checkLocationList(): bool
    {
        return (bool) $this->actClient();
    }

    // ─── Object lookup ────────────────────────────────────────────────────────────

    private function actClient(): ?Client
    {
        $id = $this->resourceID();
        if (!$id) {
            return null;
        }
        $client = Client::get()->byID($id);
        return ($client && $client->exists()) ? $client : null;
    }

    private function actLocation(): ?ClientObject
    {
        $id = $this->subResourceID();
        if (!$id) {
            return null;
        }
        $loc = ClientObject::get()->byID($id);
        return ($loc && $loc->exists()) ? $loc : null;
    }

    // ─── Client forms ─────────────────────────────────────────────────────────────

    private function clientFields(): FieldList
    {
        return FieldList::create(
            TextField::create('Company', 'Unternehmen')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextField::create('Name', 'Kontakt Person')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            EmailField::create('Email', 'Email')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextField::create('Phone', 'Telefon')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextField::create('Address', 'Adresse')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextField::create('PostalCode', 'Postleitzahl')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextField::create('City', 'Stadt')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
        );
    }

    public function add()
    {
        $form = Form::create(
            $this, 'add',
            $this->clientFields(),
            FieldList::create(FormAction::create('doAdd', 'Hinzufügen')->addExtraClass('btn btn-sm btn-success px-4')),
            RequiredFieldsValidator::create(['Company'])
        );
        $form->setFormAction($this->Link('add'));
        return $form;
    }

    public function doAdd(array $data, Form $form)
    {
        $client = Client::create();
        $form->saveInto($client);
        $client->write();

        $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Kunde erfolgreich gespeichert.');
        return $this->redirect($this->Link('edit/' . $client->ID));
    }

    public function edit()
    {
        $client = $this->actClient();
        if (!$client) {
            return $this->httpError(404);
        }

        $fields = $this->clientFields();
        $fields->push(HiddenField::create('ClientID', 'ID')->setValue($client->ID));

        $form = Form::create(
            $this, 'edit', $fields,
            FieldList::create(FormAction::create('doEdit', 'Speichern')->addExtraClass('btn btn-sm btn-success px-4')),
            RequiredFieldsValidator::create(['Company'])
        );
        $form->loadDataFrom($client);
        $form->setFormAction($this->Link('edit/' . $client->ID));

        return $form;
    }

    public function doEdit(array $data, Form $form)
    {
        $client = $this->actClient();
        if ($client) {
            $form->saveInto($client);
            $client->write();

            $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Kunde erfolgreich gespeichert.');
            return $this->redirectBack();
        }

        $this->setNotifySessionData('error', 'bi bi-x-circle', 'Fehler beim Speichern des Kunden.');
        return $this->redirectBack();
    }

    // ─── Location forms ───────────────────────────────────────────────────────────

    private function locationFields(int $clientID): FieldList
    {
        return FieldList::create(
            HiddenField::create('ClientID', 'Kunden ID')->setValue($clientID),
            TextField::create('Name', 'Standort Name')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextField::create('Address', 'Adresse')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextField::create('PostalCode', 'Postleitzahl')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextField::create('City', 'Stadt')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextField::create('ContactPerson', 'Ansprechpartner')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextField::create('ContactTelephone', 'Telefon')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            EmailField::create('ContactEmail', 'Email')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextareaField::create('Description', 'Beschreibung')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
        );
    }

    public function addLocation()
    {
        // clientID comes from the URL segment: /location/add/$ID
        $clientID = $this->getRequest()->param('ID');
        $clientID = ($clientID && is_numeric($clientID)) ? (int) $clientID : null;

        $client = $clientID ? Client::get()->byID($clientID) : null;
        if (!$client || !$client->exists()) {
            return $this->httpError(404);
        }

        $form = Form::create(
            $this, 'addLocation',
            $this->locationFields($client->ID),
            FieldList::create(FormAction::create('doAddLocation', 'Hinzufügen')->addExtraClass('btn btn-sm btn-success px-4')),
            RequiredFieldsValidator::create(['Name', 'Address', 'PostalCode', 'City'])
        );
        $form->setFormAction($this->Link('location/add/' . $client->ID));

        return $form;
    }

    public function doAddLocation(array $data, Form $form)
    {
        $clientID = isset($data['ClientID']) && is_numeric($data['ClientID']) ? (int) $data['ClientID'] : null;
        $client   = $clientID ? Client::get()->byID($clientID) : null;

        if ($client && $client->exists()) {
            $location           = ClientObject::create();
            $location->ClientID = $client->ID;
            $form->saveInto($location);
            $location->write();

            $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Standort erfolgreich gespeichert.');
            return $this->redirect($this->Link('edit/' . $client->ID . '/location/' . $location->ID));
        }

        $this->setNotifySessionData('error', 'bi bi-x-circle', 'Fehler beim Speichern des Standorts.');
        return $this->redirectBack();
    }

    public function editLocation()
    {
        $location = $this->actLocation();
        if (!$location) {
            return $this->httpError(404);
        }

        $fields = $this->locationFields($location->ClientID);
        $fields->push(HiddenField::create('LocationID', 'Standort ID')->setValue($location->ID));

        $form = Form::create(
            $this, 'editLocation', $fields,
            FieldList::create(FormAction::create('doEditLocation', 'Speichern')->addExtraClass('btn btn-sm btn-success px-4')),
            RequiredFieldsValidator::create(['Name', 'Address', 'PostalCode', 'City'])
        );
        $form->loadDataFrom($location);
        $form->setFormAction($this->Link('location/edit/' . $location->ID));

        return $form;
    }

    public function doEditLocation(array $data, Form $form)
    {
        $id       = isset($data['LocationID']) && is_numeric($data['LocationID']) ? (int) $data['LocationID'] : null;
        $location = $id ? ClientObject::get()->byID($id) : null;

        if ($location && $location->exists()) {
            $form->saveInto($location);
            $location->write();

            $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Standort erfolgreich gespeichert.');
            return $this->redirect($this->Link('edit/' . $location->ClientID . '/location/' . $location->ID));
        }

        $this->setNotifySessionData('error', 'bi bi-x-circle', 'Fehler beim Speichern des Standorts.');
        return $this->redirectBack();
    }

    // ─── Template getters ─────────────────────────────────────────────────────────

    public function getCurrentClient(): ?Client
    {
        return $this->actClient();
    }

    public function getCurrentLocation(): ?ClientObject
    {
        return $this->actLocation();
    }
}
