<?php
namespace Rio_OneMap_Search\Services;

if (!defined('ABSPATH')) exit;

class AddressLogRepository
{
    public function create(array $data): bool
    {
        if ((int) get_option('rio_onemap_save_addresses', 0) !== 1) {
            return false;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'rio_onemap_address_logs';

        return (bool) $wpdb->insert($table, [
            'postal_code' => sanitize_text_field($data['postal_code'] ?? ''),
            'selected_address' => sanitize_textarea_field($data['selected_address'] ?? ''),
            'selected_lat' => (float) ($data['selected_lat'] ?? 0),
            'selected_lng' => (float) ($data['selected_lng'] ?? 0),
            'nearest_store_id' => isset($data['nearest_store_id']) ? absint($data['nearest_store_id']) : null,
            'nearest_store_name' => sanitize_text_field($data['nearest_store_name'] ?? ''),
            'distance_km' => isset($data['distance_km']) ? (float) $data['distance_km'] : null,
            'user_ip' => sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? ''),
            'user_agent' => sanitize_textarea_field($_SERVER['HTTP_USER_AGENT'] ?? ''),
            'created_at' => current_time('mysql'),
        ]);
    }
}
