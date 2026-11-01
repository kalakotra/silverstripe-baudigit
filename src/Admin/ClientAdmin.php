<?php

namespace Kalakotra\Baudigit\Admin;

use SilverStripe\Admin\ModelAdmin;
use Kalakotra\Baudigit\Models\Client;
use SilverStripe\Security\Permission;

class ClientAdmin extends ModelAdmin
{
    private static $managed_models = [
        Client::class,
    ];

    private static $url_segment = 'clients';
    private static $menu_title = 'Kunden';

    public function getMenuTitle()
    {
        return _t(__CLASS__.'.MENU_TITLE', 'Kunden');
    }

    private static $menu_icon_class = 'font-icon-block-user';

    public $showImportForm = false;

    private static $menu_priority = 40;

    
}