<?php
/**
 * Custom Filter block render.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

$attrs = wp_parse_args($attributes ?? [], [
    'title'      => __('Filter', 'ai-zippy-child'),
    'actionUrl'  => '',
    'submitText' => __('Apply Filters', 'ai-zippy-child'),
    'clearText'  => __('Clear', 'ai-zippy-child'),
    'showClear'  => true,
    'sections'   => [
        [
            'id'           => 'price',
            'title'        => __('Price', 'ai-zippy-child'),
            'source'       => 'price',
            'open'         => true,
            'rangeCount'   => 4,
            'includeIds'   => [],
            'excludeIds'   => [],
            'hierarchical' => false,
        ],
    ],
]);

$shop_url = trim((string) $attrs['actionUrl']);

if ($shop_url === '') {
    $shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
}

$wrapper_attributes = get_block_wrapper_attributes(['class' => 'custom-filter']);

$get_current_values = static function (string $key): array {
    if (!isset($_GET[$key])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        return [];
    }

    $value = wp_unslash($_GET[$key]); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $items = is_array($value) ? $value : [$value];
    $items = array_reduce(
        $items,
        static function (array $carry, $item): array {
            return array_merge($carry, explode(',', (string) $item));
        },
        []
    );

    return array_values(array_filter(array_map('sanitize_text_field', $items)));
};

$get_terms_for_section = static function (array $section): array {
    $source = sanitize_key($section['source'] ?? '');
    $taxonomy = function_exists('ai_zippy_child_custom_filter_allowed_taxonomy')
        ? ai_zippy_child_custom_filter_allowed_taxonomy($source)
        : '';

    if ($taxonomy === '') {
        return [];
    }

    $include_ids = array_values(array_filter(array_map('absint', (array) ($section['includeIds'] ?? []))));
    $exclude_ids = array_values(array_filter(array_map('absint', (array) ($section['excludeIds'] ?? []))));
    $args = [
        'taxonomy'   => $taxonomy,
        'hide_empty' => true,
        'number'     => 0,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ];

    if ($include_ids) {
        $args['include'] = $include_ids;
        $args['orderby'] = 'include';
    }

    if ($exclude_ids) {
        $args['exclude'] = $exclude_ids;
    }

    $terms = get_terms($args);

    return is_wp_error($terms) ? [] : $terms;
};

$render_terms = static function (array $terms, array $section, string $name, array $current_values, string $id_prefix, int $parent = 0, int $level = 0) use (&$render_terms): void {
    $hierarchical = !empty($section['hierarchical']) && sanitize_key($section['source'] ?? '') === 'product_cat';

    foreach ($terms as $term) {
        if ($hierarchical && (int) $term->parent !== $parent) {
            continue;
        }

        if (!$hierarchical && $parent !== 0) {
            continue;
        }
        ?>
        <label class="custom-filter__option" style="<?php echo esc_attr('--filter-level:' . $level); ?>" for="<?php echo esc_attr($id_prefix . '-' . $term->slug); ?>">
            <input id="<?php echo esc_attr($id_prefix . '-' . $term->slug); ?>" type="checkbox" data-custom-filter-name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($term->slug); ?>" <?php checked(in_array($term->slug, $current_values, true)); ?> />
            <span><?php echo esc_html($term->name); ?></span>
        </label>
        <?php

        if ($hierarchical) {
            $render_terms($terms, $section, $name, $current_values, $id_prefix, (int) $term->term_id, $level + 1);
        }
    }
};

$sections = is_array($attrs['sections']) ? $attrs['sections'] : [];
?>

<form <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> action="<?php echo esc_url($shop_url); ?>" method="get">
    <?php if (trim((string) $attrs['title']) !== '') : ?>
        <h2 class="custom-filter__title"><?php echo esc_html($attrs['title']); ?></h2>
    <?php endif; ?>

    <?php foreach ($sections as $section_index => $section) : ?>
        <?php
        if (!is_array($section)) {
            continue;
        }

        $source = sanitize_key($section['source'] ?? '');
        $title  = trim((string) ($section['title'] ?? ''));
        $open   = !empty($section['open']);
        $id_prefix = 'custom-filter-' . $section_index . '-' . $source;

        if ($title === '') {
            $title = __('Filter Section', 'ai-zippy-child');
        }

        if ($source === 'brand' && (!function_exists('ai_zippy_child_custom_filter_brand_taxonomy') || ai_zippy_child_custom_filter_brand_taxonomy() === '')) {
            continue;
        }
        ?>
        <details class="custom-filter__section" <?php echo $open ? 'open' : ''; ?>>
            <summary><?php echo esc_html($title); ?></summary>
            <div class="custom-filter__options">
                <?php if ($source === 'price') : ?>
                    <?php
                    $ranges = function_exists('ai_zippy_child_custom_filter_build_price_ranges')
                        ? ai_zippy_child_custom_filter_build_price_ranges(absint($section['rangeCount'] ?? 4))
                        : [];
                    $current_price_range = $get_current_values('price_range')[0] ?? '';
                    ?>
                    <?php foreach ($ranges as $range_index => $range) : ?>
                        <label class="custom-filter__option" for="<?php echo esc_attr($id_prefix . '-' . $range_index); ?>">
                            <input id="<?php echo esc_attr($id_prefix . '-' . $range_index); ?>" type="radio" name="price_range" value="<?php echo esc_attr($range['value']); ?>" <?php checked($current_price_range, $range['value']); ?> />
                            <span><?php echo wp_kses_post($range['label']); ?></span>
                        </label>
                    <?php endforeach; ?>
                <?php elseif ($source === 'stock') : ?>
                    <?php $current_stock = $get_current_values('stock')[0] ?? ''; ?>
                    <label class="custom-filter__option" for="<?php echo esc_attr($id_prefix . '-instock'); ?>">
                        <input id="<?php echo esc_attr($id_prefix . '-instock'); ?>" type="checkbox" name="stock" value="instock" <?php checked($current_stock, 'instock'); ?> />
                        <span><?php esc_html_e('In stock only', 'ai-zippy-child'); ?></span>
                    </label>
                <?php else : ?>
                    <?php
                    $name_map = [
                        'product_cat' => 'filter_product_cat',
                        'product_tag' => 'filter_product_tag',
                        'brand'       => 'filter_brand',
                    ];
                    $name = $name_map[$source] ?? '';
                    $terms = $get_terms_for_section($section);
                    ?>
                    <?php if ($name !== '' && $terms) : ?>
                        <?php $render_terms($terms, $section, $name, $get_current_values($name), $id_prefix); ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </details>
    <?php endforeach; ?>

    <div class="custom-filter__actions">
        <button class="custom-filter__submit az-button az-button--small" type="submit">
            <?php echo esc_html($attrs['submitText']); ?>
        </button>
        <?php if (!empty($attrs['showClear'])) : ?>
            <a class="custom-filter__clear" href="<?php echo esc_url($shop_url); ?>">
                <?php echo esc_html($attrs['clearText']); ?>
            </a>
        <?php endif; ?>
    </div>
</form>
