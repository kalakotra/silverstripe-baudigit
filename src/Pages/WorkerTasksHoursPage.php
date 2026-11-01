<?php

namespace Kalakotra\Baudigit\Pages;

use Kalakotra\Baudigit\Controllers\WorkerTasksHoursPageController;

class WorkerTasksHoursPage extends \Page
{
    private static $table_name = 'baudigit_WorkerTasksHoursPage';

    private static $singular_name = 'Mitarbeiter Aufgaben Stunden Seite';

    private static $plural_name = 'Mitarbeiter Aufgaben Stunden Seiten';

    private static $description = 'Seite für Mitarbeiter um ihre Arbeitsstunden zu verwalten';

    private static $controller_name = WorkerTasksHoursPageController::class;
}