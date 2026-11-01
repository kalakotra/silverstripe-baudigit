<?php

namespace Kalakotra\Baudigit\Pages;

use Kalakotra\Baudigit\Utils\PagePermissions;
use Kalakotra\Baudigit\Controllers\RecurringInvoicePageController;
use SilverStripe\Security\PermissionProvider;

class RecurringInvoicePage extends \Page implements PermissionProvider
{
    use PagePermissions;

    private static $table_name = 'baudigit_RecurringInvoicePage';

    private static $singular_name = 'Wiederkehrende-Rechnung-Seite';
    private static $plural_name = 'Wiederkehrende-Rechnung-Seiten';

    // define controller for this page type
    private static $controller_name = RecurringInvoicePageController::class;

}