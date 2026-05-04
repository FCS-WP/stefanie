<?php
/**
 * Plugin Name: Rio-OneMap Search
 * Description: Singapore postal code search using OneMap API. Finds and highlights nearest configured store.
 * Version: 1.0.4
 * Author: Rio
 * Text Domain: rio-onemap-search
 */

if (!defined('ABSPATH')) {
    exit;
}

define('RIO_ONEMAP_SEARCH_VERSION', '1.0.4');
define('RIO_ONEMAP_SEARCH_FILE', __FILE__);
define('RIO_ONEMAP_SEARCH_PATH', plugin_dir_path(__FILE__));
define('RIO_ONEMAP_SEARCH_URL', plugin_dir_url(__FILE__));

require_once RIO_ONEMAP_SEARCH_PATH . 'includes/Core/Plugin.php';
require_once RIO_ONEMAP_SEARCH_PATH . 'includes/Core/Installer.php';
require_once RIO_ONEMAP_SEARCH_PATH . 'includes/Core/StorePostType.php';
require_once RIO_ONEMAP_SEARCH_PATH . 'includes/Admin/StoreMetaBox.php';
require_once RIO_ONEMAP_SEARCH_PATH . 'includes/Admin/SettingsPage.php';
require_once RIO_ONEMAP_SEARCH_PATH . 'includes/Services/OneMapTokenService.php';
require_once RIO_ONEMAP_SEARCH_PATH . 'includes/Services/DistanceService.php';
require_once RIO_ONEMAP_SEARCH_PATH . 'includes/Services/StoreRepository.php';
require_once RIO_ONEMAP_SEARCH_PATH . 'includes/Services/OneMapClient.php';
require_once RIO_ONEMAP_SEARCH_PATH . 'includes/Services/AddressLogRepository.php';
require_once RIO_ONEMAP_SEARCH_PATH . 'includes/Ajax/SearchAjax.php';
require_once RIO_ONEMAP_SEARCH_PATH . 'includes/Frontend/Shortcode.php';

register_activation_hook(__FILE__, ['Rio_OneMap_Search\\Core\\Installer', 'activate']);

add_action('plugins_loaded', function () {
    Rio_OneMap_Search\Core\Plugin::instance()->boot();
});
