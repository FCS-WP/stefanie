<?php
/**
 * Shop archive SEO helpers.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

function ai_zippy_child_shop_archive_meta_description(): void
{
    if (!function_exists('is_shop') || (!is_shop() && !is_product_taxonomy())) {
        return;
    }

    echo "\n" . '<meta name="description" content="' . esc_attr__('Shop Bubble, Skip Hop, Micro & more — islandwide delivery', 'ai-zippy-child') . '">' . "\n";
}
add_action('wp_head', 'ai_zippy_child_shop_archive_meta_description', 2);
