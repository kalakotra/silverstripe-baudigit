<?php

namespace Kalakotra\Baudigit\Models;

use SilverStripe\ORM\DataObject;

class InvoiceEmailLog extends DataObject
{
    private static $table_name = 'baudigit_InvoiceEmailLog';

    private static $singular_name = 'E-Mail Log';
    private static $plural_name   = 'E-Mail Logs';

    private static $db = [
        'Type'      => "Enum('invoice,reminder', 'invoice')", // invoice = Rechnung, reminder = Zahlungserinnerung
        'SentTo'    => 'Varchar(255)',
        'SentAt'    => 'DBDatetime',
        'Success'   => 'Boolean',
    ];

    private static $has_one = [
        'Invoice' => Invoice::class,
    ];

    private static $default_sort = 'SentAt DESC';

    private static $summary_fields = [
        'SentAt.Nice'    => 'Gesendet am',
        'getTypeLabel'   => 'Typ',
        'SentTo'         => 'Empfänger',
        'getSuccessBadge' => 'Status',
    ];

    public function getTypeLabel(): string
    {
        return match ($this->Type) {
            'reminder' => 'Zahlungserinnerung',
            default    => 'Rechnung',
        };
    }

    public function getSuccessBadge(): string
    {
        return $this->Success ? '✓ Gesendet' : '✗ Fehler';
    }
}
