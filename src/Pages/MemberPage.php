<?php

namespace Kalakotra\Baudigit\Pages;

use Kalakotra\Baudigit\Utils\PagePermissions;
use Kalakotra\Baudigit\Controllers\MemberPageController;
use SilverStripe\Security\PermissionProvider;

class MemberPage extends \Page implements PermissionProvider
{
    use PagePermissions;

    private static $table_name = 'baudigit_MemberPage';

    private static $singular_name = 'Benutzer-Seite';
    private static $plural_name = 'Benutzer-Seiten';

    // define controller for this page type
    private static $controller_name = MemberPageController::class;
}