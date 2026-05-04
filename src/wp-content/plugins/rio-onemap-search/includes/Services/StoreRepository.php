<?php
namespace Rio_OneMap_Search\Services;

use Rio_OneMap_Search\Core\StorePostType;

if (!defined('ABSPATH')) exit;

class StoreRepository
{
    public function all_active(): array
    {
        $query = new \WP_Query([
            'post_type' => StorePostType::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        ]);

        $stores = [];
        foreach ($query->posts as $post) {
            $lat = get_post_meta($post->ID, '_rio_store_lat', true);
            $lng = get_post_meta($post->ID, '_rio_store_lng', true);

            if ($lat === '' || $lng === '') {
                continue;
            }

            $stores[] = [
                'id' => $post->ID,
                'name' => get_the_title($post),
                'address' => get_post_meta($post->ID, '_rio_store_address', true),
                'postal_code' => get_post_meta($post->ID, '_rio_store_postal_code', true),
                'phone' => get_post_meta($post->ID, '_rio_store_phone', true),
                'map_url' => get_post_meta($post->ID, '_rio_store_map_url', true),
                'lat' => (float) $lat,
                'lng' => (float) $lng,
            ];
        }

        wp_reset_postdata();
        return $stores;
    }
}
