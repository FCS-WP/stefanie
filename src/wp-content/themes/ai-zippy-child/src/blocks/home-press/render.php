<?php
defined('ABSPATH') || exit;

$attrs = wp_parse_args($attributes ?? [], [
    'heading' => 'A Little Shop That Made the Headlines.',
    'backgroundImageUrl' => '',
    'backgroundImageAlt' => '',
    'logos' => [],
    'paddingTop' => 86,
    'paddingBottom' => 0,
    'marginTop' => 0,
    'marginBottom' => 0,
]);

$logos = is_array($attrs['logos']) ? $attrs['logos'] : [];
$style = sprintf('--home-press-padding-top:%dpx;--home-press-padding-bottom:%dpx;--home-press-margin-top:%dpx;--home-press-margin-bottom:%dpx;', absint($attrs['paddingTop']), absint($attrs['paddingBottom']), absint($attrs['marginTop']), absint($attrs['marginBottom']));
$wrapper_attributes = get_block_wrapper_attributes(['class' => 'home-press', 'style' => $style]);
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <?php if (!empty($attrs['backgroundImageUrl'])) : ?>
        <img class="home-press__image" src="<?php echo esc_url($attrs['backgroundImageUrl']); ?>" alt="<?php echo esc_attr($attrs['backgroundImageAlt']); ?>">
    <?php endif; ?>
    <div class="home-press__inner">
        <h2 class="home-press__heading az-section-heading"><?php echo wp_kses_post($attrs['heading']); ?></h2>
        <div class="home-press__logos">
            <?php foreach ($logos as $logo) : ?>
                <?php $logo = wp_parse_args((array) $logo, ['text' => '', 'imageUrl' => '', 'imageAlt' => '', 'url' => '#']); ?>
                <a class="home-press__logo" href="<?php echo esc_url($logo['url'] ?: '#'); ?>">
                    <?php if (!empty($logo['imageUrl'])) : ?>
                        <img src="<?php echo esc_url($logo['imageUrl']); ?>" alt="<?php echo esc_attr($logo['imageAlt']); ?>">
                    <?php else : ?>
                        <span><?php echo wp_kses_post($logo['text']); ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="home-press__dots" aria-hidden="true"><span class="is-active"></span><span></span><span></span></div>
    </div>
</section>
