<?php

namespace Kalakotra\Baudigit\Pages;

use Kalakotra\Baudigit\Utils\PagePermissions;
use Kalakotra\Baudigit\Controllers\HomePageController;
use SilverStripe\Security\PermissionProvider;

class HomePage extends \Page implements PermissionProvider
{
    use PagePermissions;
    
    private static $table_name = 'baudigit_HomePage';

    private static $singular_name = 'Startseite';
    private static $plural_name = 'Startseiten';

    private static $controller_name = HomePageController::class;
}