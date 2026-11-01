<?php

declare(strict_types=1);

namespace Kalakotra\Baudigit\Controllers;

use Kalakotra\Baudigit\Models\Offer;
use Kalakotra\Baudigit\Models\Client;
use Kalakotra\Baudigit\Models\Article;
use Kalakotra\Baudigit\Models\Invoice;
use Kalakotra\Baudigit\Models\Project;
use Kalakotra\Baudigit\Models\ClientObject;
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
use SilverStripe\Control\HTTPResponse;

class InvoicePageController extends \PageController
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
        'sendReminder',
        'cancellation',
        'duplicateInvoice',
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
        'reminder/$ID!'                 => 'sendReminder',
        'cancel/$ID!'                   => 'cancellation',
        'duplicate/$ID!'                => 'duplicateInvoice',
    ];

    // ─── Object lookup ────────────────────────────────────────────────────────────

    private function actInvoice(): ?Invoice
    {
        $id = $this->resourceID();
        if (!$id) {
            return null;
        }
        $invoice = Invoice::get()->byID($id);
        return ($invoice && $invoice->exists()) ? $invoice : null;
    }

    private function actArticle(): ?InvoiceArticle
    {
        $id = $this->subResourceID();
        if (!$id) {
            return null;
        }
        $article = InvoiceArticle::get()->byID($id);
        return ($article && $article->exists()) ? $article : null;
    }

    // ─── Template helpers ─────────────────────────────────────────────────────────

    public function readAllInputs()
    {
        $showOnlyStatus = $this->data()->ShowOnlyStatus;
        $statusValue    = $this->data()->StatusValue;
        if ($showOnlyStatus) {
            return Invoice::get()->filter('Status', $statusValue);
        }
        return Invoice::get();
    }

    public function getAllArticles()
    {
        return Article::get();
    }

    public function getAllInvoiceArticles()
    {
        $invoice = $this->actInvoice();
        return $invoice ? $invoice->InvoiceArticles() : false;
    }

    public function getCurrentInvoice(): ?Invoice
    {
        return $this->actInvoice();
    }

    public function getCurrentInvoiceArticle(): ?InvoiceArticle
    {
        return $this->actArticle();
    }

    // ─── Invoice add/edit ─────────────────────────────────────────────────────────

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
        $lastInvoice       = Invoice::get()->sort('Created', 'DESC')->first();
        $nextInvoiceNumber = $lastInvoice ? $lastInvoice->getNextInvoiceNumber() : date('Y') . '-0001';
        $config            = SiteConfig::current_site_config();
        $dueDate           = date('Y-m-d', strtotime('+14 days'));

        $formFields = FieldList::create(
            TextField::create('InvoiceNumber', 'Rechnungsnummer', $nextInvoiceNumber)->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-control')->setReadonly(true)->setDisabled(true),
            DateField::create('DueDate', 'Fälligkeitsdatum', $dueDate)->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-control'),
            GroupedDropdownField::create('ClientObjectID', 'Kunde', $this->buildClientGroupedList())->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-select select2me')->setEmptyString('-- bitte wählen --'),
            TextareaField::create('IntroText', 'Einleitungstext', $config->InvoiceIntroText)->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-control'),
            TextareaField::create('OutroText', 'Schlusstext', $config->InvoiceOutroText)->setFieldHolderTemplate('FormField_customholder')->addExtraClass('form-control'),
        );

        // Pre-fill from project if ?p=ID
        $projectID = $this->getRequest()->getVar('p');
        if ($projectID && is_numeric($projectID)) {
            $project = Project::get()->byID((int) $projectID);
            if ($project && $project->exists()) {
                $formFields->dataFieldByName('ClientObjectID')?->setValue($project->ClientObjectID);
                if ($project->OfferID) {
                    $formFields->push(HiddenField::create('OfferID', 'Angebot')->setValue($project->OfferID));
                }
            }
            $formFields->push(HiddenField::create('ProjectID', 'Projekt')->setValue($projectID));
        }

        $form = Form::create(
            $this, 'add', $formFields,
            FieldList::create(FormAction::create('doAdd', 'Speichern')->addExtraClass('btn btn-sm btn-success px-4 mb-2')),
            RequiredFieldsValidator::create(['InvoiceNumber', 'ClientObjectID'])
        );
        $form->setFormAction($this->Link('add'));

        return $form;
    }

    public function doAdd(array $data, Form $form)
    {
        $invoice = new Invoice();
        $form->saveInto($invoice);
        $invoice->write();

        if (!empty($data['ProjectID']) && is_numeric($data['ProjectID'])) {
            $project = Project::get()->byID((int) $data['ProjectID']);
            if ($project && $project->exists()) {
                $invoice->ProjectID = $project->ID;
                $invoice->write();
                if ($project->OfferID) {
                    $this->copyOfferArticlesToInvoice((int) $project->OfferID, $invoice->ID);
                }
            }
        }

        return $this->redirect($this->Link('edit/' . $invoice->ID));
    }

    public function edit()
    {
        $invoice = $this->actInvoice();
        if (!$invoice) {
            return $this->httpError(404);
        }

        $editFields = $this->add()->Fields();
        $editFields->push(HiddenField::create('InvoiceID', 'ID')->setValue($invoice->ID));

        $actions = FieldList::create();
        if ($invoice->checkEditingStatus()) {
            $actions->push(FormAction::create('doEdit', 'Speichern')->addExtraClass('btn btn-sm btn-success px-4 mb-2'));
        } else {
            foreach ($editFields as $field) {
                $field->setReadonly(true)->setDisabled(true);
            }
        }

        $form = Form::create($this, 'edit', $editFields, $actions, RequiredFieldsValidator::create(['InvoiceNumber', 'ClientObjectID']));
        $form->loadDataFrom($invoice);
        $form->setFormAction($this->Link('edit/' . $invoice->ID));

        return $form;
    }

    public function doEdit(array $data, Form $form)
    {
        $invoice = $this->actInvoice();
        if ($invoice) {
            $form->saveInto($invoice);
            $invoice->write();

            $this->setNotifySessionData('success', 'bi bi-check2-circle', _t('Kalakotra\\Baudigit\\Controllers\\InvoicePageController.INVOICE_SAVED', 'Rechnung erfolgreich gespeichert.'));
            return $this->redirectBack();
        }

        $this->setNotifySessionData('danger', 'bi bi-exclamation-triangle', _t('Kalakotra\\Baudigit\\Controllers\\InvoicePageController.INVOICE_SAVE_ERROR', 'Fehler beim Speichern der Rechnung.'));
        return $this->redirectBack();
    }

    public function changeStatus()
    {
        $invoice = $this->actInvoice();
        $status  = $this->getRequest()->param('Status');

        if ($invoice && is_numeric($status)
            && $invoice->Status != 3
            && $invoice->CancellationID == 0) {
            $invoice->Status = (int) $status;
            $invoice->write();

            $this->setNotifySessionData('success', 'bi bi-check2-circle', _t('Kalakotra\\Baudigit\\Controllers\\InvoicePageController.STATUS_CHANGED', 'Rechnungsstatus erfolgreich geändert.'));
            return $this->redirectBack();
        }

        $this->setNotifySessionData('danger', 'bi bi-exclamation-triangle', _t('Kalakotra\\Baudigit\\Controllers\\InvoicePageController.STATUS_CHANGE_ERROR', 'Fehler beim Ändern des Rechnungsstatus.'));
        return $this->redirectBack();
    }

    // ─── Article forms ────────────────────────────────────────────────────────────

    public function addArticleForm()
    {
        $config = SiteConfig::current_site_config();

        $formFields = FieldList::create(
            NumericField::create('Quantity', 'Menge', 1)->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control')->setAttribute('type', 'number')->setAttribute('min', '1')->setAttribute('step', '1'),
            NumericField::create('Discount', 'Rabatt (%)', '0')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control')->setScale(2),
            HiddenField::create('InvoiceID', 'Rechnung'),
            TextField::create('Title', 'Titel')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control')->setAttribute('list', 'articleList')->setAttribute('autocomplete', 'off'),
            DropdownField::create('Unit', '', Article::singleton()->getUnitOptions())->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-select'),
            HiddenField::create('BuyPriceNetto', 'Einkaufspreis netto'),
            NumericField::create('SellPriceNetto', 'Preis (netto)')->setScale(2)->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control'),
            NumericField::create('TaxRate', 'MwSt. (%)', $config->InvoiceTax)->setScale(2)->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control')->setReadonly(true),
            TextareaField::create('Description', 'Beschreibung')->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control')->setRows(2),
            HiddenField::create('SourceArticleID'),
            NumericField::create('FinalPrice', 'Betrag', '0,00')->setScale(2)->setFieldHolderTemplate('FormField_customholder')->setAttribute('class', 'form-control')->setReadonly(true),
        );

        $invoice = $this->actInvoice();
        if ($invoice) {
            $formFields->fieldByName('InvoiceID')?->setValue($invoice->ID);
        }

        $form = Form::create(
            $this, 'addArticleForm', $formFields,
            FieldList::create(FormAction::create('doAddArticle', 'Hinzufügen')->addExtraClass('btn btn-sm btn-success px-4 mb-2')),
            RequiredFieldsValidator::create(['Quantity', 'Title', 'SellPriceNetto'])
        );
        $form->setTemplate('InvoiceArticleForm');
        $form->setFormAction($this->Link('article/add'));

        return $form;
    }

    public function doAddArticle(array $data, Form $form)
    {
        $invoiceID = isset($data['InvoiceID']) && is_numeric($data['InvoiceID']) ? (int) $data['InvoiceID'] : null;
        $invoice   = $invoiceID ? Invoice::get()->byID($invoiceID) : null;
        $isAjax    = $this->getRequest()->isAjax();

        if ($invoice && $invoice->exists()) {
            $config  = SiteConfig::current_site_config();
            $article = InvoiceArticle::create();
            $form->saveInto($article);
            $article->InvoiceID = $invoice->ID;
            $article->TaxRate   = $config->InvoiceTax;
            $article->write();

            if ($isAjax) {
                return $this->jsonArticleResponse($article, $invoice);
            }

            $this->setNotifySessionData('success', 'bi bi-check2-circle', _t('Kalakotra\\Baudigit\\Controllers\\InvoicePageController.ARTICLE_SAVED', 'Artikel erfolgreich gespeichert.'));
            return $this->redirect($this->Link('edit/' . $invoice->ID . '?activetab=invoice_article'));
        }

        if ($isAjax) {
            return $this->jsonError(_t('Kalakotra\\Baudigit\\Controllers\\InvoicePageController.ARTICLE_ADD_ERROR', 'Fehler beim Hinzufügen des Artikels.'));
        }

        $this->setNotifySessionData('danger', 'bi bi-exclamation-triangle', _t('Kalakotra\\Baudigit\\Controllers\\InvoicePageController.ARTICLE_ADD_ERROR', 'Fehler beim Hinzufügen des Artikels.'));
        return $this->redirectBack();
    }

    public function editArticleForm()
    {
        $invoiceArticle = $this->actArticle();
        if (!$invoiceArticle) {
            return $this->httpError(404);
        }

        $formFields = $this->addArticleForm()->Fields();
        $formFields->push(HiddenField::create('ArticleRecordID', 'ID')->setValue($invoiceArticle->ID));
        $formFields->fieldByName('InvoiceID')?->setValue($invoiceArticle->InvoiceID);

        $form = Form::create(
            $this, 'editArticleForm', $formFields,
            FieldList::create(FormAction::create('doEditArticle', 'Speichern')->addExtraClass('btn btn-sm btn-success px-4 mb-2')),
            RequiredFieldsValidator::create(['Quantity', 'Title', 'SellPriceNetto'])
        );
        $form->setTemplate('InvoiceArticleForm');
        $form->loadDataFrom($invoiceArticle);
        $form->setFormAction($this->Link('article/' . $invoiceArticle->ID));

        return $form;
    }

    public function doEditArticle(array $data, Form $form)
    {
        $id             = isset($data['ArticleRecordID']) && is_numeric($data['ArticleRecordID']) ? (int) $data['ArticleRecordID'] : null;
        $invoiceArticle = $id ? InvoiceArticle::get()->byID($id) : null;
        $isAjax         = $this->getRequest()->isAjax();

        if ($invoiceArticle && $invoiceArticle->exists()) {
            $form->saveInto($invoiceArticle);
            $invoiceArticle->write();

            if ($isAjax) {
                $invoice = Invoice::get()->byID($invoiceArticle->InvoiceID);
                return $this->jsonArticleResponse($invoiceArticle, $invoice);
            }

            $this->setNotifySessionData('success', 'bi bi-check2-circle', _t('Kalakotra\\Baudigit\\Controllers\\InvoicePageController.ARTICLE_SAVED', 'Artikel erfolgreich gespeichert.'));
            return $this->redirect($this->Link('edit/' . $invoiceArticle->InvoiceID . '?activetab=invoice_article'));
        }

        if ($isAjax) {
            return $this->jsonError(_t('Kalakotra\\Baudigit\\Controllers\\InvoicePageController.ARTICLE_SAVE_ERROR', 'Fehler beim Speichern des Artikels.'));
        }

        $this->setNotifySessionData('danger', 'bi bi-exclamation-triangle', _t('Kalakotra\\Baudigit\\Controllers\\InvoicePageController.ARTICLE_SAVE_ERROR', 'Fehler beim Speichern des Artikels.'));
        return $this->redirectBack();
    }

    public function deleteArticle()
    {
        $articleID = $this->subResourceID();
        $isAjax    = $this->getRequest()->isAjax();

        if (!$articleID) {
            return $isAjax ? $this->jsonError('Ungültige Artikel-ID.', 400) : $this->httpError(400);
        }

        $invoiceArticle = InvoiceArticle::get()->byID($articleID);
        if ($invoiceArticle && $invoiceArticle->exists()) {
            $invoiceID = $invoiceArticle->InvoiceID;
            $invoiceArticle->delete();
            $invoiceArticle->destroy();

            if ($isAjax) {
                $invoice = Invoice::get()->byID($invoiceID);
                return $this->jsonTotalsResponse($invoice);
            }

            return $this->redirect($this->Link('edit/' . $invoiceID . '?activetab=invoice_article'));
        }

        return $isAjax ? $this->jsonError('Artikel nicht gefunden.', 404) : $this->redirectBack();
    }

    // ─── Duplicate ────────────────────────────────────────────────────────────────

    public function duplicateInvoice()
    {
        $source = $this->actInvoice();
        if (!$source) {
            return $this->httpError(404);
        }

        // Build next invoice number
        $lastInvoice       = Invoice::get()->sort('Created', 'DESC')->first();
        $nextInvoiceNumber = $lastInvoice ? $lastInvoice->getNextInvoiceNumber() : date('Y') . '-0001';

        $copy                  = new Invoice();
        $copy->InvoiceNumber   = $nextInvoiceNumber;
        $copy->ClientObjectID  = $source->ClientObjectID;
        $copy->ProjectID       = $source->ProjectID;
        $copy->OfferID         = $source->OfferID;
        $copy->IntroText       = $source->IntroText;
        $copy->OutroText       = $source->OutroText;
        $copy->DueDate         = date('Y-m-d', strtotime('+14 days'));
        $copy->Status          = 0; // Draft
        $copy->write();

        foreach ($source->InvoiceArticles() as $ia) {
            $na                  = new InvoiceArticle();
            $na->InvoiceID       = $copy->ID;
            $na->Title           = $ia->Title;
            $na->Description     = $ia->Description;
            $na->Quantity        = $ia->Quantity;
            $na->Unit            = $ia->Unit;
            $na->SellPriceNetto  = $ia->SellPriceNetto;
            $na->BuyPriceNetto   = $ia->BuyPriceNetto;
            $na->Discount        = $ia->Discount;
            $na->TaxRate         = $ia->TaxRate;
            $na->FinalPrice      = $ia->FinalPrice;
            $na->SourceArticleID = $ia->SourceArticleID;
            $na->write();
        }

        $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Rechnung als Entwurf dupliziert: ' . $copy->InvoiceNumber);
        return $this->redirect($this->Link('edit/' . $copy->ID));
    }

    // ─── PDF / send / cancellation ────────────────────────────────────────────────

    public function previewPDF()
    {
        $invoice = $this->actInvoice();
        if ($invoice) {
            return $invoice->getPDF();
        }
        return $this->httpError(404);
    }

    public function send()
    {
        $invoice = $this->actInvoice();
        if ($invoice) {
            if ($invoice->sendInvoiceEmail()) {
                $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Rechnung erfolgreich versendet.');
                return $this->redirect($this->Link('edit/' . $invoice->ID . '?sent=1'));
            }
            $this->setNotifySessionData('danger', 'bi bi-exclamation-triangle', 'Fehler beim Versenden der Rechnung.');
            return $this->redirect($this->Link('edit/' . $invoice->ID . '?sent=0'));
        }
        return $this->redirectBack();
    }

    public function sendReminder()
    {
        $invoice = $this->actInvoice();
        if (!$invoice) {
            return $this->redirectBack();
        }

        // Samo za poslane, neplaćene račune
        if ($invoice->Status !== 1) {
            $this->setNotifySessionData('warning', 'bi bi-exclamation-circle', 'Erinnerung kann nur für gesendete Rechnungen verschickt werden.');
            return $this->redirect($this->Link('edit/' . $invoice->ID));
        }

        if ($invoice->sendReminderEmail()) {
            $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Zahlungserinnerung erfolgreich gesendet.');
        } else {
            $this->setNotifySessionData('danger', 'bi bi-exclamation-triangle', 'Fehler: Keine E-Mail-Adresse für diesen Kunden hinterlegt.');
        }

        return $this->redirect($this->Link('edit/' . $invoice->ID));
    }

    public function cancellation()
    {
        $invoice = $this->actInvoice();
        if (!$invoice) {
            return $this->redirectBack();
        }

        $invoice->Status = 3; // Cancelled
        $invoice->write();

        $storno                      = new Invoice();
        $storno->ClientObjectID      = $invoice->ClientObjectID;
        $storno->DueDate             = date('Y-m-d');
        $storno->IntroText           = 'Stornorechnung für Rechnung ' . $invoice->InvoiceNumber . '.';
        $storno->OutroText           = 'Diese Rechnung wurde automatisch als Stornorechnung erstellt.';
        $storno->Status              = 2; // Paid
        $storno->CancellationOfID    = $invoice->ID;
        $storno->write();

        foreach ($invoice->InvoiceArticles() as $ia) {
            $na                  = new InvoiceArticle();
            $na->InvoiceID       = $storno->ID;
            $na->Title           = $ia->Title;
            $na->Description     = $ia->Description;
            $na->Quantity        = -1 * $ia->Quantity;
            $na->Unit            = $ia->Unit;
            $na->SellPriceNetto  = $ia->SellPriceNetto;
            $na->Discount        = $ia->Discount;
            $na->TaxRate         = $ia->TaxRate;
            $na->FinalPrice      = -1 * $ia->FinalPrice;
            $na->SourceArticleID = $ia->SourceArticleID;
            $na->write();
        }

        $this->setNotifySessionData('success', 'bi bi-check2-circle', 'Stornorechnung erfolgreich erstellt.');
        return $this->redirect($this->Link('edit/' . $storno->ID));
    }

    // ─── Utility ─────────────────────────────────────────────────────────────────

    private function copyOfferArticlesToInvoice(int $offerID, int $invoiceID): void
    {
        $offer   = Offer::get()->byID($offerID);
        $invoice = Invoice::get()->byID($invoiceID);

        if (!$offer || !$invoice) {
            return;
        }

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
    }

    // ─── AJAX helpers ─────────────────────────────────────────────────────────────

    private function jsonArticleResponse(InvoiceArticle $article, ?Invoice $invoice): HTTPResponse
    {
        $unitOptions = (new InvoiceArticle())->getUnitOptions();
        $data = [
            'ok'      => true,
            'article' => [
                'id'             => $article->ID,
                'title'          => $article->Title,
                'description'    => $article->Description,
                'quantity'       => (float) $article->Quantity,
                'unit'           => (int) $article->Unit,
                'unitLabel'      => $unitOptions[$article->Unit] ?? '',
                'sellPriceNetto' => (float) $article->SellPriceNetto,
                'discount'       => (float) $article->Discount,
                'discountAmount' => round($article->getDiscountAmount(), 2),
                'taxRate'        => (float) $article->TaxRate,
                'taxAmount'      => round($article->getTaxAmount(), 2),
                'finalPrice'     => round($article->getBruttoPrice(), 2),
                'lineNetto'      => round($article->getDiscountedPrice(), 2),
            ],
            'totals'  => $this->buildTotals($invoice),
        ];
        return $this->jsonResponse($data);
    }

    private function jsonTotalsResponse(?Invoice $invoice): HTTPResponse
    {
        return $this->jsonResponse(['ok' => true, 'totals' => $this->buildTotals($invoice)]);
    }

    private function buildTotals(?Invoice $invoice): array
    {
        if (!$invoice) return [];
        return [
            'netto'  => round((float) $invoice->getTotalNetto(true), 2),
            'tax'    => round((float) $invoice->getTotalTax(true), 2),
            'brutto' => round((float) $invoice->getTotalBrutto(true), 2),
        ];
    }

    private function jsonError(string $message, int $status = 422): HTTPResponse
    {
        return $this->jsonResponse(['ok' => false, 'error' => $message], $status);
    }

    private function jsonResponse(array $data, int $status = 200): HTTPResponse
    {
        $response = HTTPResponse::create(json_encode($data), $status);
        $response->addHeader('Content-Type', 'application/json');
        return $response;
    }
}
