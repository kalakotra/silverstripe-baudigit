<?php

declare(strict_types=1);

namespace Kalakotra\Baudigit\Controllers;

use Kalakotra\Baudigit\Models\Invoice;
use Kalakotra\Baudigit\Models\Offer;
use Kalakotra\Baudigit\Models\Client;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\FieldType\DBField;

class DashboardPageController extends \PageController
{
    use \Kalakotra\Baudigit\Traits\FeatureToggleTrait;

    protected function init(): void
    {
        parent::init();
        if ($err = $this->checkEnabled()) {
            return;
        }
    }
    // ── Rechnungen ────────────────────────────────────────────────────────────────

    /** Alle offenen Rechnungen (Status = 1, Gesendet, nicht bezahlt) */
    public function getOpenInvoices()
    {
        return Invoice::get()->filter('Status', 1)->sort('DueDate', 'ASC');
    }

    /** Überfällige Rechnungen (Status = 1 und DueDate < heute) */
    public function getOverdueInvoices()
    {
        return Invoice::get()->filter([
            'Status'                  => 1,
            'DueDate:LessThan'        => date('Y-m-d'),
        ])->sort('DueDate', 'ASC');
    }

    /** Summe aller offenen Rechnungen (Brutto) */
    public function getOpenInvoicesTotal(): string
    {
        $total = 0.0;
        foreach ($this->getOpenInvoices() as $inv) {
            $total += (float) $inv->getTotalBrutto(true);
        }
        return '€ ' . number_format($total, 2, ',', '.');
    }

    /** Summe aller überfälligen Rechnungen (Brutto) */
    public function getOverdueInvoicesTotal(): string
    {
        $total = 0.0;
        foreach ($this->getOverdueInvoices() as $inv) {
            $total += (float) $inv->getTotalBrutto(true);
        }
        return '€ ' . number_format($total, 2, ',', '.');
    }

    public function getOpenInvoicesCount(): int
    {
        return (int) $this->getOpenInvoices()->count();
    }

    public function getOverdueInvoicesCount(): int
    {
        return (int) $this->getOverdueInvoices()->count();
    }

    /** Diesen Monat bezahlte Rechnungen (Status = 2) */
    public function getPaidThisMonthTotal(): string
    {
        $from  = date('Y-m-01');
        $until = date('Y-m-t');
        $total = 0.0;
        foreach (Invoice::get()->filter(['Status' => 2, 'LastEdited:GreaterThanOrEqual' => $from, 'LastEdited:LessThanOrEqual' => $until . ' 23:59:59']) as $inv) {
            $total += (float) $inv->getTotalBrutto(true);
        }
        return '€ ' . number_format($total, 2, ',', '.');
    }

    // ── Angebote ──────────────────────────────────────────────────────────────────

    public function getOpenOffersCount(): int
    {
        return (int) Offer::get()->filter('Status', 0)->count();
    }

    // ── Kunden ────────────────────────────────────────────────────────────────────

    public function getTotalClientsCount(): int
    {
        return (int) Client::get()->count();
    }

    // ── Offene Rechnungen für Tabelle (top 10 nach Fälligkeit) ───────────────────

    public function getTopOpenInvoices()
    {
        return $this->getOpenInvoices()->limit(10);
    }

    /** Heutiges Datum als Y-m-d String — für Template-Vergleich mit DueDate */
    public function getToday(): string
    {
        return date('Y-m-d');
    }

    public function getFirstInvoicePage()
    {
        return \app\Pages\InvoicePage::get()->first();
    }
}
