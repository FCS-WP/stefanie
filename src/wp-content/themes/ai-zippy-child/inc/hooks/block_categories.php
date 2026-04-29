<?php
/**
 * Child theme block categories.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

/**
 * Register child block categories without removing the parent AI Zippy group.
 */
function ai_zippy_child_register_block_categories(array $categories): array
{
    $existing_slugs = array_column($categories, 'slug');
    $child_categories = [
        [
            'slug'  => 'ai-zippy-site',
            'title' => __('AI Zippy Site', 'ai-zippy-child'),
            'icon'  => 'admin-site',
        ],
        [
            'slug'  => 'ai-zippy-page',
            'title' => __('AI Zippy Page', 'ai-zippy-child'),
            'icon'  => 'layout',
        ],
    ];

    foreach (array_reverse($child_categories) as $category) {
        if (!in_array($category['slug'], $existing_slugs, true)) {
            array_unshift($categories, $category);
        }
    }

    return $categories;
}
add_filter('block_categories_all', 'ai_zippy_child_register_block_categories', 20);
