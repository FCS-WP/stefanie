<?php
/**
 * Shop Page block render.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

if (!function_exists('wc_get_products')) {
    return;
}

$wrapper_attributes = get_block_wrapper_attributes(['class' => 'shop-archive']);
$shop_url           = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
$current_term       = is_tax(['product_cat', 'product_tag']) || (function_exists('is_product_taxonomy') && is_product_taxonomy()) ? get_queried_object() : null;
$archive_title      = $current_term instanceof WP_Term ? $current_term->name : __('Buy Toys Online in Singapore', 'ai-zippy-child');
$archive_desc       = $current_term instanceof WP_Term && trim((string) $current_term->description) !== ''
    ? $current_term->description
    : __('Shop Bubble, Skip Hop, Micro & more — islandwide delivery', 'ai-zippy-child');
$breadcrumb_items = [
    [
        'label' => __('Home', 'ai-zippy-child'),
        'url'   => home_url('/'),
    ],
    [
        'label' => __('Shop', 'ai-zippy-child'),
        'url'   => $shop_url,
    ],
];

if ($current_term instanceof WP_Term) {
    $breadcrumb_items[] = [
        'label' => $current_term->name,
        'url'   => get_term_link($current_term),
    ];
} elseif (is_post_type_archive('product') && !function_exists('is_shop')) {
    $breadcrumb_items[] = [
        'label' => post_type_archive_title('', false),
        'url'   => '',
    ];
} elseif (is_search()) {
    $breadcrumb_items[] = [
        'label' => sprintf(__('Search: %s', 'ai-zippy-child'), get_search_query()),
        'url'   => '',
    ];
}

$get_param = static function (string $key, $default = '') {
    return isset($_GET[$key]) ? wc_clean(wp_unslash($_GET[$key])) : $default; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
};

$get_array_param = static function (string $key, array $default = []): array {
    if (!isset($_GET[$key])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        return $default;
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

    return array_values(array_filter(array_map('sanitize_title', $items)));
};

$taxonomy_exists = static function (string $taxonomy): bool {
    return taxonomy_exists($taxonomy);
};

$brand_taxonomy    = $taxonomy_exists('product_brand') ? 'product_brand' : ($taxonomy_exists('pa_brand') ? 'pa_brand' : '');
$age_taxonomy      = $taxonomy_exists('pa_age') ? 'pa_age' : '';
$occasion_taxonomy = $taxonomy_exists('pa_occasion') ? 'pa_occasion' : '';
$pill              = sanitize_key($get_param('shop_filter', 'all'));
$category          = $get_array_param(
    'filter_product_cat',
    $get_array_param('product_cat', $current_term instanceof WP_Term && $current_term->taxonomy === 'product_cat' ? [$current_term->slug] : [])
);
$product_tags      = $get_array_param(
    'filter_product_tag',
    $get_array_param('product_tag', $current_term instanceof WP_Term && $current_term->taxonomy === 'product_tag' ? [$current_term->slug] : [])
);
$age               = sanitize_title($get_param('age'));
$price             = sanitize_key($get_param('price'));
$price_range       = sanitize_text_field($get_param('price_range'));
$brand             = $get_array_param('filter_brand', $get_array_param('brand'));
$occasion          = sanitize_title($get_param('occasion'));
$stock             = sanitize_key($get_param('stock'));
$sort_options      = [
    'popular'    => __('Popular', 'ai-zippy-child'),
    'latest'     => __('Latest', 'ai-zippy-child'),
    'price-low'  => __('Price Low', 'ai-zippy-child'),
    'price-high' => __('Price High', 'ai-zippy-child'),
];
$sort              = sanitize_key($get_param('sort', 'popular'));
$sort              = array_key_exists($sort, $sort_options) ? $sort : 'popular';
$page              = max(1, absint($get_param('product-page', 1)));

$filter_count = 0;
foreach ([$category, $product_tags, $age, $price, $price_range, $brand, $occasion, $stock] as $value) {
    if ((is_array($value) && $value) || (!is_array($value) && $value !== '')) {
        ++$filter_count;
    }
}

$build_url = static function (array $changes = []) use ($shop_url): string {
    $params = [];

    foreach ($_GET as $key => $value) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $clean_key = sanitize_key($key);

        if ($clean_key === '') {
            continue;
        }

        $params[$clean_key] = is_array($value) ? array_map('wc_clean', wp_unslash($value)) : wc_clean(wp_unslash($value));
    }

    foreach ($changes as $key => $value) {
        if ($value === '' || $value === null || $value === false) {
            unset($params[$key]);
            continue;
        }

        $params[$key] = $value;
    }

    unset($params['product-page']);

    return esc_url(add_query_arg($params, $shop_url));
};

$get_terms = static function (string $taxonomy, array $args = []): array {
    if ($taxonomy === '' || !taxonomy_exists($taxonomy)) {
        return [];
    }

    $terms = get_terms(array_merge([
        'taxonomy'   => $taxonomy,
        'hide_empty' => true,
        'number'     => 20,
    ], $args));

    return is_wp_error($terms) ? [] : $terms;
};

$find_term_slug = static function (string $taxonomy, array $slugs): string {
    if ($taxonomy === '' || !taxonomy_exists($taxonomy)) {
        return '';
    }

    foreach ($slugs as $slug) {
        $term = get_term_by('slug', $slug, $taxonomy);

        if ($term instanceof WP_Term) {
            return $term->slug;
        }
    }

    return '';
};

$pills = [
    ['key' => 'all', 'label' => __('All Products', 'ai-zippy-child')],
    ['key' => 'trending', 'label' => __('Trending Now', 'ai-zippy-child')],
    ['key' => 'best-sellers', 'label' => __('Best-Sellers', 'ai-zippy-child')],
    ['key' => 'sale', 'label' => __('SALE', 'ai-zippy-child')],
    ['key' => 'gifts', 'label' => __('Gifts', 'ai-zippy-child')],
    ['key' => 'new-in', 'label' => __('New In', 'ai-zippy-child')],
    ['key' => 'educational', 'label' => __('Educational Toys', 'ai-zippy-child')],
    ['key' => 'outdoor', 'label' => __('Outdoor Play', 'ai-zippy-child')],
];

$categories = $get_terms('product_cat', ['number' => 0, 'orderby' => 'name', 'order' => 'ASC']);
$brands     = $brand_taxonomy ? $get_terms($brand_taxonomy, ['slug' => ['bubble', 'skip-hop', 'micro']]) : [];
$ages       = $age_taxonomy ? $get_terms($age_taxonomy) : [];
$occasions  = $occasion_taxonomy ? $get_terms($occasion_taxonomy) : [];

$args = [
    'status'   => 'publish',
    'limit'    => 12,
    'page'     => $page,
    'paginate' => true,
    'return'   => 'objects',
];

$tax_query  = [];
$meta_query = [];

$apply_include_ids = static function (array &$query_args, array $ids): void {
    $ids = array_values(array_unique(array_filter(array_map('absint', $ids))));

    if (!$ids) {
        $query_args['include'] = [0];
        return;
    }

    if (!empty($query_args['include'])) {
        $existing = array_values(array_filter(array_map('absint', (array) $query_args['include'])));
        $ids      = array_values(array_intersect($existing, $ids));
    }

    $query_args['include'] = $ids ?: [0];
};

$get_price_product_ids = static function (float $min_price, float $max_price): array {
    global $wpdb;

    if ($max_price < $min_price) {
        [$min_price, $max_price] = [$max_price, $min_price];
    }

    $lookup_table = $wpdb->prefix . 'wc_product_meta_lookup';
    $table_exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $lookup_table));

    if ($table_exists === $lookup_table) {
        return array_map(
            'absint',
            $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT product_id
                    FROM {$lookup_table}
                    WHERE min_price >= %f
                    AND min_price <= %f",
                    $min_price,
                    $max_price
                )
            )
        );
    }

    return array_map(
        'absint',
        $wpdb->get_col(
            $wpdb->prepare(
                "SELECT post_id
                FROM {$wpdb->postmeta}
                WHERE meta_key = %s
                AND CAST(meta_value AS DECIMAL(10,2)) BETWEEN %f AND %f",
                '_price',
                $min_price,
                $max_price
            )
        )
    );
};

if ($category) {
    $tax_query[] = [
        'taxonomy' => 'product_cat',
        'field'    => 'slug',
        'terms'    => $category,
    ];
}

if ($product_tags) {
    $tax_query[] = [
        'taxonomy' => 'product_tag',
        'field'    => 'slug',
        'terms'    => $product_tags,
    ];
}

if ($age !== '' && $age_taxonomy) {
    $tax_query[] = [
        'taxonomy' => $age_taxonomy,
        'field'    => 'slug',
        'terms'    => [$age],
    ];
}

if ($brand && $brand_taxonomy) {
    $tax_query[] = [
        'taxonomy' => $brand_taxonomy,
        'field'    => 'slug',
        'terms'    => $brand,
    ];
}

if ($occasion !== '' && $occasion_taxonomy) {
    $tax_query[] = [
        'taxonomy' => $occasion_taxonomy,
        'field'    => 'slug',
        'terms'    => [$occasion],
    ];
}

if ($stock === 'instock') {
    $args['stock_status'] = 'instock';
}

if ($price !== '') {
    $ranges = [
        'under-20' => [0, 20],
        '20-50'    => [20, 50],
        '50-100'   => [50, 100],
        '100-plus' => [100, 99999],
    ];

    if (isset($ranges[$price])) {
        $apply_include_ids($args, $get_price_product_ids((float) $ranges[$price][0], (float) $ranges[$price][1]));
    }
}

if ($price_range !== '' && preg_match('/^(\d+(?:\.\d+)?)-(\d+(?:\.\d+)?)$/', $price_range, $matches)) {
    $apply_include_ids($args, $get_price_product_ids((float) $matches[1], (float) $matches[2]));
}

if ($pill === 'sale') {
    $sale_ids = wc_get_product_ids_on_sale();
    $apply_include_ids($args, $sale_ids ?: [0]);
} elseif ($pill === 'best-sellers') {
    $args['orderby']  = 'meta_value_num';
    $args['meta_key'] = 'total_sales';
    $args['order']    = 'DESC';
} elseif ($pill === 'new-in') {
    $args['orderby'] = 'date';
    $args['order']   = 'DESC';
} elseif (in_array($pill, ['gifts', 'educational', 'outdoor'], true)) {
    $pill_slug = $find_term_slug('product_cat', [
        $pill,
        'gift',
        'gifts',
        'educational-toys',
        'outdoor-play',
    ]);

    if ($pill_slug !== '') {
        $tax_query[] = [
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => [$pill_slug],
        ];
    }
}

if ($sort === 'latest') {
    $args['orderby'] = 'date';
    $args['order']   = 'DESC';
} elseif ($sort === 'price-low') {
    $args['orderby']  = 'meta_value_num';
    $args['meta_key'] = '_price';
    $args['order']    = 'ASC';
} elseif ($sort === 'price-high') {
    $args['orderby']  = 'meta_value_num';
    $args['meta_key'] = '_price';
    $args['order']    = 'DESC';
} elseif (empty($args['orderby'])) {
    $args['orderby']  = 'meta_value_num';
    $args['meta_key'] = 'total_sales';
    $args['order']    = 'DESC';
}

if ($tax_query) {
    $args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
}

if ($meta_query) {
    $args['meta_query'] = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
}

$results  = wc_get_products($args);
$products = $results->products ?? [];
$max_page = max(1, (int) ($results->max_num_pages ?? 1));
$has_shop_pill_widgets   = is_active_sidebar('shop-pill-bar');
$has_shop_filter_widgets = is_active_sidebar('shop-filter-sidebar');

$render_filters = static function (string $context) use ($shop_url, $categories, $category, $ages, $age, $brands, $brand, $occasions, $occasion, $price, $stock, $sort): void {
    $id_prefix = 'shop-' . $context . '-';
    $price_ranges = [
        'under-20' => __('Under $20', 'ai-zippy-child'),
        '20-50'    => __('$20-$50', 'ai-zippy-child'),
        '50-100'   => __('$50-$100', 'ai-zippy-child'),
        '100-plus' => __('$100+', 'ai-zippy-child'),
    ];
    $render_category_terms = static function (array $terms, int $parent = 0, int $level = 0) use (&$render_category_terms, $category, $id_prefix): void {
        foreach ($terms as $term) {
            if ((int) $term->parent !== $parent) {
                continue;
            }
            ?>
            <label class="shop-archive__filter-option" style="<?php echo esc_attr('--filter-level:' . $level); ?>" for="<?php echo esc_attr($id_prefix . 'cat-' . $term->slug); ?>">
                <input id="<?php echo esc_attr($id_prefix . 'cat-' . $term->slug); ?>" data-shop-filter-input type="checkbox" name="filter_product_cat[]" value="<?php echo esc_attr($term->slug); ?>" <?php checked(in_array($term->slug, $category, true)); ?> />
                <?php echo esc_html($term->name); ?>
            </label>
            <?php
            $render_category_terms($terms, (int) $term->term_id, $level + 1);
        }
    };
    ?>
    <form class="shop-archive__filters" action="<?php echo esc_url($shop_url); ?>" method="get" data-shop-filter-form>
        <input type="hidden" name="sort" value="<?php echo esc_attr($sort); ?>" />
        <h2><?php esc_html_e('Filter', 'ai-zippy-child'); ?></h2>

        <details class="shop-archive__filter-group" open>
            <summary><?php esc_html_e('Category', 'ai-zippy-child'); ?></summary>
            <div class="shop-archive__filter-options">
                <?php $render_category_terms($categories); ?>
            </div>
        </details>

        <details class="shop-archive__filter-group" open>
            <summary><?php esc_html_e('Age Group', 'ai-zippy-child'); ?></summary>
            <div class="shop-archive__filter-options">
                <?php if ($ages) : ?>
                    <?php foreach ($ages as $term) : ?>
                        <label class="shop-archive__filter-option" for="<?php echo esc_attr($id_prefix . 'age-' . $term->slug); ?>">
                            <input id="<?php echo esc_attr($id_prefix . 'age-' . $term->slug); ?>" data-shop-filter-input type="radio" name="age" value="<?php echo esc_attr($term->slug); ?>" <?php checked($age, $term->slug); ?> />
                            <?php echo esc_html($term->name); ?>
                        </label>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p><?php esc_html_e('0-2 · 3-5 · 6-9 · 10+', 'ai-zippy-child'); ?></p>
                <?php endif; ?>
            </div>
        </details>

        <details class="shop-archive__filter-group" open>
            <summary><?php esc_html_e('Price', 'ai-zippy-child'); ?></summary>
            <div class="shop-archive__filter-options">
                <?php foreach ($price_ranges as $value => $label) : ?>
                    <label class="shop-archive__filter-option" for="<?php echo esc_attr($id_prefix . 'price-' . $value); ?>">
                        <input id="<?php echo esc_attr($id_prefix . 'price-' . $value); ?>" data-shop-filter-input type="radio" name="price" value="<?php echo esc_attr($value); ?>" <?php checked($price, $value); ?> />
                        <?php echo esc_html($label); ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </details>

        <?php if ($brands) : ?>
            <details class="shop-archive__filter-group" open>
                <summary><?php esc_html_e('Brand', 'ai-zippy-child'); ?></summary>
                <div class="shop-archive__filter-options">
                <?php foreach ($brands as $term) : ?>
                    <label class="shop-archive__filter-option" for="<?php echo esc_attr($id_prefix . 'brand-' . $term->slug); ?>">
                        <input id="<?php echo esc_attr($id_prefix . 'brand-' . $term->slug); ?>" data-shop-filter-input type="radio" name="filter_brand" value="<?php echo esc_attr($term->slug); ?>" <?php checked(in_array($term->slug, $brand, true)); ?> />
                        <?php echo esc_html($term->name); ?>
                    </label>
                <?php endforeach; ?>
                </div>
            </details>
        <?php endif; ?>

        <details class="shop-archive__filter-group" open>
            <summary><?php esc_html_e('Occasion', 'ai-zippy-child'); ?></summary>
            <div class="shop-archive__filter-options">
                <?php if ($occasions) : ?>
                    <?php foreach ($occasions as $term) : ?>
                        <label class="shop-archive__filter-option" for="<?php echo esc_attr($id_prefix . 'occasion-' . $term->slug); ?>">
                            <input id="<?php echo esc_attr($id_prefix . 'occasion-' . $term->slug); ?>" data-shop-filter-input type="radio" name="occasion" value="<?php echo esc_attr($term->slug); ?>" <?php checked($occasion, $term->slug); ?> />
                            <?php echo esc_html($term->name); ?>
                        </label>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p><?php esc_html_e('Birthday · Baby Shower · Festive · Graduation', 'ai-zippy-child'); ?></p>
                <?php endif; ?>
            </div>
        </details>

        <details class="shop-archive__filter-group" open>
            <summary><?php esc_html_e('Stock', 'ai-zippy-child'); ?></summary>
            <div class="shop-archive__filter-options">
                <label class="shop-archive__filter-option" for="<?php echo esc_attr($id_prefix . 'stock'); ?>">
                    <input id="<?php echo esc_attr($id_prefix . 'stock'); ?>" data-shop-filter-input type="checkbox" name="stock" value="instock" <?php checked($stock, 'instock'); ?> />
                    <?php esc_html_e('In stock only', 'ai-zippy-child'); ?>
                </label>
            </div>
        </details>

        <div class="shop-archive__filter-actions">
            <button class="az-button az-button--small" type="submit"><?php esc_html_e('Apply Filters', 'ai-zippy-child'); ?></button>
            <a href="<?php echo esc_url($shop_url); ?>"><?php esc_html_e('Clear', 'ai-zippy-child'); ?></a>
        </div>
    </form>
    <?php
};

$render_sort_form = static function (string $class_name) use ($build_url, $sort, $sort_options): void {
    $current_label = $sort_options[$sort] ?? $sort_options['popular'];
    ?>
    <div class="<?php echo esc_attr($class_name); ?>">
        <details class="shop-archive__sort-menu">
            <summary>
                <span><?php esc_html_e('Sort:', 'ai-zippy-child'); ?></span>
                <strong><?php echo esc_html($current_label); ?></strong>
            </summary>
            <div class="shop-archive__sort-options">
                <?php foreach ($sort_options as $value => $label) : ?>
                    <a class="<?php echo $sort === $value ? 'is-active' : ''; ?>" href="<?php echo esc_url($build_url(['sort' => $value === 'popular' ? null : $value])); ?>">
                        <?php echo esc_html($label); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </details>
    </div>
    <?php
};
?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <div class="shop-archive__hero">
        <nav class="shop-archive__breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'ai-zippy-child'); ?>">
            <?php foreach ($breadcrumb_items as $index => $item) : ?>
                <?php if ($index > 0) : ?>
                    <span aria-hidden="true">/</span>
                <?php endif; ?>

                <?php if (!empty($item['url']) && $index !== array_key_last($breadcrumb_items)) : ?>
                    <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['label']); ?></a>
                <?php else : ?>
                    <span><?php echo esc_html($item['label']); ?></span>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
        <h1><?php echo esc_html($archive_title); ?></h1>
        <p><?php echo esc_html(wp_strip_all_tags($archive_desc)); ?></p>
    </div>

    <div class="shop-archive__bar">
        <?php if ($has_shop_pill_widgets) : ?>
            <div class="shop-archive__pills shop-archive__pills--widgets">
                <?php dynamic_sidebar('shop-pill-bar'); ?>
            </div>
        <?php else : ?>
            <nav class="shop-archive__pills" aria-label="<?php esc_attr_e('Shop categories', 'ai-zippy-child'); ?>">
                <?php foreach ($pills as $pill_item) : ?>
                    <a class="<?php echo $pill === $pill_item['key'] ? 'is-active' : ''; ?>" href="<?php echo esc_url($build_url(['shop_filter' => $pill_item['key'] === 'all' ? null : $pill_item['key']])); ?>">
                        <?php echo esc_html($pill_item['label']); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        <?php endif; ?>

        <?php $render_sort_form('shop-archive__sort shop-archive__sort--desktop'); ?>
    </div>

    <div class="shop-archive__mobile-controls">
        <button type="button" data-shop-filter-open>
            <?php esc_html_e('Filter', 'ai-zippy-child'); ?>
            <span data-filter-count <?php echo $filter_count ? '' : 'hidden'; ?>><?php echo esc_html((string) $filter_count); ?></span>
        </button>
        <?php $render_sort_form('shop-archive__sort shop-archive__sort--mobile'); ?>
    </div>

    <div class="shop-archive__layout">
        <aside class="shop-archive__sidebar">
            <?php if ($has_shop_filter_widgets) : ?>
                <div class="shop-archive__widget-filter">
                    <?php dynamic_sidebar('shop-filter-sidebar'); ?>
                </div>
            <?php else : ?>
                <?php $render_filters('desktop'); ?>
            <?php endif; ?>
        </aside>

        <div class="shop-archive__products">
            <?php if ($products) : ?>
                <div class="shop-archive__grid">
                    <?php foreach ($products as $product) : ?>
                        <?php
                        $image_id = $product->get_image_id();
                        $badges   = wp_get_post_terms($product->get_id(), 'product_tag', ['fields' => 'names']);
                        $badges   = is_wp_error($badges) ? [] : array_slice(array_filter($badges), 0, 4);
                        ?>
                        <article class="shop-archive__card">
                            <a class="shop-archive__image" href="<?php echo esc_url($product->get_permalink()); ?>">
                                <?php if ($badges) : ?>
                                    <span class="shop-archive__badges" aria-label="<?php esc_attr_e('Product tags', 'ai-zippy-child'); ?>">
                                        <?php foreach ($badges as $badge_index => $badge) : ?>
                                            <span class="shop-archive__badge shop-archive__badge--<?php echo esc_attr(($badge_index % 4) + 1); ?>">
                                                <?php echo esc_html($badge); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </span>
                                <?php endif; ?>
                                <img src="<?php echo esc_url($image_id ? wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail') : wc_placeholder_img_src()); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" loading="lazy" />
                            </a>
                            <div class="shop-archive__card-body">
                                <h2><a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($product->get_name()); ?></a></h2>
                                <div class="shop-archive__price"><?php echo wp_kses_post($product->get_price_html()); ?></div>
                                <a class="shop-archive__add add_to_cart_button ajax_add_to_cart" href="<?php echo esc_url($product->add_to_cart_url()); ?>" data-product_id="<?php echo esc_attr((string) $product->get_id()); ?>" data-quantity="1" aria-label="<?php echo esc_attr($product->add_to_cart_description()); ?>">
                                    <?php esc_html_e('+ Add to Cart', 'ai-zippy-child'); ?>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p class="shop-archive__empty"><?php esc_html_e('No products found for these filters.', 'ai-zippy-child'); ?></p>
            <?php endif; ?>

            <?php if ($max_page > 1) : ?>
                <nav class="shop-archive__pagination" aria-label="<?php esc_attr_e('Product pagination', 'ai-zippy-child'); ?>">
                    <?php if ($page > 1) : ?>
                        <a class="shop-archive__page-prev" href="<?php echo esc_url(add_query_arg('product-page', $page - 1)); ?>">←</a>
                    <?php endif; ?>
                    <span><?php echo esc_html($page . ' / ' . $max_page); ?></span>
                    <?php if ($page < $max_page) : ?>
                        <a class="shop-archive__page-next" href="<?php echo esc_url(add_query_arg('product-page', $page + 1)); ?>">→</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </div>
    </div>

    <div class="shop-archive__drawer" data-shop-filter-drawer aria-hidden="true">
        <div class="shop-archive__drawer-panel">
            <div class="shop-archive__drawer-head">
                <h2><?php esc_html_e('Filter Products', 'ai-zippy-child'); ?></h2>
                <button type="button" data-shop-filter-close aria-label="<?php esc_attr_e('Close filters', 'ai-zippy-child'); ?>">×</button>
            </div>
            <?php if ($has_shop_filter_widgets) : ?>
                <div class="shop-archive__widget-filter">
                    <?php dynamic_sidebar('shop-filter-sidebar'); ?>
                </div>
            <?php else : ?>
                <?php $render_filters('mobile'); ?>
            <?php endif; ?>
        </div>
    </div>
</section>
