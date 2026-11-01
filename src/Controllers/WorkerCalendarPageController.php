<?php

namespace Kalakotra\Baudigit\Controllers;

use Kalakotra\Baudigit\Models\ProjectTask;

class WorkerCalendarPageController extends \PageController
{
    use \Kalakotra\Baudigit\Traits\FeatureToggleTrait;

    protected function init(): void
    {
        parent::init();
        if ($err = $this->checkEnabled()) {
            return;
        }
    }
    private static $allowed_actions = [
        // Define allowed actions here if needed
    ];

    // get all asigned ProjectTasks for the logged in worker
    public function getAssignedTasks()
    {
        $currentUser = $this->currentUser();
        if ($currentUser) {
            $myTasks = ProjectTask::get()
                ->filter([
                    //'AssignedTo.ID' => $currentUser->ID,
                    'Status:not' => '2' // exclude completed tasks
                ]);
            return $myTasks;
        }
        return null;
    }
}