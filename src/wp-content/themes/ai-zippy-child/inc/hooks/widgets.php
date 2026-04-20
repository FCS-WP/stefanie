<?php
/**
 * Widget areas for the child theme.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

function ai_zippy_child_register_widget_areas(): void
{
    register_sidebar([
        'name'          => __('Shop Pill Bar', 'ai-zippy-child'),
        'id'            => 'shop-pill-bar',
        'description'   => __('Add shop category pill links or blocks here.', 'ai-zippy-child'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ]);

    register_sidebar([
        'name'          => __('Shop Filter Sidebar', 'ai-zippy-child'),
        'id'            => 'shop-filter-sidebar',
        'description'   => __('Add shop filter blocks or widgets here.', 'ai-zippy-child'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ]);

    register_sidebar([
        'name'          => __('Header Widgets', 'ai-zippy-child'),
        'id'            => 'header-widgets',
        'description'   => __('Optional widgets for the site header area.', 'ai-zippy-child'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ]);

    register_sidebar([
        'name'          => __('Footer Widgets', 'ai-zippy-child'),
        'id'            => 'footer-widgets',
        'description'   => __('Optional widgets for the site footer area.', 'ai-zippy-child'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ]);
}
add_action('widgets_init', 'ai_zippy_child_register_widget_areas');
