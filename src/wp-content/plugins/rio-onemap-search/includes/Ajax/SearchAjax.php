<?php
namespace Rio_OneMap_Search\Ajax;

use Rio_OneMap_Search\Services\AddressLogRepository;
use Rio_OneMap_Search\Services\DistanceService;
use Rio_OneMap_Search\Services\OneMapClient;
use Rio_OneMap_Search\Services\StoreRepository;

if (!defined('ABSPATH')) exit;

class SearchAjax
{
    public function register_hooks(): void
    {
        add_action('wp_ajax_rio_onemap_search', [$this, 'search']);
        add_action('wp_ajax_nopriv_rio_onemap_search', [$this, 'search']);
        add_action('wp_ajax_rio_onemap_save_address', [$this, 'save_address']);
        add_action('wp_ajax_nopriv_rio_onemap_save_address', [$this, 'save_address']);
    }

    public function search(): void
    {
        check_ajax_referer('rio_onemap_search_nonce', 'nonce');

        $postal_code = sanitize_text_field($_POST['postal_code'] ?? '');
        if (!preg_match('/^[0-9]{6}$/', $postal_code)) {
            wp_send_json_error(['message' => 'Please enter a valid 6-digit Singapore postal code.']);
        }

        $client = new OneMapClient();
        $search = $client->search($postal_code);

        if (!$search['success']) {
            wp_send_json_error(['message' => $search['message']]);
        }

        $stores = (new StoreRepository())->all_active();
        if (empty($stores)) {
            wp_send_json_error(['message' => 'No stores configured.']);
        }

        $distance = new DistanceService();
        $results = [];

        foreach ($search['results'] as $address) {
            $nearest = $distance->nearest((float) $address['lat'], (float) $address['lng'], $stores);
            $results[] = [
                'address' => $address,
                'nearest_store' => $nearest,
            ];
        }

        wp_send_json_success([
            'results' => $results,
            'save_enabled' => (int) get_option('rio_onemap_save_addresses', 0) === 1,
        ]);
    }

    public function save_address(): void
    {
        check_ajax_referer('rio_onemap_search_nonce', 'nonce');

        $saved = (new AddressLogRepository())->create([
            'postal_code' => sanitize_text_field($_POST['postal_code'] ?? ''),
            'selected_address' => sanitize_textarea_field($_POST['selected_address'] ?? ''),
            'selected_lat' => (float) ($_POST['selected_lat'] ?? 0),
            'selected_lng' => (float) ($_POST['selected_lng'] ?? 0),
            'nearest_store_id' => absint($_POST['nearest_store_id'] ?? 0),
            'nearest_store_name' => sanitize_text_field($_POST['nearest_store_name'] ?? ''),
            'distance_km' => (float) ($_POST['distance_km'] ?? 0),
        ]);

        if (!$saved) {
            wp_send_json_error(['message' => 'Address saving is disabled or failed.']);
        }

        wp_send_json_success(['message' => 'Address saved.']);
    }
}
