<?php
namespace Rio_OneMap_Search\Admin;

use Rio_OneMap_Search\Core\StorePostType;

if (!defined('ABSPATH')) exit;

class StoreMetaBox
{
    public function register_hooks(): void
    {
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_' . StorePostType::POST_TYPE, [$this, 'save'], 10, 2);
    }

    public function add_meta_boxes(): void
    {
        add_meta_box(
            'rio_store_location',
            __('Store Location', 'rio-onemap-search'),
            [$this, 'render'],
            StorePostType::POST_TYPE,
            'normal',
            'high'
        );
    }

    public function render(\WP_Post $post): void
    {
        wp_nonce_field('rio_store_location_save', 'rio_store_location_nonce');

        $fields = [
            'address' => get_post_meta($post->ID, '_rio_store_address', true),
            'postal_code' => get_post_meta($post->ID, '_rio_store_postal_code', true),
            'lat' => get_post_meta($post->ID, '_rio_store_lat', true),
            'lng' => get_post_meta($post->ID, '_rio_store_lng', true),
            'phone' => get_post_meta($post->ID, '_rio_store_phone', true),
            'map_url' => get_post_meta($post->ID, '_rio_store_map_url', true),
        ];
        ?>
        <p><label><strong>Address</strong></label><br>
            <textarea name="rio_store_address" rows="3" style="width:100%;"><?php echo esc_textarea($fields['address']); ?></textarea>
        </p>
        <p><label><strong>Postal Code</strong></label><br>
            <input type="text" name="rio_store_postal_code" value="<?php echo esc_attr($fields['postal_code']); ?>" class="regular-text">
        </p>
        <p><label><strong>Latitude</strong></label><br>
            <input type="text" name="rio_store_lat" value="<?php echo esc_attr($fields['lat']); ?>" class="regular-text" placeholder="1.3521">
        </p>
        <p><label><strong>Longitude</strong></label><br>
            <input type="text" name="rio_store_lng" value="<?php echo esc_attr($fields['lng']); ?>" class="regular-text" placeholder="103.8198">
        </p>
        <p><label><strong>Phone</strong></label><br>
            <input type="text" name="rio_store_phone" value="<?php echo esc_attr($fields['phone']); ?>" class="regular-text">
        </p>
        <p><label><strong>Map URL</strong></label><br>
            <input type="url" name="rio_store_map_url" value="<?php echo esc_url($fields['map_url']); ?>" class="large-text" placeholder="https://maps.google.com/...">
        </p>
        <p class="description">Use OneMap Search to find latitude/longitude for each store, then save them here.</p>
        <?php
    }

    public function save(int $post_id, \WP_Post $post): void
    {
        if (!isset($_POST['rio_store_location_nonce']) || !wp_verify_nonce($_POST['rio_store_location_nonce'], 'rio_store_location_save')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;

        update_post_meta($post_id, '_rio_store_address', sanitize_textarea_field($_POST['rio_store_address'] ?? ''));
        update_post_meta($post_id, '_rio_store_postal_code', sanitize_text_field($_POST['rio_store_postal_code'] ?? ''));
        update_post_meta($post_id, '_rio_store_lat', sanitize_text_field($_POST['rio_store_lat'] ?? ''));
        update_post_meta($post_id, '_rio_store_lng', sanitize_text_field($_POST['rio_store_lng'] ?? ''));
        update_post_meta($post_id, '_rio_store_phone', sanitize_text_field($_POST['rio_store_phone'] ?? ''));
        update_post_meta($post_id, '_rio_store_map_url', esc_url_raw($_POST['rio_store_map_url'] ?? ''));
    }
}
