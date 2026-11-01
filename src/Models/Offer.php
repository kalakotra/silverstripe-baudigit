<?php

namespace Kalakotra\Baudigit\Models;

use Kalakotra\Baudigit\Models\OfferArticle;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Email\Email;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\i18n\i18nEntityProvider;
use Kalakotra\Baudigit\Utils\DataObjectPermissions;
use SilverStripe\Security\PermissionProvider;

class Offer extends DataObject implements PermissionProvider, i18nEntityProvider
{
    use DataObjectPermissions;

    private static $table_name = 'baudigit_Offer';

    private static $singular_name = 'Angebot';

    private static $plural_name = 'Angebote';

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
        'ValidUntil' => 'Date',
        'IntroText' => 'Text',
        'OutroText' => 'Text',
        'TotalNetto' => 'Decimal(19,2)',
        'TotalBrutto' => 'Decimal(19,2)',
        'Status' => 'Int', //"Enum('Draft, Sent, Accepted, Project, Rejected', 'Draft')", 0=Draft, 1=Sent, 2=Accepted, 3=Project, 4=Rejected
    ];

    private static $has_one = [
        'ClientObject' => ClientObject::class,
    ];

    private static $has_many = [
        'OfferArticles' => OfferArticle::class,
    ];

    private static $cascade_deletes = [
        'OfferArticles',
    ];

    private static $summary_fields = [
        'OfferNumber',
        'ClientObject.getFullName' => 'Kunde',
        'Created.Nice' => 'Datum',
        'ValidUntil.Nice' => 'Gültig bis',
        'Status',
    ];

    private static $field_labels = [
        'OfferNumber' => 'Angebotsnummer',
        'ClientObject.getFullName' => 'Kunde',
        'Date' => 'Datum',
        'ValidUntil' => 'Gültig bis',
        'Subject' => 'Betreff',
        'Body' => 'Inhalt',
        'TotalNetto' => 'Gesamt Netto',
        'TotalBrutto' => 'Gesamt Brutto',
        'Status' => 'Status',
        'IntroText' => 'Einleitungstext',
        'OutroText' => 'Schlusstext',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        

        return $fields;
    }

    public function getNextOfferNumber()
    {
        if (preg_match('/^(\\d{4})-(\\d{4})$/', $this->OfferNumber, $matches)) {
            $year = $matches[1];
            $number = (int)$matches[2] + 1;
            return sprintf('%s-%04d', $year, $number);
        }
        return $this->OfferNumber;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        // set offer number if not set
        if ($this->OfferNumber == '') {
            $lastOffer = Offer::get()->sort('Created', 'DESC')->first();
            if ($lastOffer && preg_match('/^(\\d{4})-(\\d{4})$/', $lastOffer->OfferNumber)) {
                $this->OfferNumber = $lastOffer->getNextOfferNumber();
            } else {
                $this->OfferNumber = date('Y') . '-0001';
            }
            //$this->OfferNumber = $lastOffer ? $lastOffer->getNextOfferNumber() : date('Y') . '-0001';
        }

    }

    public function getTotalNetto(): float|int
    {
        $total = 0;
        foreach ($this->OfferArticles() as $article) {
            $total += $article->getDiscountedPrice();
        }
        return $total;
    }

    public function getTotalTax(): float|int
    {
        $total = 0;
        foreach ($this->OfferArticles() as $article) {
            $total += $article->getTaxAmount();
        }
        return $total;
    }

    public function getTotalBrutto(): float|int
    {
        return $this->getTotalNetto() + $this->getTotalTax();
    }

    public function getGermanStatus() {
        return match ($this->Status) {
            0 => 'Entwurf',
            1 => 'Gesendet',
            2 => 'Akzeptiert',
            3 => 'Projekt',
            4 => 'Abgelehnt',
            default => 'Unbekannt',
        };
    }

    public function getGermanStatusOptions(): array
    {
        return [
            0 => 'Entwurf',
            1 => 'Gesendet',
            2 => 'Akzeptiert',
           /// 3 => 'Projekt',
            4 => 'Abgelehnt',
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

    public function getPDF() {
        $controller = Controller::curr();
        $html = $controller->customise(['Offer' => $this])->renderWith('app/Pages/Layout/PDF/Offer');
        $filename = "angebot-".$this->ID.".pdf";
        $pdfPath = $controller->generatePDF($html, $filename);

        $controller->getResponse()->addHeader('Content-Type', 'application/pdf');
        $controller->getResponse()->addHeader('Content-Disposition', 'inline; filename="'.$filename.'"');

        return file_get_contents($pdfPath);
    }

    public function sendOfferEmail() {
        $client = $this->ClientObject()->Client();
        if ($client && $client->Email) {
            $config = SiteConfig::current_site_config();
            $pdfContent = $this->getPDF();
            $email = Email::create();
            $email->setTo($client->Email);
            $email->setFrom($config->CompanyContactEmail);
            $email->setSubject('Angebot ' . $this->OfferNumber);
            $email->setBody(DBField::create_field('HTMLText', $config->EmailOfferText));
            $email->addAttachmentFromData($pdfContent, 'Angebot-' . $this->OfferNumber . '.pdf', 'application/pdf');
            $email->sendPlain();
            //$this->Status = 1; //Sent
            //$this->write();
            return true;
        }
        return false;
    }

}