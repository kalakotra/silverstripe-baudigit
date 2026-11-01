<?php

namespace Kalakotra\Baudigit\Admin;

use SilverStripe\Admin\ModelAdmin;
use Kalakotra\Baudigit\Models\Article;
use SilverStripe\Security\Permission;

class ArticleAdmin extends ModelAdmin
{
    private static $managed_models = [
        Article::class,
    ];

    private static $url_segment = 'products';
    private static $menu_title = 'Produkte';

    public function getMenuTitle()
    {
        return _t(__CLASS__.'.MENU_TITLE', 'Produkte');
    }

    private static $menu_icon_class = 'font-icon-menu-modaladmin';

    public $showImportForm = false;

    private static $menu_priority = 40;

    
}