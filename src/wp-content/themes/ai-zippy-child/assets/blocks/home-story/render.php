<?php
defined('ABSPATH') || exit;

$attrs = wp_parse_args($attributes ?? [], [
    'heading' => 'Come in for a look.<br>Leave with the perfect gift.',
    'description' => 'Tom & Stefanie is a curated destination for fun, thoughtful, and trend-forward toys & giftables - chosen to spark a reaction in every kid.',
    'pointOne' => 'Curated with care<br>every item hand-picked',
    'pointTwo' => 'Affordable, fun, and<br>always on-trend',
    'buttonText' => 'OUR STORY',
    'buttonUrl' => '/about-us',
    'backgroundImageUrl' => '',
    'backgroundImageAlt' => '',
    'paddingTop' => 86,
    'paddingBottom' => 72,
    'marginTop' => 0,
    'marginBottom' => 0,
]);

$style = sprintf('--home-story-padding-top:%dpx;--home-story-padding-bottom:%dpx;--home-story-margin-top:%dpx;--home-story-margin-bottom:%dpx;', absint($attrs['paddingTop']), absint($attrs['paddingBottom']), absint($attrs['marginTop']), absint($attrs['marginBottom']));
$wrapper_attributes = get_block_wrapper_attributes(['class' => 'home-story', 'style' => $style]);
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <div class="home-story__inner">
        <div class="home-story__panel">
            <?php if (!empty($attrs['backgroundImageUrl'])) : ?>
                <img class="home-story__image" src="<?php echo esc_url($attrs['backgroundImageUrl']); ?>" alt="<?php echo esc_attr($attrs['backgroundImageAlt']); ?>">
            <?php endif; ?>
            <div class="home-story__content">
                <h2 class="home-story__heading az-section-heading"><?php echo wp_kses_post($attrs['heading']); ?></h2>
                <p class="home-story__description"><?php echo wp_kses_post($attrs['description']); ?></p>
                <p class="home-story__point home-story__point--heart"><?php echo wp_kses_post($attrs['pointOne']); ?></p>
                <p class="home-story__point home-story__point--coin"><?php echo wp_kses_post($attrs['pointTwo']); ?></p>
            </div>
            <?php if (trim((string) $attrs['buttonText']) !== '') : ?>
                <a class="home-story__button az-button az-button--medium" href="<?php echo esc_url($attrs['buttonUrl'] ?: '#'); ?>"><?php echo esc_html($attrs['buttonText']); ?></a>
            <?php endif; ?>
        </div>
    </div>
</section>
