<?php
/**
 * Home Categories block render.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

$attrs = wp_parse_args($attributes ?? [], [
    'eyebrow'             => 'BROWSE OUR COLLECTION',
    'heading'             => 'Shop by Category',
    'description'         => 'Find the perfect toy for every age, occasion & personality.',
    'includeCategoryIds'  => [],
    'excludeCategoryIds'  => [],
    'maxCategories'       => 12,
    'hideEmpty'           => false,
    'orderBy'             => 'menu_order',
    'order'               => 'ASC',
    'slidesToShow'        => 6,
    'tabletSlidesToShow'  => 3,
    'mobileSlidesToShow'  => 2,
    'showArrows'          => true,
    'showDots'            => false,
    'paddingTop'          => 70,
    'paddingBottom'       => 86,
    'marginTop'           => 0,
    'marginBottom'        => 0,
]);

$include_ids = array_values(array_filter(array_map('absint', (array) $attrs['includeCategoryIds'])));
$exclude_ids = array_values(array_filter(array_map('absint', (array) $attrs['excludeCategoryIds'])));
$order_by    = in_array($attrs['orderBy'], ['menu_order', 'name', 'count', 'id'], true) ? $attrs['orderBy'] : 'menu_order';
$order       = strtoupper($attrs['order']) === 'DESC' ? 'DESC' : 'ASC';
$term_args   = [
    'taxonomy'   => 'product_cat',
    'hide_empty' => (bool) $attrs['hideEmpty'],
    'number'     => max(1, absint($attrs['maxCategories'])),
    'exclude'    => $exclude_ids,
    'order'      => $order,
];

if (!empty($include_ids)) {
    $term_args['include'] = $include_ids;
}

if ($order_by === 'menu_order') {
    $term_args['orderby']  = 'meta_value_num';
    $term_args['meta_key'] = 'order'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
} elseif ($order_by === 'id') {
    $term_args['orderby'] = 'term_id';
} else {
    $term_args['orderby'] = $order_by;
}

$terms = taxonomy_exists('product_cat') ? get_terms($term_args) : [];

if (is_wp_error($terms)) {
    $terms = [];
}

$style = sprintf(
    '--home-categories-padding-top:%dpx;--home-categories-padding-bottom:%dpx;--home-categories-margin-top:%dpx;--home-categories-margin-bottom:%dpx;--home-categories-slides:%d;--home-categories-tablet-slides:%d;--home-categories-mobile-slides:%d;',
    absint($attrs['paddingTop']),
    absint($attrs['paddingBottom']),
    absint($attrs['marginTop']),
    absint($attrs['marginBottom']),
    max(1, min(8, absint($attrs['slidesToShow']))),
    max(1, min(4, absint($attrs['tabletSlidesToShow']))),
    max(1, min(2, absint($attrs['mobileSlidesToShow'])))
);

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'home-categories',
    'style' => $style,
]);
?>

<section
    <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    data-desktop-slides="<?php echo esc_attr(max(1, min(8, absint($attrs['slidesToShow'])))); ?>"
    data-tablet-slides="<?php echo esc_attr(max(1, min(4, absint($attrs['tabletSlidesToShow'])))); ?>"
    data-mobile-slides="<?php echo esc_attr(max(1, min(2, absint($attrs['mobileSlidesToShow'])))); ?>"
>
    <div class="home-categories__inner">
        <div class="home-categories__header">
            <?php if (trim((string) $attrs['eyebrow']) !== '') : ?>
                <p class="home-categories__eyebrow"><?php echo wp_kses_post($attrs['eyebrow']); ?></p>
            <?php endif; ?>

            <?php if (trim((string) $attrs['heading']) !== '') : ?>
                <h2 class="home-categories__heading az-section-heading"><?php echo wp_kses_post($attrs['heading']); ?></h2>
            <?php endif; ?>

            <?php if (trim((string) $attrs['description']) !== '') : ?>
                <p class="home-categories__description"><?php echo wp_kses_post($attrs['description']); ?></p>
            <?php endif; ?>
        </div>

        <?php if (!empty($terms)) : ?>
            <div class="home-categories__carousel">
                <?php if ((bool) $attrs['showArrows']) : ?>
                    <button class="home-categories__arrow home-categories__arrow--prev" type="button" aria-label="<?php esc_attr_e('Previous categories', 'ai-zippy-child'); ?>">
                        <span aria-hidden="true"></span>
                    </button>
                <?php endif; ?>

                <div class="home-categories__viewport">
                    <div class="home-categories__track">
                        <?php foreach ($terms as $index => $term) : ?>
                            <?php
                            $thumbnail_id = absint(get_term_meta($term->term_id, 'thumbnail_id', true));
                            $image_url    = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'woocommerce_thumbnail') : '';
                            $image_alt    = $thumbnail_id ? get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true) : '';
                            $term_link    = get_term_link($term);

                            if (is_wp_error($term_link)) {
                                $term_link = '#';
                            }
                            ?>
                            <div class="home-categories__slide">
                                <a class="home-categories__card" href="<?php echo esc_url($term_link); ?>">
                                    <span class="home-categories__image-wrap home-categories__image-wrap--<?php echo esc_attr(($index % 6) + 1); ?>">
                                        <?php if ($image_url) : ?>
                                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt ?: $term->name); ?>">
                                        <?php else : ?>
                                            <span class="home-categories__image-placeholder" aria-hidden="true"></span>
                                        <?php endif; ?>
                                    </span>
                                    <span class="home-categories__category-title"><?php echo esc_html($term->name); ?></span>
                                    <span class="home-categories__count">
                                        <?php
                                        printf(
                                            esc_html(_n('%s product', '%s products', (int) $term->count, 'ai-zippy-child')),
                                            esc_html(number_format_i18n((int) $term->count))
                                        );
                                        ?>
                                    </span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if ((bool) $attrs['showArrows']) : ?>
                    <button class="home-categories__arrow home-categories__arrow--next" type="button" aria-label="<?php esc_attr_e('Next categories', 'ai-zippy-child'); ?>">
                        <span aria-hidden="true"></span>
                    </button>
                <?php endif; ?>
            </div>

            <?php if ((bool) $attrs['showDots']) : ?>
                <div class="home-categories__dots" aria-label="<?php esc_attr_e('Category carousel pages', 'ai-zippy-child'); ?>"></div>
            <?php endif; ?>
        <?php else : ?>
            <p class="home-categories__empty"><?php esc_html_e('No product categories found.', 'ai-zippy-child'); ?></p>
        <?php endif; ?>
    </div>
</section>
