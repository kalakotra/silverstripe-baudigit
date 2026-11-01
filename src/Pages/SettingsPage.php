<?php

namespace Kalakotra\Baudigit\Pages;

use Kalakotra\Baudigit\Utils\PagePermissions;
use Kalakotra\Baudigit\Controllers\SettingsPageController;
use SilverStripe\Security\PermissionProvider;

class SettingsPage extends \Page implements PermissionProvider
{
    use PagePermissions;

    private static $table_name = 'baudigit_SettingsPage';

    private static $singular_name = 'Einstellungen-Seite';
    private static $plural_name = 'Einstellungen-Seiten';

    // define controller for this page type
    private static $controller_name = SettingsPageController::class;

}