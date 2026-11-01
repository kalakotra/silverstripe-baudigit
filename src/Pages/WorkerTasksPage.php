<?php

namespace Kalakotra\Baudigit\Pages;

use Kalakotra\Baudigit\Utils\PagePermissions;
use SilverStripe\Security\PermissionProvider;
use Kalakotra\Baudigit\Controllers\WorkerTasksPageController;

class WorkerTasksPage extends \Page implements PermissionProvider
{
    use PagePermissions;

    private static $table_name = 'baudigit_WorkerTasksPage';

    private static $singular_name = 'Mitarbeiter Aufgaben Seite';

    private static $plural_name = 'Mitarbeiter Aufgaben Seiten';

    private static $description = 'Seite für Mitarbeiter um ihre Aufgaben zu verwalten';

    private static $controller_name = WorkerTasksPageController::class;
    
}