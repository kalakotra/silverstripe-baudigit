<?php

declare(strict_types=1);

namespace Kalakotra\Baudigit\Traits;

/**
 * Shared behaviour for document line-item classes
 * (InvoiceArticle, OfferArticle, RecurringInvoiceArticle).
 *
 * Provides:
 *  - DB field declarations (via $lineItemDb) — merge into $db in each class
 *  - Unit label helpers
 *  - Price calculation helpers
 *  - FinalPrice computation in onBeforeWrite
 *  - formatPrice helper
 */
trait LineItemTrait
{
    // ─── DB columns every line-item needs ────────────────────────────────────
    // Merge these into $db in the consuming class.
    protected static array $lineItemDb = [
        'Title'          => 'Varchar(255)',
        'Description'    => 'Text',
        'Unit'           => 'Int',
        'BuyPriceNetto'  => 'Decimal(19,2)',
        'SellPriceNetto' => 'Decimal(19,2)',
        'Quantity'       => 'Decimal(10,2)',
        'Discount'       => 'Decimal(10,2)',
        'TaxRate'        => 'Decimal(10,2)',
        'FinalPrice'     => 'Decimal(19,2)',
    ];

    // ─── Unit helpers ─────────────────────────────────────────────────────────

    public function getUnitOptions(): array
    {
        return [
            0 => 'Stück',
            1 => 'm',
            2 => 'm²',
            3 => 'm³',
            4 => 'l',
            5 => 'kg',
            6 => 'Stunde',
            7 => 'Sonstige',
        ];
    }

    public function getUnitLabel(): string
    {
        return $this->getUnitOptions()[$this->Unit] ?? 'Unbekannt';
    }

    /** BT-130 unit code */
    public function getUnitShortLabel(): string
    {
        return [
            0 => 'STK',
            1 => 'M',
            2 => 'M2',
            3 => 'M3',
            4 => 'L',
            5 => 'KG',
            6 => 'STD',
            7 => 'SNS',
        ][$this->Unit] ?? 'UNB';
    }

    // ─── Price helpers ────────────────────────────────────────────────────────

    public function getDiscountedPrice(): float
    {
        $net      = (float) $this->SellPriceNetto * (float) $this->Quantity;
        $discount = $net * ((float) $this->Discount / 100);
        return $net - $discount;
    }

    public function getDiscountAmount(): float
    {
        $net = (float) $this->SellPriceNetto * (float) $this->Quantity;
        return $net * ((float) $this->Discount / 100);
    }

    public function getTaxAmount(): float
    {
        return $this->getDiscountedPrice() * ((float) $this->TaxRate / 100);
    }

    public function getBruttoPrice(): float
    {
        return $this->getDiscountedPrice() + $this->getTaxAmount();
    }

    public function formatPrice(float $price, bool $withCurrency = true): string
    {
        $formatted = number_format($price, 2, ',', '.') . ' €';
        return $withCurrency ? $formatted : rtrim($formatted, ' €');
    }

    // ─── onBeforeWrite hook (call parent::onBeforeWrite() first) ─────────────

    protected function computeFinalPrice(): void
    {
        $net              = (float) $this->SellPriceNetto * (float) $this->Quantity;
        $afterDiscount    = $net - $net * ((float) $this->Discount / 100);
        $this->FinalPrice = $afterDiscount + $afterDiscount * ((float) $this->TaxRate / 100);
    }
}
