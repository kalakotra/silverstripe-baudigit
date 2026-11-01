<?php

namespace Kalakotra\Baudigit\Controllers;

use Kalakotra\Baudigit\Models\ProjectTaskHours;

class WorkerTasksHoursPageController extends \PageController
{
    use \Kalakotra\Baudigit\Traits\FeatureToggleTrait;

    protected function init(): void
    {
        parent::init();
        if ($err = $this->checkEnabled()) {
            return;
        }
    }
    public function getActuallMonthWorkerTasksHours()
    {
        $currentUser = $this->currentUser();
        if ($currentUser) {
            $startOfMonth = date('Y-m-01');
            $endOfMonth = date('Y-m-t');

            $myHours = ProjectTaskHours::get()
                ->filter([
                    'LoggedByID' => $currentUser->ID,
                    'Created:GreaterThanOrEqual' => $startOfMonth,
                    'Created:LessThanOrEqual' => $endOfMonth,
                ]);
            return $myHours;
        }
        return null;
    }

    public function getActuallMonthWorkerTasksHoursSum()
    {
        $currentUser = $this->currentUser();
        if ($currentUser) {
            $startOfMonth = date('Y-m-01');
            $endOfMonth = date('Y-m-t');

            $myHoursSum = ProjectTaskHours::get()
                ->filter([
                    'LoggedByID' => $currentUser->ID,
                    'Created:GreaterThanOrEqual' => $startOfMonth,
                    'Created:LessThanOrEqual' => $endOfMonth,
                ])->sum('Hours');
            return $myHoursSum;
        }
        return 0;
    }
}