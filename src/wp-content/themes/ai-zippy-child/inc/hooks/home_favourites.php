<?php
/**
 * Home favourites AJAX helpers.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

add_action('rest_api_init', 'ai_zippy_child_register_home_favourites_routes');

function ai_zippy_child_register_home_favourites_routes(): void
{
    register_rest_route('ai-zippy-child/v1', '/home-favourites/products', [
        'methods'             => 'POST',
        'callback'            => 'ai_zippy_child_rest_home_favourites_products',
        'permission_callback' => '__return_true',
    ]);
}

function ai_zippy_child_rest_home_favourites_products(WP_REST_Request $request): WP_REST_Response
{
    $params = $request->get_json_params();

    if (!is_array($params)) {
        $params = [];
    }

    $filter = isset($params['filter']) && is_array($params['filter']) ? $params['filter'] : [];
    $limit  = isset($params['limit']) ? absint($params['limit']) : 8;

    return rest_ensure_response([
        'html'     => ai_zippy_child_home_favourites_products_html($filter, $limit),
        'products' => ai_zippy_child_home_favourites_get_products($filter, $limit),
    ]);
}

function ai_zippy_child_home_favourites_get_products(array $filter = [], int $limit = 8): array
{
    if (!function_exists('wc_get_products')) {
        return [];
    }

    $limit = max(1, min(24, $limit));
    $type  = sanitize_key($filter['type'] ?? 'recent');
    $args  = [
        'status'  => 'publish',
        'limit'   => $limit,
        'return'  => 'objects',
        'orderby' => 'date',
        'order'   => 'DESC',
    ];

    $category_id = absint($filter['categoryId'] ?? 0);

    if ($category_id > 0) {
        $term = get_term($category_id, 'product_cat');

        if ($term && !is_wp_error($term)) {
            $args['category'] = [$term->slug];
        }
    }

    if ($type === 'under_price') {
        $max_price          = max(1, (float) ($filter['maxPrice'] ?? 20));
        $args['meta_query'] = [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
            [
                'key'     => '_price',
                'value'   => $max_price,
                'compare' => '<=',
                'type'    => 'DECIMAL(10,2)',
            ],
        ];
        $args['orderby']   = 'meta_value_num';
        $args['meta_key']  = '_price'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
        $args['order']     = 'ASC';
    } elseif ($type === 'featured') {
        $args['featured'] = true;
        $args['orderby']  = 'date';
    } elseif ($type === 'sale') {
        $sale_ids = wc_get_product_ids_on_sale();

        if (empty($sale_ids)) {
            return [];
        }

        $args['include'] = $sale_ids;
        $args['orderby'] = 'date';
    } elseif ($type === 'popular') {
        $args['orderby']  = 'meta_value_num';
        $args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
        $args['order']    = 'DESC';
    } elseif ($type === 'new') {
        $args['orderby'] = 'date';
        $args['order']   = 'DESC';
    }

    $products = wc_get_products($args);

    return array_values(array_filter(array_map('ai_zippy_child_home_favourites_format_product', $products)));
}

function ai_zippy_child_home_favourites_format_product($product): array
{
    if (!$product instanceof WC_Product) {
        return [];
    }

    $image_id = $product->get_image_id();
    $terms    = wp_get_post_terms($product->get_id(), 'product_cat', ['fields' => 'names']);

    return [
        'id'              => $product->get_id(),
        'name'            => $product->get_name(),
        'permalink'       => get_permalink($product->get_id()),
        'price_html'      => $product->get_price_html(),
        'image'           => $image_id ? wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail') : wc_placeholder_img_src(),
        'image_alt'       => $image_id ? get_post_meta($image_id, '_wp_attachment_image_alt', true) : $product->get_name(),
        'category'        => !empty($terms) && !is_wp_error($terms) ? $terms[0] : '',
        'add_to_cart_url' => $product->add_to_cart_url(),
        'add_to_cart_text'=> $product->add_to_cart_text(),
    ];
}

function ai_zippy_child_home_favourites_products_html(array $filter = [], int $limit = 8): string
{
    $products = ai_zippy_child_home_favourites_get_products($filter, $limit);

    if (empty($products)) {
        return '<p class="home-favourites__empty">' . esc_html__('No products found.', 'ai-zippy-child') . '</p>';
    }

    ob_start();

    foreach ($products as $index => $product) {
        ai_zippy_child_home_favourites_render_product($product, $index);
    }

    return (string) ob_get_clean();
}

function ai_zippy_child_home_favourites_render_product(array $product, int $index = 0): void
{
    ?>
    <article class="home-favourites__slide<?php echo $index === 1 ? ' is-active' : ''; ?>">
        <div class="home-favourites__card">
            <a class="home-favourites__image-link" href="<?php echo esc_url($product['permalink']); ?>">
                <img class="home-favourites__image" src="<?php echo esc_url($product['image']); ?>" alt="<?php echo esc_attr($product['image_alt'] ?: $product['name']); ?>">
            </a>
            <div class="home-favourites__body">
                <a class="home-favourites__product-title" href="<?php echo esc_url($product['permalink']); ?>">
                    <?php echo esc_html($product['name']); ?>
                </a>
                <?php if (!empty($product['category'])) : ?>
                    <p class="home-favourites__meta"><?php echo esc_html($product['category']); ?></p>
                <?php endif; ?>
                <div class="home-favourites__bottom">
                    <span class="home-favourites__price"><?php echo wp_kses_post($product['price_html']); ?></span>
                    <a class="home-favourites__add az-button az-button--small" href="<?php echo esc_url($product['add_to_cart_url']); ?>" aria-label="<?php echo esc_attr($product['add_to_cart_text']); ?>">
                        <?php esc_html_e('+ADD', 'ai-zippy-child'); ?>
                    </a>
                </div>
            </div>
        </div>
    </article>
    <?php
}
