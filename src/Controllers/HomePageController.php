<?php

namespace Kalakotra\Baudigit\Controllers;

class HomePageController extends \PageController
{
    use \Kalakotra\Baudigit\Traits\FeatureToggleTrait;

    protected function init()
    {
        parent::init();
        
        // if member is admin redirect to /uebersicht otherwise to /arbeitnehmer
        if ($this->currentUser()) {
            if ($this->currentUser()->inGroup('company-admin')) {
                return $this->redirect('/uebersicht');
            } else {
                return $this->redirect('/arbeitnehmer');
            }
        }
        
    }
}