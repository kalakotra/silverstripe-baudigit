<?php

namespace Kalakotra\Baudigit\Admin;

use Kalakotra\Baudigit\Models\Project;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Security\Permission;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;

class ProjectAdmin extends ModelAdmin
{
    private static $managed_models = [
        'planned' => [
            'dataClass' => Project::class,
            'title' => 'Neue',
        ],
        'inprogress' => [
            'dataClass' => Project::class,
            'title' => 'Laufende',
        ],
        'completed' => [
            'dataClass' => Project::class,
            'title' => 'Abgeschlossene',
        ],
        'billed' => [
            'dataClass' => Project::class,
            'title' => 'Abgerechnete',
        ],
        'canceled' => [
            'dataClass' => Project::class,
            'title' => 'Stornierte',
        ],
    ];

    private static $url_segment = 'projects';
    private static $menu_title = 'Projekte';

    public function getMenuTitle()
    {
        return _t(__CLASS__ . '.MENU_TITLE', 'Projekte');
    }

    private static $menu_icon_class = 'font-icon-p-post';

    public $showImportForm = false;

    private static $menu_priority = 50;

    public function getList()
    {
        $list = parent::getList();
        // Only show Projects specific to the planned status
        /*if ($this->modelTab === 'planned') {
            $list = $list->filter('Status', 0);
        } elseif ($this->modelTab === 'inprogress') {
            $list = $list->filter('Status', 1);
        } elseif ($this->modelTab === 'completed') {
            $list = $list->filter('Status', 2);
        } elseif ($this->modelTab === 'billed') {
            $list = $list->filter('Status', 3);
        } elseif ($this->modelTab === 'canceled') {
            $list = $list->filter('Status', 4);
        }*/
        return $list;
    }

    protected function getGridFieldConfig(): GridFieldConfig
    {
        $config = parent::getGridFieldConfig();

        if ($this->modelTab !== 'planned') {
            // remove add button
            $config->removeComponentsByType(GridFieldAddNewButton::class);
        }
        
        return $config;
    }
}