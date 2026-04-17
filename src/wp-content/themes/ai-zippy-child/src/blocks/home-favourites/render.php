<?php
/**
 * Home Favourites block render.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

$default_filters = [
    ['label' => 'TRENDING NOW', 'type' => 'popular', 'categoryId' => 0, 'maxPrice' => 20],
    ['label' => 'STUFF PICKS', 'type' => 'featured', 'categoryId' => 0, 'maxPrice' => 20],
    ['label' => 'UNDER $20', 'type' => 'under_price', 'categoryId' => 0, 'maxPrice' => 20],
    ['label' => 'GIFT FOR KIDS', 'type' => 'category', 'categoryId' => 0, 'maxPrice' => 20],
    ['label' => 'NEW IN', 'type' => 'new', 'categoryId' => 0, 'maxPrice' => 20],
];

$attrs = wp_parse_args($attributes ?? [], [
    'eyebrow'       => 'POPULAR TOYS IN SINGAPORE',
    'heading'       => "This Week's Favourites",
    'description'   => 'Pick yours and discover toys your kids will actually love.',
    'viewAllText'   => 'VIEW ALL PRODUCTS',
    'viewAllUrl'    => '/shop',
    'productLimit'  => 8,
    'filterSource'  => 'custom',
    'categoryFilterIds' => [],
    'paddingTop'    => 92,
    'paddingBottom' => 108,
    'marginTop'     => 0,
    'marginBottom'  => 0,
    'filters'       => $default_filters,
]);

$filters = is_array($attrs['filters']) && !empty($attrs['filters']) ? $attrs['filters'] : $default_filters;

if (($attrs['filterSource'] ?? 'custom') === 'categories' && taxonomy_exists('product_cat')) {
    $category_ids = array_values(array_filter(array_map('absint', (array) $attrs['categoryFilterIds'])));
    $category_args = [
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'number'     => 5,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ];

    if (!empty($category_ids)) {
        $category_args['include'] = $category_ids;
        $category_args['number']  = count($category_ids);
    }

    $category_terms = get_terms($category_args);

    if (!is_wp_error($category_terms) && !empty($category_terms)) {
        $filters = array_map(
            static fn($term) => [
                'label'      => $term->name,
                'type'       => 'category',
                'categoryId' => (int) $term->term_id,
                'maxPrice'   => 20,
            ],
            $category_terms
        );
    }
}

$filters = array_values(array_map(
    static fn($filter) => wp_parse_args((array) $filter, ['label' => 'FILTER', 'type' => 'recent', 'categoryId' => 0, 'maxPrice' => 20]),
    $filters
));
$active_filter = $filters[0] ?? $default_filters[0];
$limit         = max(3, min(24, absint($attrs['productLimit'])));
$style         = sprintf(
    '--home-favourites-padding-top:%dpx;--home-favourites-padding-bottom:%dpx;--home-favourites-margin-top:%dpx;--home-favourites-margin-bottom:%dpx;',
    absint($attrs['paddingTop']),
    absint($attrs['paddingBottom']),
    absint($attrs['marginTop']),
    absint($attrs['marginBottom'])
);

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'home-favourites',
    'style' => $style,
]);
?>

<section
    <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    data-endpoint="<?php echo esc_url(rest_url('ai-zippy-child/v1/home-favourites/products')); ?>"
    data-limit="<?php echo esc_attr($limit); ?>"
>
    <div class="home-favourites__inner">
        <div class="home-favourites__header">
            <?php if (trim((string) $attrs['eyebrow']) !== '') : ?>
                <p class="home-favourites__eyebrow"><?php echo wp_kses_post($attrs['eyebrow']); ?></p>
            <?php endif; ?>

            <?php if (trim((string) $attrs['heading']) !== '') : ?>
                <h2 class="home-favourites__heading az-section-heading"><?php echo wp_kses_post($attrs['heading']); ?></h2>
            <?php endif; ?>

            <?php if (trim((string) $attrs['description']) !== '') : ?>
                <p class="home-favourites__description"><?php echo wp_kses_post($attrs['description']); ?></p>
            <?php endif; ?>
        </div>

        <div class="home-favourites__filters" role="tablist" aria-label="<?php esc_attr_e('Product filters', 'ai-zippy-child'); ?>">
            <?php foreach ($filters as $index => $filter) : ?>
                <button
                    class="home-favourites__filter az-button az-button--medium<?php echo $index === 0 ? ' is-active' : ''; ?>"
                    type="button"
                    data-filter="<?php echo esc_attr(wp_json_encode($filter)); ?>"
                    role="tab"
                    aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                >
                    <?php echo esc_html($filter['label']); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="home-favourites__carousel">
            <button class="home-favourites__arrow home-favourites__arrow--prev" type="button" aria-label="<?php esc_attr_e('Previous products', 'ai-zippy-child'); ?>">
                <span aria-hidden="true"></span>
            </button>

            <div class="home-favourites__viewport" aria-live="polite">
                <div class="home-favourites__track">
                    <?php
                    if (function_exists('ai_zippy_child_home_favourites_products_html')) {
                        echo ai_zippy_child_home_favourites_products_html($active_filter, $limit); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    }
                    ?>
                </div>
            </div>

            <button class="home-favourites__arrow home-favourites__arrow--next" type="button" aria-label="<?php esc_attr_e('Next products', 'ai-zippy-child'); ?>">
                <span aria-hidden="true"></span>
            </button>
        </div>

        <?php if (trim((string) $attrs['viewAllText']) !== '') : ?>
            <a class="home-favourites__view-all az-button az-button--large" href="<?php echo esc_url($attrs['viewAllUrl'] ?: '/shop'); ?>">
                <?php echo esc_html($attrs['viewAllText']); ?>
            </a>
        <?php endif; ?>
    </div>
</section>
