<?php

namespace Kalakotra\Baudigit\Pages;

use Kalakotra\Baudigit\Utils\PagePermissions;
use Kalakotra\Baudigit\Controllers\DashboardPageController;
use SilverStripe\Security\PermissionProvider;

class DashboardPage extends \Page implements PermissionProvider
{
    use PagePermissions;

    private static $table_name = 'baudigit_DashboardPage';

    private static $singular_name = 'Dashboard-Seite';
    private static $plural_name = 'Dashboard-Seiten';

    // define controller for this page type
    private static $controller_name = DashboardPageController::class;

}