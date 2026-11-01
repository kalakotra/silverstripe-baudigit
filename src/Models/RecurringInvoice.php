<?php

namespace Kalakotra\Baudigit\Models;

use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\Control\Controller;
use Kalakotra\Baudigit\Models\RecurringInvoiceArticle;
use SilverStripe\i18n\i18nEntityProvider;
use Kalakotra\Baudigit\Utils\DataObjectPermissions;
use SilverStripe\Security\PermissionProvider;

class RecurringInvoice extends DataObject implements PermissionProvider, i18nEntityProvider
{
    use DataObjectPermissions;

    private static $table_name = 'baudigit_RecurringInvoice';
    private static $singular_name = 'Wiederkehrende Rechnung';

    private static $plural_name = 'Wiederkehrende Rechnungen';

    private static $default_sort = 'Created DESC';

    public function provideI18nEntities()
    {
        return [
            __CLASS__ . '.SINGULARNAME' => 'Angebot',
            __CLASS__ . '.PLURALNAME' => 'Angebote',
            __CLASS__ . '.PLURALS' => [
                'one' => 'Angebot',
                'other' => '{count} Angebote',
            ],
        ];
    }

    private static $db = [
        'OfferNumber' => 'Varchar(100)',
        'StartDate' => 'Date',
        'ValidUntil' => 'Date',
        'InvoiceInterval' => 'Int', // 0= monthly, 1=quarterly, 2=half-yearly, 3=yearly
        'IntroText' => 'Text',
        'OutroText' => 'Text',
        'TotalNetto' => 'Decimal(19,2)',
        'TotalBrutto' => 'Decimal(19,2)',
        'Status' => 'Int', // 0=Inactive, 1=Active
    ];

    private static $has_one = [
        'ClientObject' => ClientObject::class,
    ];

    private static $has_many = [
        'Articles' => RecurringInvoiceArticle::class,

        'Invoices' => Invoice::class,
    ];

    private static $cascade_deletes = [
        'Articles',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        

        return $fields;
    }

    public function getTotalNetto(): float|int
    {
        $total = 0;
        foreach ($this->Articles() as $article) {
            $total += $article->getDiscountedPrice();
        }
        return $total;
    }

    public function getTotalTax(): float|int
    {
        $total = 0;
        foreach ($this->Articles() as $article) {
            $total += $article->getTaxAmount();
        }
        return $total;
    }

    public function getTotalBrutto(): float|int
    {
        return $this->getTotalNetto() + $this->getTotalTax();
    }

    public function getTotalBruttoAsString(): string
    {
        return number_format($this->getTotalBrutto(), 2, ',', '.').' €';
    }

    public function getGermanInterval(): string {
        return match ($this->InvoiceInterval) {
            0 => 'Monatlich',
            1 => 'Vierteljährlich',
            2 => 'Halbjährlich',
            3 => 'Jährlich',
            default => 'Unbekannt',
        };
    }

    public function getGermanStatus() {
        return match ($this->Status) {
            0 => 'Inaktiv',
            1 => 'Aktiv',
            default => 'Unbekannt',
        };
    }

    public function getGermanStatusOptions(): array
    {
        return [
            0 => 'Inaktiv',
            1 => 'Aktiv',
        ];
    }

    public function getGermanStatusOptionsDataList()
    {

        // return false if invoice is cancelled, paid or connected to a cancellation
        if ($this->Status >= 2 || $this->CancellationOfID > 0) {
            return false;
        }

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

    public function getPDF() {
        $controller = Controller::curr();
        $html = $controller->customise(['Invoice' => $this])->renderWith('app/Pages/Layout/PDF/RecurringInvoice');
        $filename = "wiederkehrende-rechnung-".$this->ID.".pdf";
        $pdfPath = $controller->generatePDF($html, $filename);

        $controller->getResponse()->addHeader('Content-Type', 'application/pdf');
        $controller->getResponse()->addHeader('Content-Disposition', 'inline; filename="'.$filename.'"');

        return file_get_contents($pdfPath);
    }

    public function checkIfInvoiceDue(): bool
    {   
        if ($this->Status != 1) {
            return false; // only active recurring invoices
        }

        $today = date('Y-m-d');
        if ($this->StartDate <= $today) {
            // check if an invoice has already been generated for the current period
            $lastInvoice = $this->Invoices()->sort('Created', 'DESC')->first();
            if ($lastInvoice) {
                $nextInvoiceDate = null;
                switch ($this->InvoiceInterval) {
                    case 0: // monthly
                        $nextInvoiceDate = date('Y-m-d', strtotime($lastInvoice->Created . ' +1 month'));
                        break;
                    case 1: // quarterly
                        $nextInvoiceDate = date('Y-m-d', strtotime($lastInvoice->Created . ' +3 months'));
                        break;
                    case 2: // half-yearly
                        $nextInvoiceDate = date('Y-m-d', strtotime($lastInvoice->Created . ' +6 months'));
                        break;
                    case 3: // yearly
                        $nextInvoiceDate = date('Y-m-d', strtotime($lastInvoice->Created . ' +1 year'));
                        break;
                }
                if ($nextInvoiceDate && $nextInvoiceDate <= $today) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true; // no invoice generated yet
            }
        }
        return false;
    }

    public function generateInvoiceFromRecurring(): Invoice
    {
        $invoice = Invoice::create();
        $invoice->ClientObjectID = $this->ClientObjectID;
        $invoice->OfferNumber = $this->OfferNumber;
        $invoice->IntroText = $this->IntroText;
        $invoice->OutroText = $this->OutroText;
        $invoice->TotalNetto = $this->getTotalNetto();
        $invoice->TotalBrutto = $this->getTotalBrutto();
        $invoice->Status = 0; // set to draft
        $invoice->write();

        // copy articles
        foreach ($this->Articles() as $recurringArticle) {
            $article = InvoiceArticle::create();
            $article->InvoiceID = $invoice->ID;
            $article->SourceArticleID = $recurringArticle->SourceArticleID;
            $article->Title = $recurringArticle->Title;
            $article->Description = $recurringArticle->Description;
            $article->Quantity = $recurringArticle->Quantity;
            $article->SellPriceNetto = $recurringArticle->SellPriceNetto;
            $article->Discount = $recurringArticle->Discount;
            $article->TaxRate = $recurringArticle->TaxRate;
            $article->write();
        }

        return $invoice;
    }

}