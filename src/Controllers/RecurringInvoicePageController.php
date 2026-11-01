<?php

declare(strict_types=1);

namespace Kalakotra\Baudigit\Controllers;

use Kalakotra\Baudigit\Models\Client;
use Kalakotra\Baudigit\Models\Article;
use Kalakotra\Baudigit\Models\Invoice;
use Kalakotra\Baudigit\Pages\InvoicePage;
use Kalakotra\Baudigit\Models\ClientObject;
use SilverStripe\Forms\Form;
use Kalakotra\Baudigit\Models\InvoiceArticle;
use Kalakotra\Baudigit\Models\RecurringInvoice;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\Validation\RequiredFieldsValidator;
use Kalakotra\Baudigit\Models\RecurringInvoiceArticle;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Forms\GroupedDropdownField;

class RecurringInvoicePageController extends \PageController
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
        'addArticleForm',
        'doAddArticle',
        'editArticleForm',
        'doEditArticle',
        'deleteArticle',
        'previewPDF',
        'send',
        'toInvoice',
    ];

    private static array $url_handlers = [
        'add'                           => 'add',
        'edit/$ID!'                     => 'edit',
        'edit/$ID!/article/$SubID'      => 'edit',
        'change-status/$ID!/$Status!'   => 'changeStatus',
        'article/add'                   => 'addArticleForm',
        'article/$SubID!'               => 'editArticleForm',
        'article/delete/$SubID!'        => 'deleteArticle',
        'preview/$ID!'                  => 'previewPDF',
        'send/$ID!'                     => 'send',
        'to-invoice/$ID!'               => 'toInvoice',
    ];

    // ─── Object lookup ────────────────────────────────────────────────────────────

    private function actRecurring(): ?RecurringInvoice
    {
        $id = $this->resourceID();
        if (!$id) {
            return null;
        }
        $ri = RecurringInvoice::get()->byID($id);
        return ($ri && $ri->exists()) ? $ri : null;
    }

    private function actArticle(): ?RecurringInvoiceArticle
    {
        $id = $this->subResourceID();
        if (!$id) {
            return null;
        }
        $article = RecurringInvoiceArticle::get()->byID($id);
        return ($article && $article->exists()) ? $article : null;
    }

    // ─── Template helpers ─────────────────────────────────────────────────────────

    public function readAllInputs()
    {
        $showOnlyStatus = $this->data()->ShowOnlyStatus;
        $statusValue    = $this->data()->StatusValue;
        if ($showOnlyStatus) {
            return RecurringInvoice::get()->filter('Status', $statusValue);
        }
        return RecurringInvoice::get();
    }

    public function getAllArticles()
    {
        return Article::get();
    }

    public function getAllOfferArticles()
    {
        $ri = $this->actRecurring();
        return $ri ? $ri->Articles() : false;
    }

    public function getCurrentRecurringInvoice(): ?RecurringInvoice
    {
        return $this->actRecurring();
    }

    public function getCurrentRecurringArticle(): ?RecurringInvoiceArticle
    {
        return $this->actArticle();
    }

    public function getSentStatus(): ?string
    {
        return $this->getRequest()->getVar('sent');
    }

    // ─── Recurring invoice add/edit ───────────────────────────────────────────────

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

    public function add()
    {
        $config     = SiteConfig::current_site_config();
        $validUntil = date('Y-m-d', strtotime('+365 days'));

        $formFields = FieldList::create(
            DateField::create('StartDate', 'Startdatum', date('Y-m-d'))->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-control'),
            DateField::create('ValidUntil', 'Aktiv bis', $validUntil)->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-control'),
            DropdownField::create('InvoiceInterval', 'Rechnungsintervall', [
                0 => 'Monatlich',
                1 => 'Vierteljährlich',
                2 => 'Halbjährlich',
                3 => 'Jährlich',
            ])->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-select'),
            GroupedDropdownField::create('ClientObjectID', 'Kunde', $this->buildClientGroupedList())->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-select select2me')->setEmptyString('-- bitte wählen --'),
            TextareaField::create('IntroText', 'Einleitungstext', $config->OfferIntroText)->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-control'),
            TextareaField::create('OutroText', 'Schlusstext', $config->OfferOutroText)->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-control'),
        );

        $form = Form::create(
            $this, 'add', $formFields,
            FieldList::create(FormAction::create('doAdd', 'Speichern')->addExtraClass('btn btn-sm btn-success px-4 mb-2')),
            RequiredFieldsValidator::create(['ClientObjectID'])
        );
        $form->setFormAction($this->Link('add'));

        return $form;
    }

    public function doAdd(array $data, Form $form)
    {
        $ri = new RecurringInvoice();
        $form->saveInto($ri);
        $ri->write();

        $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Wiederkehrende Rechnung erfolgreich erstellt.');
        return $this->redirect($this->Link('edit/' . $ri->ID));
    }

    public function edit()
    {
        $ri = $this->actRecurring();
        if (!$ri) {
            return $this->httpError(404);
        }

        $fields = $this->add()->Fields();
        $fields->push(HiddenField::create('RecurringInvoiceID', 'ID')->setValue($ri->ID));

        $form = Form::create(
            $this, 'edit', $fields,
            FieldList::create(FormAction::create('doEdit', 'Speichern')->addExtraClass('btn btn-sm btn-success px-4 mb-2')),
            RequiredFieldsValidator::create(['ClientObjectID'])
        );
        $form->loadDataFrom($ri);
        $form->setFormAction($this->Link('edit/' . $ri->ID));

        return $form;
    }

    public function doEdit(array $data, Form $form)
    {
        $ri = $this->actRecurring();
        if ($ri) {
            $form->saveInto($ri);
            $ri->write();

            $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Wiederkehrende Rechnung erfolgreich gespeichert.');
            return $this->redirectBack();
        }

        $this->setNotifySessionData('error', 'bi bi-x-circle', 'Fehler beim Speichern der wiederkehrenden Rechnung.');
        return $this->redirectBack();
    }

    public function changeStatus()
    {
        $ri     = $this->actRecurring();
        $status = $this->getRequest()->param('Status');

        if ($ri && is_numeric($status)) {
            $ri->Status = (int) $status;
            $ri->write();

            $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Rechnungsstatus erfolgreich geändert.');
            return $this->redirectBack();
        }

        $this->setNotifySessionData('danger', 'bi bi-exclamation-triangle', 'Fehler beim Ändern des Rechnungsstatus.');
        return $this->redirectBack();
    }

    // ─── Article forms ────────────────────────────────────────────────────────────

    public function addArticleForm()
    {
        $config = SiteConfig::current_site_config();
        $ri     = $this->actRecurring();

        $formFields = FieldList::create(
            NumericField::create('Quantity', 'Menge', 1)->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control')->setAttribute('type', 'number')->setAttribute('min', '1')->setAttribute('step', '1'),
            NumericField::create('Discount', 'Rabatt (%)', '0')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control')->setScale(2),
            HiddenField::create('RecurringInvoiceID', 'Wiederkehrende Rechnung')->setValue($ri ? $ri->ID : null),
            TextField::create('Title', 'Titel')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control')->setAttribute('list', 'articleList')->setAttribute('autocomplete', 'off'),
            DropdownField::create('Unit', '', Article::singleton()->getUnitOptions())->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-select'),
            HiddenField::create('BuyPriceNetto', 'Einkaufspreis netto'),
            NumericField::create('SellPriceNetto', 'Preis (netto)')->setScale(2)->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            NumericField::create('TaxRate', 'MwSt. (%)', $config->InvoiceTax)->setScale(2)->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control')->setReadonly(true),
            TextareaField::create('Description', 'Beschreibung')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control')->setRows(2),
            HiddenField::create('SourceArticleID'),
            NumericField::create('FinalPrice', 'Betrag', '0,00')->setScale(2)->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control')->setReadonly(true),
        );

        $form = Form::create(
            $this, 'addArticleForm', $formFields,
            FieldList::create(FormAction::create('doAddArticle', 'Hinzufügen')->addExtraClass('btn btn-sm btn-success px-4 mb-2')),
            RequiredFieldsValidator::create(['Quantity', 'Title', 'SellPriceNetto'])
        );
        $form->setTemplate('RecurringInvoiceArticleForm');
        $form->setFormAction($this->Link('article/add'));

        return $form;
    }

    public function doAddArticle(array $data, Form $form)
    {
        $riID = isset($data['RecurringInvoiceID']) && is_numeric($data['RecurringInvoiceID']) ? (int) $data['RecurringInvoiceID'] : null;
        $ri   = $riID ? RecurringInvoice::get()->byID($riID) : null;

        if ($ri && $ri->exists()) {
            $article                      = RecurringInvoiceArticle::create();
            $form->saveInto($article);
            $article->RecurringInvoiceID  = $ri->ID;
            $article->write();

            $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Artikel erfolgreich hinzugefügt.');
            return $this->redirect($this->Link('edit/' . $ri->ID . '?activetab=offer_article'));
        }

        $this->setNotifySessionData('error', 'bi bi-x-circle', 'Fehler beim Hinzufügen des Artikels.');
        return $this->redirectBack();
    }

    public function editArticleForm()
    {
        $article = $this->actArticle();
        if (!$article) {
            return $this->httpError(404);
        }

        $formFields = $this->addArticleForm()->Fields();
        $formFields->push(HiddenField::create('ArticleRecordID', 'ID')->setValue($article->ID));
        $formFields->fieldByName('RecurringInvoiceID')?->setValue($article->RecurringInvoiceID);

        $form = Form::create(
            $this, 'editArticleForm', $formFields,
            FieldList::create(FormAction::create('doEditArticle', 'Speichern')->addExtraClass('btn btn-sm btn-success px-4 mb-2')),
            RequiredFieldsValidator::create(['Quantity', 'Title', 'SellPriceNetto'])
        );
        $form->setTemplate('RecurringInvoiceArticleForm');
        $form->loadDataFrom($article);
        $form->setFormAction($this->Link('article/' . $article->ID));

        return $form;
    }

    public function doEditArticle(array $data, Form $form)
    {
        $id      = isset($data['ArticleRecordID']) && is_numeric($data['ArticleRecordID']) ? (int) $data['ArticleRecordID'] : null;
        $article = $id ? RecurringInvoiceArticle::get()->byID($id) : null;

        if ($article && $article->exists()) {
            $form->saveInto($article);
            $article->write();

            $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Artikel erfolgreich gespeichert.');
            return $this->redirect($this->Link('edit/' . $article->RecurringInvoiceID . '?activetab=offer_article'));
        }

        $this->setNotifySessionData('error', 'bi bi-x-circle', 'Fehler beim Speichern des Artikels.');
        return $this->redirectBack();
    }

    public function deleteArticle()
    {
        $articleID = $this->subResourceID();
        if (!$articleID) {
            return $this->httpError(400);
        }

        $article = RecurringInvoiceArticle::get()->byID($articleID);
        if ($article && $article->exists()) {
            $riID = $article->RecurringInvoiceID;
            $article->delete();
            $article->destroy();
            return $this->redirect($this->Link('edit/' . $riID . '?activetab=offer_article'));
        }

        return $this->redirectBack();
    }

    // ─── PDF / send / convert ─────────────────────────────────────────────────────

    public function previewPDF()
    {
        $ri = $this->actRecurring();
        if ($ri) {
            return $ri->getPDF();
        }
        return $this->httpError(404);
    }

    public function send()
    {
        $ri = $this->actRecurring();
        if ($ri) {
            if ($ri->sendOfferEmail()) {
                $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Wiederkehrende Rechnung erfolgreich gesendet.');
                return $this->redirect($this->Link('edit/' . $ri->ID . '?sent=1'));
            }
            $this->setNotifySessionData('error', 'bi bi-x-circle', 'Fehler beim Senden der wiederkehrenden Rechnung.');
            return $this->redirect($this->Link('edit/' . $ri->ID . '?sent=0'));
        }
        return $this->redirectBack();
    }

    public function toInvoice()
    {
        $ri = $this->actRecurring();
        if (!$ri) {
            return $this->redirectBack();
        }

        $lastInvoice         = Invoice::get()->sort('Created', 'DESC')->first();
        $invoice             = new Invoice();
        $invoice->ClientObjectID = $ri->ClientObjectID;
        $invoice->IntroText  = $ri->IntroText;
        $invoice->OutroText  = $ri->OutroText;
        $invoice->InvoiceNumber = $lastInvoice ? $lastInvoice->getNextInvoiceNumber() : date('Y') . '-0001';
        $invoice->DueDate    = date('Y-m-d', strtotime('+14 days'));
        $invoice->write();

        foreach ($ri->Articles() as $ra) {
            $ia                  = new InvoiceArticle();
            $ia->InvoiceID       = $invoice->ID;
            $ia->Title           = $ra->Title;
            $ia->Description     = $ra->Description;
            $ia->Quantity        = $ra->Quantity;
            $ia->Unit            = $ra->Unit;
            $ia->SellPriceNetto  = $ra->SellPriceNetto;
            $ia->Discount        = $ra->Discount;
            $ia->TaxRate         = $ra->TaxRate;
            $ia->FinalPrice      = $ra->FinalPrice;
            $ia->SourceArticleID = $ra->SourceArticleID;
            $ia->write();
        }

        $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Rechnung erfolgreich erstellt.');

        $invoicePage = InvoicePage::get()->first();
        if ($invoicePage) {
            return $this->redirect($invoicePage->Link('edit/' . $invoice->ID));
        }
        return $this->redirect($this->Link('edit/' . $ri->ID));
    }
}
