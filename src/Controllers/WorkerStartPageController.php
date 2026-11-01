<?php

namespace Kalakotra\Baudigit\Controllers;

use Kalakotra\Baudigit\Models\ProjectTask;

class WorkerStartPageController extends \PageController
{
    use \Kalakotra\Baudigit\Traits\FeatureToggleTrait;

    protected function init(): void
    {
        parent::init();
        if ($err = $this->checkEnabled()) {
            return;
        }
    }

    
}