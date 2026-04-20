<?php
/**
 * Home Testimonials block render.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

$default_reviews = [
    [
        'rating'   => 5,
        'text'     => 'Found the perfect birthday gift here. The staff actually knew what they were talking about!',
        'name'     => 'Sarah L.',
        'location' => 'Woodlands',
    ],
    [
        'rating'   => 4,
        'text'     => 'Kids went crazy for the magnetic tiles. Ordered online, arrived in 2 days. 10/10 will repeat.',
        'name'     => 'Marcus T.',
        'location' => 'Tampines',
    ],
];

$attrs = wp_parse_args($attributes ?? [], [
    'eyebrow'          => 'CRUMBS OF TRUTH',
    'heading'          => 'What Parents<br>Are Saying',
    'leftShortcode'    => '',
    'viewMoreText'     => 'VIEW MORE',
    'viewMoreUrl'      => '/reviews',
    'followText'       => 'FOLLOW US',
    'followUrl'        => 'https://www.instagram.com/',
    'socialImageUrl'   => '',
    'socialImageAlt'   => '',
    'paddingTop'       => 76,
    'paddingBottom'    => 88,
    'marginTop'        => 0,
    'marginBottom'     => 0,
    'reviews'          => $default_reviews,
]);

$reviews = is_array($attrs['reviews']) && !empty($attrs['reviews']) ? $attrs['reviews'] : $default_reviews;
$style = sprintf(
    '--home-testimonials-padding-top:%dpx;--home-testimonials-padding-bottom:%dpx;--home-testimonials-margin-top:%dpx;--home-testimonials-margin-bottom:%dpx;',
    absint($attrs['paddingTop']),
    absint($attrs['paddingBottom']),
    absint($attrs['marginTop']),
    absint($attrs['marginBottom'])
);

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'home-testimonials',
    'style' => $style,
]);
?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <div class="home-testimonials__inner">
        <div class="home-testimonials__reviews">
            <?php if (trim((string) $attrs['eyebrow']) !== '') : ?>
                <p class="home-testimonials__eyebrow"><?php echo esc_html($attrs['eyebrow']); ?></p>
            <?php endif; ?>

            <?php if (trim((string) $attrs['heading']) !== '') : ?>
                <h2 class="home-testimonials__heading az-section-heading"><?php echo wp_kses_post($attrs['heading']); ?></h2>
            <?php endif; ?>

            <?php if (trim((string) $attrs['leftShortcode']) !== '') : ?>
                <div class="home-testimonials__shortcode">
                    <?php echo do_shortcode(wp_kses_post($attrs['leftShortcode'])); ?>
                </div>
            <?php else : ?>
                <div class="home-testimonials__cards">
                    <?php foreach ($reviews as $review) : ?>
                        <?php
                        $review = wp_parse_args((array) $review, $default_reviews[0]);
                        $rating = max(1, min(5, absint($review['rating'])));
                        ?>
                        <article class="home-testimonials__card">
                            <div class="home-testimonials__stars" aria-label="<?php echo esc_attr(sprintf(__('%d out of 5 stars', 'ai-zippy-child'), $rating)); ?>">
                                <?php echo esc_html(str_repeat('★', $rating)); ?>
                            </div>
                            <?php if (trim((string) $review['text']) !== '') : ?>
                                <p class="home-testimonials__quote"><?php echo wp_kses_post($review['text']); ?></p>
                            <?php endif; ?>
                            <?php if (trim((string) $review['name']) !== '') : ?>
                                <p class="home-testimonials__name"><?php echo esc_html($review['name']); ?></p>
                            <?php endif; ?>
                            <?php if (trim((string) $review['location']) !== '') : ?>
                                <p class="home-testimonials__location"><?php echo esc_html($review['location']); ?></p>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (trim((string) $attrs['viewMoreText']) !== '') : ?>
                <a class="home-testimonials__button az-button az-button--medium" href="<?php echo esc_url($attrs['viewMoreUrl'] ?: '#'); ?>">
                    <?php echo esc_html($attrs['viewMoreText']); ?>
                </a>
            <?php endif; ?>
        </div>

        <div class="home-testimonials__social">
            <div class="home-testimonials__social-frame">
                <?php if (!empty($attrs['socialImageUrl'])) : ?>
                    <img src="<?php echo esc_url($attrs['socialImageUrl']); ?>" alt="<?php echo esc_attr($attrs['socialImageAlt']); ?>">
                <?php else : ?>
                    <div class="home-testimonials__social-placeholder">
                        <span>Tom & Stefanie</span>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (trim((string) $attrs['followText']) !== '') : ?>
                <a class="home-testimonials__button az-button az-button--medium" href="<?php echo esc_url($attrs['followUrl'] ?: '#'); ?>">
                    <?php echo esc_html($attrs['followText']); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>
