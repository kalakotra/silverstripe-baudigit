<?php

namespace Kalakotra\Baudigit\Pages;

use Kalakotra\Baudigit\Utils\PagePermissions;
use Kalakotra\Baudigit\Controllers\ClientPageController;
use SilverStripe\Security\PermissionProvider;

class ClientPage extends \Page implements PermissionProvider
{
    use PagePermissions;

    private static $table_name = 'baudigit_ClientPage';

    private static $singular_name = 'Kunden-Seite';
    private static $plural_name = 'Kunden-Seiten';

    // define controller for this page type
    private static $controller_name = ClientPageController::class;
}