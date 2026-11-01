<?php

declare(strict_types=1);

namespace Kalakotra\Baudigit\Models;

use Kalakotra\Baudigit\Traits\LineItemTrait;
use SilverStripe\ORM\DataObject;
use Kalakotra\Baudigit\Utils\DataObjectPermissions;
use SilverStripe\Security\PermissionProvider;

class RecurringInvoiceArticle extends DataObject implements PermissionProvider
{
    use DataObjectPermissions;
    use LineItemTrait;

    private static string $table_name = 'app_RecurringInvoiceArticle';

    private static string $singular_name = 'Artikel (Wiederkehrend)';
    private static string $plural_name   = 'Artikel (Wiederkehrend)';

    private static array $db = [
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

    private static array $has_one = [
        'RecurringInvoice' => RecurringInvoice::class,
        'SourceArticle'    => Article::class,
    ];

    public function onBeforeWrite(): void
    {
        parent::onBeforeWrite();
        $this->computeFinalPrice();
    }
}
