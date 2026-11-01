<?php

namespace Kalakotra\Baudigit\Controllers;

use Dom\Text;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\Validation\RequiredFieldsValidator;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

class SettingsPageController extends \PageController
{
    use \Kalakotra\Baudigit\Traits\FeatureToggleTrait;

    protected function init(): void
    {
        parent::init();
        if ($err = $this->checkEnabled()) {
            return;
        }
    }
    private static $allowed_actions = [
        'companySettingsForm',
        'invoiceSettingsForm',
        'textSettingsForm',
    ];

    public function companySettingsForm()
    {

        
        $fields = FieldList::create(
            LiteralField::create('formGrid1', '<div class="row"><div class="col-12">'),
                TextField::create('CompanyName', 'Firmenname')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            LiteralField::create('formGrid2', '</div><div class="col-12 col-lg-4">'),
                TextField::create('CompanyAddressLine1', 'Adresse Zeile 1')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            LiteralField::create('formGrid3', '</div><div class="col-12 col-lg-4">'),
                TextField::create('CompanyAddressLine2', 'Adresse Zeile 2')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            LiteralField::create('formGrid4', '</div><div class="col-12 col-lg-4">'),
                TextField::create('CompanyAddressLine3', 'Adresse Zeile 3')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            LiteralField::create('formGrid5', '</div><div class="col-12 col-lg-2">'),
                TextField::create('CompanyPostalCode', 'PLZ')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            LiteralField::create('formGrid6', '</div><div class="col-12 col-lg-4">'),
                TextField::create('CompanyCity', 'Ort')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            LiteralField::create('formGrid7', '</div><div class="col-12"><hr />'),
                TextField::create('CompanyContactDepartment', 'Abteilung')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            LiteralField::create('formGrid8', '</div><div class="col-12 col-lg-4">'),
                TextField::create('CompanyContactPhone', 'Telefon')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            LiteralField::create('formGrid9', '</div><div class="col-12 col-lg-4">'),
                TextField::create('CompanyContactEmail', 'Email')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            LiteralField::create('formGrid10', '</div><div class="col-12 col-lg-4">'),
                TextField::create('CompanyContactWebsite', 'Webseite')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            LiteralField::create('formGrid11', '</div><div class="col-12"><hr /></div><div class="col-12 col-lg-4">'),
                TextField::create('CompanyVATId', 'USt-IdNr')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            LiteralField::create('formGrid12', '</div><div class="col-12 col-lg-4">'),
                TextField::create('CompanyTaxNumber', 'Steuernummer')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            LiteralField::create('formGrid13', '</div><div class="col-12 col-lg-4">'),
                TextField::create('CompanyContactPerson', 'Inhaber / Kontaktperson')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            LiteralField::create('formGrid14', '</div></div>'),
        );

        $required = RequiredFieldsValidator::create([
            'CompanyName',
            'CompanyAddressLine1',
            'CompanyPostalCode',
            'CompanyCity',
            'CompanyCountry',
            'CompanyContactEmail'
        ]);

        $action = FieldList::create(
            FormAction::create('doSaveCompanySettings', 'Speichern')->addExtraClass('btn btn-sm btn-success')
        );

        $form = Form::create($this, 'companySettingsForm', $fields, $action, $required);

        $siteConfig = SiteConfig::current_site_config();
        $form->loadDataFrom($siteConfig);

        return $form;
    }

    public function doSaveCompanySettings($data, $form)
    {
        $siteConfig = SiteConfig::current_site_config();
        $form->saveInto($siteConfig);
        $siteConfig->write();

        $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Firmendaten erfolgreich gespeichert.');
        return $this->redirectBack();
        //return $this->redirect($this->Link('&activetab=offer_article'));
    }

    public function invoiceSettingsForm()
    {
        $fields = FieldList::create(
            TextField::create('PaymentTermDays', 'Zahlungsziel (Tage)')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            //TextField::create('PaymentTermText', 'Zahlungsziel (Text)')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextField::create('PaymentIBAN', 'IBAN')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextField::create('PaymentBIC', 'BIC')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextField::create('PaymentAccountHolder', 'Kontoinhaber')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
        );

        $required = RequiredFieldsValidator::create([
            'PaymentTermDays',
            'PaymentIBAN',
            'PaymentBIC',
            'PaymentAccountHolder'
        ]);

        $action = FieldList::create(
            FormAction::create('doSaveInvoiceSettings', 'Speichern')->addExtraClass('btn btn-sm btn-success')
        );

        $form = Form::create($this, 'invoiceSettingsForm', $fields, $action, $required);

        $siteConfig = SiteConfig::current_site_config();
        $form->loadDataFrom($siteConfig);

        return $form;
    }

    public function doSaveInvoiceSettings($data, $form)
    {
        $siteConfig = SiteConfig::current_site_config();
        $form->saveInto($siteConfig);
        $siteConfig->write();

        $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Rechnungseinstellungen erfolgreich gespeichert.');
        return $this->redirect($this->Link('?activetab=settings_invoices'));
    }

    public function textSettingsForm()
    {
        /*
        'OfferIntroText' => 'Text', // Standard Einleitungstext für Angebote
        'OfferOutroText' => 'Text', // Standard Schlusstext für Angebote
        'InvoiceIntroText' => 'Text', // Standard Einleitungstext für Rechnungen
        'InvoiceOutroText' => 'Text', // Standard Schlusstext für Rechnungen

        'EmailOfferText' => 'HTMLText', // Standard Text für Angebots-E-Mail
        'EmailInvoiceText' => 'HTMLText', // Standard Text für <Rechnungs-E-Mail>
        // */
        $fields = FieldList::create(
            TextareaField::create('OfferIntroText', 'Angebot Einleitungstext')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextareaField::create('OfferOutroText', 'Angebot Schlusstext')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextareaField::create('InvoiceIntroText', 'Rechnung Einleitungstext')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextareaField::create('InvoiceOutroText', 'Rechnung Schlusstext')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextareaField::create('EmailOfferText', 'Angebots E-Mail Text')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            TextareaField::create('EmailInvoiceText', 'Rechnungs E-Mail Text')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
        );

        $required = RequiredFieldsValidator::create([
            'OfferIntroText',
            'OfferOutroText',
            'InvoiceIntroText',
            'InvoiceOutroText',
            'EmailOfferText',
            'EmailInvoiceText',
        ]);

        $action = FieldList::create(
            FormAction::create('doSaveTextSettings', 'Speichern')->addExtraClass('btn btn-sm btn-success')
        );

        $form = Form::create($this, 'textSettingsForm', $fields, $action, $required);

        $siteConfig = SiteConfig::current_site_config();
        $form->loadDataFrom($siteConfig);

        return $form;
    }

    public function doSaveTextSettings($data, $form)
    {
        $siteConfig = SiteConfig::current_site_config();
        $form->saveInto($siteConfig);
        $siteConfig->write();

        $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Textvorlagen erfolgreich gespeichert.');
        return $this->redirect($this->Link('?activetab=settings_texts'));
    }
}