<?php

namespace Kalakotra\Baudigit\Pages;

use Kalakotra\Baudigit\Utils\PagePermissions;
use SilverStripe\Security\PermissionProvider;

class WorkerStartPage extends \Page implements PermissionProvider
{
    use PagePermissions;
    private static $table_name = 'baudigit_WorkerStartPage';

    private static $singular_name = 'Mitarbeiter Startseite';

    private static $plural_name = 'Mitarbeiter Startseiten';

    private static $description = 'Startseite für Mitarbeiter nach dem Login';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        return $fields;
    }
}