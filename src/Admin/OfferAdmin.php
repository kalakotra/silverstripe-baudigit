<?php

namespace Kalakotra\Baudigit\Admin;

use Kalakotra\Baudigit\Models\Offer;
use Kalakotra\Baudigit\Models\Invoice;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Security\Permission;

class OfferAdmin extends ModelAdmin
{
    private static $managed_models = [
        Offer::class,
        Invoice::class
    ];

    private static $url_segment = 'angebote';

    private static $menu_title = 'Angebote';

    public function init()
    {
        parent::init();

        if (!Permission::check('ADMIN')) {
            $this->getList()->removeByID($this->ID);
        }
    }
}