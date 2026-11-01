<?php

declare(strict_types=1);

namespace Kalakotra\Baudigit\Controllers;

use Kalakotra\Baudigit\Models\Offer;
use Kalakotra\Baudigit\Models\Client;
use Kalakotra\Baudigit\Models\Article;
use Kalakotra\Baudigit\Models\Invoice;
use Kalakotra\Baudigit\Pages\InvoicePage;
use Kalakotra\Baudigit\Pages\ProjectPage;
use Kalakotra\Baudigit\Models\ClientObject;
use Kalakotra\Baudigit\Models\OfferArticle;
use Kalakotra\Baudigit\Models\InvoiceArticle;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\Validation\RequiredFieldsValidator;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Forms\GroupedDropdownField;

class OfferPageController extends \PageController
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

    private function actOffer(): ?Offer
    {
        $id = $this->resourceID();
        if (!$id) {
            return null;
        }
        $offer = Offer::get()->byID($id);
        return ($offer && $offer->exists()) ? $offer : null;
    }

    private function actArticle(): ?OfferArticle
    {
        $id = $this->subResourceID();
        if (!$id) {
            return null;
        }
        $article = OfferArticle::get()->byID($id);
        return ($article && $article->exists()) ? $article : null;
    }

    // ─── Template helpers ─────────────────────────────────────────────────────────

    public function checkProjectModule(): bool
    {
        return ProjectPage::get()->exists();
    }

    public function readAllInputs()
    {
        $showOnlyStatus = $this->data()->ShowOnlyStatus;
        $statusValue    = $this->data()->StatusValue;
        if ($showOnlyStatus) {
            return Offer::get()->filter('Status', $statusValue);
        }
        return Offer::get();
    }

    public function getAllArticles()
    {
        return Article::get();
    }

    public function getAllOfferArticles()
    {
        $offer = $this->actOffer();
        return $offer ? $offer->OfferArticles() : false;
    }

    public function getCurrentOffer(): ?Offer
    {
        return $this->actOffer();
    }

    public function getCurrentOfferArticle(): ?OfferArticle
    {
        return $this->actArticle();
    }

    // ─── Offer add/edit ───────────────────────────────────────────────────────────

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
        $lastOffer       = Offer::get()->sort('Created', 'DESC')->first();
        $nextOfferNumber = $lastOffer ? $lastOffer->getNextOfferNumber() : date('Y') . '-0001';
        $config          = SiteConfig::current_site_config();
        $validUntil      = date('Y-m-d', strtotime('+30 days'));

        $formFields = FieldList::create(
            TextField::create('OfferNumber', 'Angebotsnummer', $nextOfferNumber)->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-control')->setReadonly(true),
            DateField::create('ValidUntil', 'Gültig bis', $validUntil)->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-control'),
            GroupedDropdownField::create('ClientObjectID', 'Kunde', $this->buildClientGroupedList())->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-select select2me')->setEmptyString('-- bitte wählen --'),
            TextareaField::create('IntroText', 'Einleitungstext', $config->OfferIntroText)->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-control'),
            TextareaField::create('OutroText', 'Schlusstext', $config->OfferOutroText)->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-control'),
        );

        $form = Form::create(
            $this, 'add', $formFields,
            FieldList::create(FormAction::create('doAdd', 'Speichern')->addExtraClass('btn btn-sm btn-success px-4 mb-2')),
            RequiredFieldsValidator::create(['OfferNumber', 'ClientObjectID'])
        );
        $form->setFormAction($this->Link('add'));

        return $form;
    }

    public function doAdd(array $data, Form $form)
    {
        $offer = new Offer();
        $form->saveInto($offer);
        $offer->write();

        $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Angebot erfolgreich erstellt.');
        return $this->redirect($this->Link('edit/' . $offer->ID));
    }

    public function edit()
    {
        $offer = $this->actOffer();
        if (!$offer) {
            return $this->httpError(404);
        }

        $fields = $this->add()->Fields();
        $fields->push(HiddenField::create('OfferID', 'ID')->setValue($offer->ID));

        $form = Form::create(
            $this, 'edit', $fields,
            FieldList::create(FormAction::create('doEdit', 'Speichern')->addExtraClass('btn btn-sm btn-success px-4 mb-2')),
            RequiredFieldsValidator::create(['OfferNumber', 'ClientObjectID'])
        );
        $form->loadDataFrom($offer);
        $form->setFormAction($this->Link('edit/' . $offer->ID));

        return $form;
    }

    public function doEdit(array $data, Form $form)
    {
        $offer = $this->actOffer();
        if ($offer) {
            $form->saveInto($offer);
            $offer->write();

            $this->setNotifySessionData('success', 'bi bi-check2-circle', _t('Kalakotra\\Baudigit\\Controllers\\OfferPageController.OFFER_SAVED', 'Angebot erfolgreich gespeichert.'));
            return $this->redirectBack();
        }

        $this->setNotifySessionData('error', 'bi bi-x-circle', _t('Kalakotra\\Baudigit\\Controllers\\OfferPageController.OFFER_SAVE_ERROR', 'Fehler beim Speichern des Angebots.'));
        return $this->redirectBack();
    }

    public function changeStatus()
    {
        $offer  = $this->actOffer();
        $status = $this->getRequest()->param('Status');

        if ($offer && is_numeric($status)) {
            $offer->Status = (int) $status;
            $offer->write();

            $this->setNotifySessionData('success', 'bi bi-check2-circle', _t('Kalakotra\\Baudigit\\Controllers\\OfferPageController.STATUS_CHANGED', 'Angebotsstatus erfolgreich geändert.'));
            return $this->redirectBack();
        }

        $this->setNotifySessionData('danger', 'bi bi-exclamation-triangle', 'Fehler beim Ändern des Angebotsstatus.');
        return $this->redirectBack();
    }

    // ─── Article forms ────────────────────────────────────────────────────────────

    public function addArticleForm()
    {
        $config = SiteConfig::current_site_config();

        $formFields = FieldList::create(
            NumericField::create('Quantity', 'Menge', 1)->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control')->setAttribute('type', 'number')->setAttribute('min', '1')->setAttribute('step', '1')->setValue(1),
            NumericField::create('Discount', 'Rabatt (%)', '0')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control')->setScale(2),
            HiddenField::create('OfferID', 'Angebot'),
            TextField::create('Title', 'Titel')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control')->setAttribute('list', 'articleList')->setAttribute('autocomplete', 'off'),
            DropdownField::create('Unit', '', Article::singleton()->getUnitOptions())->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-select'),
            HiddenField::create('BuyPriceNetto', 'Einkaufspreis netto'),
            NumericField::create('SellPriceNetto', 'Preis (netto)')->setScale(2)->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            NumericField::create('TaxRate', 'MwSt. (%)', $config->InvoiceTax)->setScale(2)->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control')->setReadonly(true),
            TextareaField::create('Description', 'Beschreibung')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control')->setRows(2),
            HiddenField::create('SourceArticleID'),
            NumericField::create('FinalPrice', 'Betrag', '0,00')->setScale(2)->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control')->setReadonly(true),
        );

        // Set OfferID from current offer
        $offer = $this->actOffer();
        if ($offer) {
            $formFields->fieldByName('OfferID')?->setValue($offer->ID);
        }

        $form = Form::create(
            $this, 'addArticleForm', $formFields,
            FieldList::create(FormAction::create('doAddArticle', 'Hinzufügen')->addExtraClass('btn btn-sm btn-success px-4 mb-2')),
            RequiredFieldsValidator::create(['Quantity', 'Title', 'SellPriceNetto'])
        );
        $form->setTemplate('OfferArticleForm');
        $form->setFormAction($this->Link('article/add'));

        return $form;
    }

    public function doAddArticle(array $data, Form $form)
    {
        $offerID = isset($data['OfferID']) && is_numeric($data['OfferID']) ? (int) $data['OfferID'] : null;
        $offer   = $offerID ? Offer::get()->byID($offerID) : null;

        if ($offer && $offer->exists()) {
            $config  = SiteConfig::current_site_config();
            $article = OfferArticle::create();
            $form->saveInto($article);
            $article->OfferID  = $offer->ID;
            $article->TaxRate  = $config->InvoiceTax;
            $article->write();

            $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Artikel erfolgreich hinzugefügt.');
            return $this->redirect($this->Link('edit/' . $offer->ID . '?activetab=offer_article'));
        }

        $this->setNotifySessionData('error', 'bi bi-x-circle', 'Fehler beim Hinzufügen des Artikels.');
        return $this->redirectBack();
    }

    public function editArticleForm()
    {
        $offerArticle = $this->actArticle();
        if (!$offerArticle) {
            return $this->httpError(404);
        }

        $formFields = $this->addArticleForm()->Fields();
        $formFields->push(HiddenField::create('ArticleRecordID', 'ID')->setValue($offerArticle->ID));
        $formFields->fieldByName('OfferID')?->setValue($offerArticle->OfferID);

        $form = Form::create(
            $this, 'editArticleForm', $formFields,
            FieldList::create(FormAction::create('doEditArticle', 'Speichern')->addExtraClass('btn btn-sm btn-success px-4 mb-2')),
            RequiredFieldsValidator::create(['Quantity', 'Title', 'SellPriceNetto'])
        );
        $form->setTemplate('OfferArticleForm');
        $form->loadDataFrom($offerArticle);
        $form->setFormAction($this->Link('article/' . $offerArticle->ID));

        return $form;
    }

    public function doEditArticle(array $data, Form $form)
    {
        $id           = isset($data['ArticleRecordID']) && is_numeric($data['ArticleRecordID']) ? (int) $data['ArticleRecordID'] : null;
        $offerArticle = $id ? OfferArticle::get()->byID($id) : null;

        if ($offerArticle && $offerArticle->exists()) {
            $form->saveInto($offerArticle);
            $offerArticle->write();

            $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Artikel erfolgreich gespeichert.');
            return $this->redirect($this->Link('edit/' . $offerArticle->OfferID . '?activetab=offer_article'));
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

        $offerArticle = OfferArticle::get()->byID($articleID);
        if ($offerArticle && $offerArticle->exists()) {
            $offerID = $offerArticle->OfferID;
            $offerArticle->delete();
            $offerArticle->destroy();
            return $this->redirect($this->Link('edit/' . $offerID . '?activetab=offer_article'));
        }

        return $this->redirectBack();
    }

    // ─── PDF / send / convert ─────────────────────────────────────────────────────

    public function previewPDF()
    {
        $offer = $this->actOffer();
        if ($offer) {
            return $offer->getPDF();
        }
        return $this->httpError(404);
    }

    public function send()
    {
        $offer = $this->actOffer();
        if ($offer) {
            if ($offer->sendOfferEmail()) {
                $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Angebot erfolgreich gesendet.');
                return $this->redirect($this->Link('edit/' . $offer->ID . '?sent=1'));
            }
            $this->setNotifySessionData('error', 'bi bi-x-circle', 'Fehler beim Senden des Angebots.');
            return $this->redirect($this->Link('edit/' . $offer->ID . '?sent=0'));
        }
        return $this->redirectBack();
    }

    public function toInvoice()
    {
        $offer = $this->actOffer();
        if (!$offer) {
            return $this->redirectBack();
        }

        $lastInvoice = Invoice::get()->sort('Created', 'DESC')->first();
        $invoice = new Invoice();
        $invoice->ClientObjectID = $offer->ClientObjectID;
        $invoice->IntroText      = $offer->IntroText;
        $invoice->OutroText      = $offer->OutroText;
        $invoice->InvoiceNumber  = $lastInvoice ? $lastInvoice->getNextInvoiceNumber() : date('Y') . '-0001';
        $invoice->DueDate        = date('Y-m-d', strtotime('+14 days'));
        $invoice->write();

        foreach ($offer->OfferArticles() as $oa) {
            $ia                  = new InvoiceArticle();
            $ia->InvoiceID       = $invoice->ID;
            $ia->Title           = $oa->Title;
            $ia->Description     = $oa->Description;
            $ia->Quantity        = $oa->Quantity;
            $ia->Unit            = $oa->Unit;
            $ia->SellPriceNetto  = $oa->SellPriceNetto;
            $ia->Discount        = $oa->Discount;
            $ia->TaxRate         = $oa->TaxRate;
            $ia->FinalPrice      = $oa->FinalPrice;
            $ia->SourceArticleID = $oa->SourceArticleID;
            $ia->write();
        }

        $offer->Status = 'Accepted';
        $offer->write();

        $invoicePage = InvoicePage::get()->first();
        if ($invoicePage) {
            return $this->redirect($invoicePage->Link('edit/' . $invoice->ID . '?converted=1'));
        }
        return $this->redirect($this->Link('edit/' . $offer->ID . '?invoice_created=' . $invoice->ID));
    }
}
