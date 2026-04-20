<?php
/**
 * Header cart fragments.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

function ai_zippy_child_header_cart_count_markup(): string
{
    $cart_count = function_exists('WC') && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;

    return sprintf(
        '<span class="site-header__cart-count%1$s" data-site-header-cart-count>%2$s</span>',
        $cart_count > 0 ? '' : ' is-empty',
        esc_html((string) $cart_count)
    );
}

function ai_zippy_child_header_cart_fragments(array $fragments): array
{
    $fragments['[data-site-header-cart-count]'] = ai_zippy_child_header_cart_count_markup();

    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'ai_zippy_child_header_cart_fragments');
