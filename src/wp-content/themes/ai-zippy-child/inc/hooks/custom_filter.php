<?php
/**
 * Custom filter block helpers.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

function ai_zippy_child_custom_filter_brand_taxonomy(): string
{
    if (taxonomy_exists('product_brand')) {
        return 'product_brand';
    }

    if (taxonomy_exists('pa_brand')) {
        return 'pa_brand';
    }

    return '';
}

function ai_zippy_child_custom_filter_allowed_taxonomy(string $taxonomy): string
{
    if ($taxonomy === 'brand') {
        return ai_zippy_child_custom_filter_brand_taxonomy();
    }

    return in_array($taxonomy, ['product_cat', 'product_tag'], true) && taxonomy_exists($taxonomy) ? $taxonomy : '';
}

function ai_zippy_child_custom_filter_get_price_bounds(): array
{
    global $wpdb;

    if (!function_exists('wc_get_price_decimals')) {
        return ['min' => 0, 'max' => 100];
    }

    $row = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT MIN(CAST(pm.meta_value AS DECIMAL(10,2))) AS min_price, MAX(CAST(pm.meta_value AS DECIMAL(10,2))) AS max_price
            FROM {$wpdb->postmeta} pm
            INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
            WHERE pm.meta_key = %s
            AND pm.meta_value <> ''
            AND p.post_type = %s
            AND p.post_status = %s",
            '_price',
            'product',
            'publish'
        ),
        ARRAY_A
    );

    $min = isset($row['min_price']) ? (float) $row['min_price'] : 0;
    $max = isset($row['max_price']) ? (float) $row['max_price'] : 100;

    if ($max <= $min) {
        $max = $min + 100;
    }

    return [
        'min' => max(0, floor($min)),
        'max' => max(1, ceil($max)),
    ];
}

function ai_zippy_child_custom_filter_build_price_ranges(int $range_count = 4): array
{
    $range_count = min(8, max(2, $range_count));
    $bounds      = ai_zippy_child_custom_filter_get_price_bounds();
    $min         = (float) $bounds['min'];
    $max         = (float) $bounds['max'];
    $step        = max(1, ceil(($max - $min) / $range_count));
    $ranges      = [];

    for ($index = 0; $index < $range_count; ++$index) {
        $start = $min + ($step * $index);
        $end   = $index === $range_count - 1 ? $max : min($max, $start + $step);

        $start_label = function_exists('wc_price') ? wc_price($start) : '$' . number_format_i18n($start, 2);
        $end_label   = function_exists('wc_price') ? wc_price($end) : '$' . number_format_i18n($end, 2);

        $ranges[] = [
            'label' => sprintf('%1$s - %2$s', $start_label, $end_label),
            'value' => (function_exists('wc_format_decimal') ? wc_format_decimal($start, 2) : number_format((float) $start, 2, '.', '')) . '-' . (function_exists('wc_format_decimal') ? wc_format_decimal($end, 2) : number_format((float) $end, 2, '.', '')),
        ];
    }

    return $ranges;
}

function ai_zippy_child_custom_filter_rest_terms(WP_REST_Request $request): array
{
    $taxonomy = ai_zippy_child_custom_filter_allowed_taxonomy(sanitize_key($request->get_param('taxonomy')));

    if ($taxonomy === '') {
        return [];
    }

    $terms = get_terms([
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
        'number'     => 200,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ]);

    if (is_wp_error($terms)) {
        return [];
    }

    return array_map(
        static fn(WP_Term $term): array => [
            'id'     => (int) $term->term_id,
            'name'   => $term->name,
            'slug'   => $term->slug,
            'parent' => (int) $term->parent,
            'count'  => (int) $term->count,
        ],
        $terms
    );
}

function ai_zippy_child_custom_filter_register_rest_routes(): void
{
    register_rest_route('ai-zippy-child/v1', '/custom-filter/terms', [
        'methods'             => 'GET',
        'callback'            => 'ai_zippy_child_custom_filter_rest_terms',
        'permission_callback' => static fn() => current_user_can('edit_posts'),
        'args'                => [
            'taxonomy' => [
                'sanitize_callback' => 'sanitize_key',
            ],
        ],
    ]);
}
add_action('rest_api_init', 'ai_zippy_child_custom_filter_register_rest_routes');
