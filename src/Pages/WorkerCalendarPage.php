<?php 

namespace Kalakotra\Baudigit\Pages;

use Kalakotra\Baudigit\Controllers\WorkerCalendarPageController;

class WorkerCalendarPage extends \Page
{
    private static $table_name = 'baudigit_WorkerCalendarPage';

    private static $singular_name = 'Mitarbeiter Kalender Seite';

    private static $plural_name = 'Mitarbeiter Kalender Seiten';

    private static $description = 'Seite für den Mitarbeiter Kalender mit Aufgabenübersicht';

    private static $controller_name = WorkerCalendarPageController::class;

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        return $fields;
    }

}