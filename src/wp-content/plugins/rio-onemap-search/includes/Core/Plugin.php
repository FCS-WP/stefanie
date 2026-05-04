<?php
namespace Rio_OneMap_Search\Core;

use Rio_OneMap_Search\Admin\SettingsPage;
use Rio_OneMap_Search\Admin\StoreMetaBox;
use Rio_OneMap_Search\Ajax\SearchAjax;
use Rio_OneMap_Search\Frontend\Shortcode;

if (!defined('ABSPATH')) exit;

class Plugin
{
    private static ?Plugin $instance = null;

    public static function instance(): Plugin
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function boot(): void
    {
        (new StorePostType())->register_hooks();
        (new StoreMetaBox())->register_hooks();
        (new SettingsPage())->register_hooks();
        (new SearchAjax())->register_hooks();
        (new Shortcode())->register_hooks();
    }
}
