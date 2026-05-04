<?php
namespace Rio_OneMap_Search\Core;

if (!defined('ABSPATH')) exit;

class StorePostType
{
    public const POST_TYPE = 'rio_store';

    public function register_hooks(): void
    {
        add_action('init', [$this, 'register']);
    }

    public function register(): void
    {
        register_post_type(self::POST_TYPE, [
            'labels' => [
                'name' => __('Stores', 'rio-onemap-search'),
                'singular_name' => __('Store', 'rio-onemap-search'),
                'add_new_item' => __('Add New Store', 'rio-onemap-search'),
                'edit_item' => __('Edit Store', 'rio-onemap-search'),
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-store',
            'supports' => ['title'],
            'capability_type' => 'post',
        ]);
    }
}
