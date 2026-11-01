<?php

namespace Kalakotra\Baudigit\Models;

use Kalakotra\Baudigit\Pages\InvoicePage;
use Kalakotra\Baudigit\Models\ClientObject;
use Kalakotra\Baudigit\Models\InvoiceArticle;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Email\Email;
use Psr\Log\LoggerInterface;
use horstoeko\zugferd\ZugferdProfiles;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\i18n\i18nEntityProvider;
use Kalakotra\Baudigit\Utils\DataObjectPermissions;
use horstoeko\zugferd\ZugferdDocumentBuilder;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\Core\Injector\Injector;
use horstoeko\zugferd\ZugferdDocumentPdfMerger;

class Invoice extends DataObject implements i18nEntityProvider, PermissionProvider
{
    use DataObjectPermissions;

    private static $table_name = 'baudigit_Invoice';

    private static $singular_name = 'Rechnung';
    private static $plural_name = 'Rechnungen';

    private static $default_sort = 'Created DESC';
    public function provideI18nEntities()
    {
        return [
            __CLASS__ . '.SINGULARNAME' => 'Rechnung',
            __CLASS__ . '.PLURALNAME' => 'Rechnungen',
            __CLASS__ . '.PLURALS' => [
                'one' => 'Rechnung',
                'other' => '{count} Rechnungen',
            ],
        ];
    }
    private static $db = [
        'InvoiceNumber' => 'Varchar',
        'TotalAmount' => 'Decimal(19,2)',
        'IntroText' => 'Text',
        'OutroText' => 'Text',
        'Status' => 'Int', //'Enum("Draft,Sent,Paid", "Draft")', 0=Draft, 1=Sent, 2=Paid, 3=Cancelled (Storno)
        'DueDate' => 'Date',
    ];

    private static $has_one = [
        'ClientObject' => ClientObject::class,
        'Project' => Project::class,
        'Offer' => Offer::class,
        'CancellationOf' => Invoice::class,
        'RecurringInvoice' => RecurringInvoice::class,
    ];

    private static $has_many = [
        'InvoiceArticles'   => InvoiceArticle::class,
        'EmailLogs'         => InvoiceEmailLog::class,
    ];

    private static $cascade_deletes = [
        'InvoiceArticles',
        'EmailLogs',
    ];

    public function getNextInvoiceNumber()
    {
        $currentYear = date('Y');
    
        // Provjeri format trenutnog broja (YYYY-XXXX)
        if (preg_match('/^(\d{4})-(\d{4})$/', $this->InvoiceNumber, $matches)) {
            $yearFromInvoice = $matches[1];
            
            // Ako je godina na zadnjem računu ista kao trenutna, samo uvećaj broj
            if ($yearFromInvoice == $currentYear) {
                $number = (int)$matches[2] + 1;
                return sprintf('%s-%04d', $currentYear, $number);
            }
        }
        
        // Ako je godina drugačija (nova godina) ili format nije prepoznat, 
        // resetuj na 0001 za trenutnu godinu
        return $currentYear . '-0001';
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        // set offer number if not set
        if ($this->InvoiceNumber == '') {
            $currentYear = date('Y');
    
            // Tražimo zadnji račun isključivo u TEKUĆOJ godini
            $lastInvoice = Invoice::get()
                ->filter(['InvoiceNumber:StartsWith' => $currentYear])
                ->sort('InvoiceNumber', 'DESC')
                ->first();

            if ($lastInvoice) {
                // Ako postoji račun u ovoj godini, nastavi niz
                $parts = explode('-', $lastInvoice->InvoiceNumber);
                $nextSequence = (int)$parts[1] + 1;
                $this->InvoiceNumber = $currentYear . '-' . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
            } else {
                // Ako je nova godina i nema još računa, kreni od 0001
                $this->InvoiceNumber = $currentYear . '-0001';
            }
        }

        if (!$this->DueDate) {
            $this->DueDate = date('Y-m-d', strtotime('+30 days'));
        }

    }

    public function getGermanStatusOptions(): array
    {
        return [
            0 => 'Entwurf',
            1 => 'Gesendet',
            2 => 'Bezahlt',
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
                'isDisabled' => ($this->Status == 4 || $this->CancellationOfID > 0) ? 'true' : 'false',
                'isActive' => ($this->Status == $key) ? 'active' : '',
                'Key' => $key,
                'Title' => $value,
            ];
            $myList->push($myItem);
        }
        return $myList;
    }

    public function getTotalNetto($raw = false)
    {
        $total = 0;
        foreach ($this->InvoiceArticles() as $article) {
            $total += $article->getDiscountedPrice();
        }
        return $total;
    }

    public function getTotalTax($raw = false)
    {
        $total = 0;
        foreach ($this->InvoiceArticles() as $article) {
            $total += $article->getTaxAmount();
        }
        return $total;
    }

    public function getTotalBrutto($raw = false): float|int
    {
        $myBrutto = $this->getTotalNetto() + $this->getTotalTax();
        // show always 2 decimal places
        if ($raw === false) {
            return (float) number_format($myBrutto, 2, ',', '');
        } else {
            return (float) $myBrutto;
        }
    }

    public function getTotalBruttoAsString(): string
    {
        return  '€ ' . number_format($this->getTotalBrutto(), 2, ',', ' ');
    }

    public function getGermanStatus() {
        return match ($this->Status) {
            0 => 'Entwurf',
            1 => 'Gesendet',
            2 => 'Bezahlt',
            3 => 'Storniert',
            default => 'Unbekannt'
        };
    }

    public function generateEInvoiceXML(): ZugferdDocumentBuilder
    {
        $config      = SiteConfig::current_site_config();
        $termDays    = (int) ($config->PaymentTermDays ?: 10);
        $taxRate     = (float) ($config->InvoiceTax ?: 19);

        // Dates
        $invoiceDate = \DateTime::createFromFormat('Y-m-d', $this->dbObject('Created')->Format('Y-MM-dd'))
            ?: new \DateTime();
        $dueDate     = $this->DueDate
            ? \DateTime::createFromFormat('Y-m-d', $this->DueDate) ?: (clone $invoiceDate)->modify("+{$termDays} days")
            : (clone $invoiceDate)->modify("+{$termDays} days");

        // Totals
        $totalNetto  = (float) $this->getTotalNetto(true);
        $totalTax    = (float) $this->getTotalTax(true);
        $totalBrutto = $totalNetto + $totalTax;

        // Buyer reference — XRechnung BT-10 (Leitweg-ID); fallback to invoice number
        $clientObject  = $this->ClientObject();
        $client        = $clientObject->Client();
        $buyerRef      = (string) ($client->LeitwegID ?: $this->InvoiceNumber);

        $document = ZugferdDocumentBuilder::CreateNew(ZugferdProfiles::PROFILE_XRECHNUNG_3);

        // ── Header ──────────────────────────────────────────────────────────────
        $document
            ->setDocumentInformation($this->InvoiceNumber, '380', $invoiceDate, 'EUR')
            ->setDocumentBusinessProcess('urn:fdc:peppol.eu:2017:poacc:billing:01:1.0')
            ->setDocumentSupplyChainEvent($invoiceDate);

        if ($this->IntroText) {
            $document->addDocumentNote($this->IntroText);
        }

        // ── Seller ──────────────────────────────────────────────────────────────
        $document
            ->setDocumentSeller($config->CompanyName)
            ->setDocumentSellerAddress(
                (string) $config->CompanyAddressLine1,
                (string) $config->CompanyAddressLine2,
                '',
                (string) $config->CompanyPostalCode,
                (string) $config->CompanyCity,
                (string) ($config->CompanyCountry ?: 'DE')
            )
            ->setDocumentSellerContact(
                (string) $config->CompanyContactPerson,
                '',
                (string) $config->CompanyContactPhone,
                '',
                (string) $config->CompanyContactEmail
            )
            ->setDocumentSellerCommunication('EM', (string) $config->CompanyContactEmail)
            ->addDocumentSellerTaxRegistration('VA', (string) $config->CompanyVATId);

        // ── Buyer ────────────────────────────────────────────────────────────────
        $document
            ->setDocumentBuyer((string) $client->Company)
            ->setDocumentBuyerReference($buyerRef)
            ->setDocumentBuyerAddress(
                (string) $client->Address,
                '',
                '',
                (string) $client->PostalCode,
                (string) $client->City,
                'DE'
            );

        $buyerEmail = $client->Email ?: $clientObject->ContactEmail;
        if ($buyerEmail) {
            $document->setDocumentBuyerCommunication('EM', (string) $buyerEmail);
        }

        // ── Payment ──────────────────────────────────────────────────────────────
        $paymentTermText = $config->PaymentTermText
            ? sprintf((string) $config->PaymentTermText, $termDays)
            : "Zahlbar innerhalb von {$termDays} Tagen ohne Abzug.";

        $document
            ->addDocumentPaymentTerm($paymentTermText, $dueDate)
            ->addDocumentPaymentMean(
                '42',
                'Überweisung',
                '', '', '', '',
                (string) $config->PaymentIBAN,
                (string) $config->PaymentAccountHolder,
                '',
                (string) $config->PaymentBIC
            );

        // ── Tax summary (single rate) ────────────────────────────────────────────
        $document->addDocumentTax('S', 'VAT', $totalNetto, $totalTax, $taxRate);

        // ── Document summation ───────────────────────────────────────────────────
        // setDocumentSummation(grandTotal, duePayable, lineTotalAmount, chargeTotalAmount,
        //                       allowanceTotalAmount, taxBasisTotalAmount, taxTotalAmount,
        //                       rounding, prepaidAmount)
        $document->setDocumentSummation(
            $totalBrutto,  // BT-112 grand total
            $totalBrutto,  // BT-115 due payable
            $totalNetto,   // BT-106 sum of line net amounts
            0.0,           // BT-108 charge total
            0.0,           // BT-107 allowance total
            $totalNetto,   // BT-109 tax basis total
            $totalTax,     // BT-110 tax total
            null,          // BT-114 rounding
            0.0            // BT-113 prepaid
        );

        // ── Line items ───────────────────────────────────────────────────────────
        $position = 1;
        foreach ($this->InvoiceArticles() as $item) {
            $lineNetto = (float) $item->getDiscountedPrice(); // netto after discount × qty

            $document
                ->addNewPosition((string) $position)
                ->setDocumentPositionProductDetails(
                    (string) $item->Title,
                    (string) $item->Description
                )
                ->setDocumentPositionQuantity((float) $item->Quantity, $item->getUnitShortLabel())
                ->setDocumentPositionNetPrice((float) $item->SellPriceNetto)
                ->addDocumentPositionTax('S', 'VAT', $taxRate)
                // BT-131: line net amount (netto after discount, before tax)
                ->setDocumentPositionLineSummation($lineNetto);

            $position++;
        }

        return $document;
    }

    public function getPDF(): string
    {
        $controller = Controller::curr();
        $html       = $controller->customise(['Invoice' => $this])->renderWith('app/Pages/Layout/PDF/Invoice');
        $filename   = 'rechnung-' . $this->ID . '.pdf';
        $pdfPath    = $controller->generatePDF((string) $html, $filename);

        // Embed ZUGFeRD / XRechnung XML into the PDF (PDF/A-3)
        try {
            $xmlDocument = $this->generateEInvoiceXML();
            $pdfMerger   = new ZugferdDocumentPdfMerger($xmlDocument, $pdfPath);
            $pdfMerger->generateDocument()->saveDocument($pdfPath);
        } catch (\Throwable $e) {
            // Log but don't block PDF delivery
            Injector::inst()
                ->get(LoggerInterface::class)
                ->warning('ZUGFeRD embed failed for invoice ' . $this->ID . ': ' . $e->getMessage());
        }

        $controller->getResponse()->addHeader('Content-Type', 'application/pdf');
        $controller->getResponse()->addHeader('Content-Disposition', 'inline; filename="' . $filename . '"');

        return (string) file_get_contents($pdfPath);
    }

    public function sendInvoiceEmail() {
        $client = $this->ClientObject()->Client();
        if ($client && $client->Email) {
            $config = SiteConfig::current_site_config();
            $pdfContent = $this->getPDF();
            $email = Email::create();
            $email->setTo($client->Email);
            $email->setFrom($config->CompanyContactEmail);
            $email->setSubject('Rechnung ' . $this->InvoiceNumber);
            //$email->setBody(DBField::create_field('HTMLText', $config->EmailInvoiceText));
            $email->setBody($config->EmailInvoiceText);
            $email->addAttachmentFromData($pdfContent, 'Rechnung-' . $this->InvoiceNumber . '.pdf', 'application/pdf');
            $email->sendPlain();
            // update status if is Draft
            if ($this->Status == 0) {
                $this->Status = 1; // Sent
            }
            $this->write();
            $this->logEmail('invoice', $client->Email, true);
            return true;
        }
        return false;
    }

    public function isOverdue(): bool
    {
        return $this->Status === 1 && $this->DueDate && $this->DueDate < date('Y-m-d');
    }

    public function sendReminderEmail(): bool
    {
        $client = $this->ClientObject()->Client();
        $email  = $client->Email ?: $this->ClientObject()->ContactEmail;

        if (!$email) {
            return false;
        }

        $config  = SiteConfig::current_site_config();
        $dueDate = $this->dbObject('DueDate')->Format('dd.MM.Y');
        $total   = '€ ' . number_format((float) $this->getTotalBrutto(true), 2, ',', '.');

        $bodyTemplate = $config->EmailReminderText
            ?: "Sehr geehrte Damen und Herren,\n\nhiermit möchten wir Sie daran erinnern, dass Rechnung {invoiceNumber} vom {dueDate} in Höhe von {totalAmount} noch offen ist.\n\nBitte überweisen Sie den Betrag auf unser Konto.\n\nMit freundlichen Grüßen\n{companyName}";

        $body = strtr($bodyTemplate, [
            '{invoiceNumber}' => $this->InvoiceNumber,
            '{dueDate}'       => $dueDate,
            '{totalAmount}'   => $total,
            '{companyName}'   => (string) $config->CompanyName,
        ]);

        $pdfContent = $this->getPDF();

        $mail = Email::create();
        $mail->setTo((string) $email);
        $mail->setFrom((string) $config->CompanyContactEmail);
        $mail->setSubject('Zahlungserinnerung – Rechnung ' . $this->InvoiceNumber);
        $mail->setBody($body);
        $mail->addAttachmentFromData($pdfContent, 'Rechnung-' . $this->InvoiceNumber . '.pdf', 'application/pdf');
        $mail->sendPlain();
        $this->logEmail('reminder', (string) $email, true);

        return true;
    }

    private function logEmail(string $type, string $to, bool $success): void
    {
        $log = InvoiceEmailLog::create();
        $log->Type      = $type;
        $log->SentTo    = $to;
        $log->SentAt    = date('Y-m-d H:i:s');
        $log->Success   = $success;
        $log->InvoiceID = $this->ID;
        $log->write();
    }

    public function checkEditingStatus() {
        if ($this->Status >= 2) { // not Draft
            return false;
        }
        return true;
    }


    // check if invoice can be cancelled
    // maybe can be used under checkEditingStatus function but for now separately
    public function canBeCancelled() {
        if ($this->Status >= 2 || $this->CancellationOfID > 0) { // Paid or already cancelled
            return false;
        }
        return true;
    }

    public function getFirstInvoicePage() {
        return InvoicePage::get()->first();
    }
}