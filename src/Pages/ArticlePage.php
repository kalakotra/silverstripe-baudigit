<?php

namespace Kalakotra\Baudigit\Pages;

use Kalakotra\Baudigit\Models\Article;
use Kalakotra\Baudigit\Utils\PagePermissions;
use Kalakotra\Baudigit\Controllers\ArticlePageController;
use SilverStripe\Security\PermissionProvider;

class ArticlePage extends \Page implements PermissionProvider
{
    use PagePermissions;

    private static $table_name = 'baudigit_ArticlePage';

    private static $singular_name = 'Artikel-Seite';
    private static $plural_name = 'Artikel-Seiten';

    // define controller for this page type
    private static $controller_name = ArticlePageController::class;


}