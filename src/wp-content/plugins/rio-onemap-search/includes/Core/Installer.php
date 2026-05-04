<?php
namespace Rio_OneMap_Search\Core;

if (!defined('ABSPATH')) exit;

class Installer
{
    public static function activate(): void
    {
        self::create_tables();
        (new StorePostType())->register();
        flush_rewrite_rules();
    }

    private static function create_tables(): void
    {
        global $wpdb;

        $table = $wpdb->prefix . 'rio_onemap_address_logs';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            postal_code VARCHAR(20) NOT NULL,
            selected_address TEXT NOT NULL,
            selected_lat DECIMAL(16, 12) NOT NULL,
            selected_lng DECIMAL(16, 12) NOT NULL,
            nearest_store_id BIGINT UNSIGNED NULL,
            nearest_store_name VARCHAR(255) NULL,
            distance_km DECIMAL(10, 4) NULL,
            user_ip VARCHAR(100) NULL,
            user_agent TEXT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY postal_code (postal_code),
            KEY nearest_store_id (nearest_store_id),
            KEY created_at (created_at)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
}
