<?php
/**
 * Home Clients V2 block render.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

$default_logos = [
    ['text' => 'ST', 'imageUrl' => '', 'imageAlt' => 'The Straits Times', 'url' => '#'],
    ['text' => 'cna<br><span>LIFESTYLE</span>', 'imageUrl' => '', 'imageAlt' => 'CNA Lifestyle', 'url' => '#'],
    ['text' => 'her<br>world', 'imageUrl' => '', 'imageAlt' => 'Her World', 'url' => '#'],
    ['text' => 'YOUNG<br>PARENTS', 'imageUrl' => '', 'imageAlt' => 'Young Parents', 'url' => '#'],
];

$attrs = wp_parse_args($attributes ?? [], [
    'heading'         => 'A Little Shop That Made the Headlines.',
    'backgroundColor' => '#fffbd6',
    'textColor'       => '#000000',
    'dotColor'        => '#ffffff',
    'dotActiveColor'  => '#f16a6c',
    'logosPerSlide'   => 4,
    'logoMaxWidth'    => 220,
    'logoMaxHeight'   => 96,
    'logoGap'         => 72,
    'logoOpacity'     => 100,
    'logoGrayscale'   => false,
    'logos'           => $default_logos,
    'paddingTop'      => 92,
    'paddingBottom'   => 78,
    'marginTop'       => 0,
    'marginBottom'    => 0,
]);

$logos = is_array($attrs['logos']) && !empty($attrs['logos']) ? $attrs['logos'] : $default_logos;
$logos = array_values(array_map(
    static fn($logo) => wp_parse_args((array) $logo, ['text' => '', 'imageUrl' => '', 'imageAlt' => '', 'url' => '#']),
    $logos
));

$logos_per_slide = max(1, min(6, absint($attrs['logosPerSlide'])));
$slides          = array_chunk($logos, $logos_per_slide);
$style           = sprintf(
    '--home-clients-v2-bg:%1$s;--home-clients-v2-color:%2$s;--home-clients-v2-dot:%3$s;--home-clients-v2-dot-active:%4$s;--home-clients-v2-logo-max-width:%5$dpx;--home-clients-v2-logo-max-height:%6$dpx;--home-clients-v2-logo-gap:%7$dpx;--home-clients-v2-logo-opacity:%8$s;--home-clients-v2-logo-filter:%9$s;--home-clients-v2-padding-top:%10$dpx;--home-clients-v2-padding-bottom:%11$dpx;--home-clients-v2-margin-top:%12$dpx;--home-clients-v2-margin-bottom:%13$dpx;',
    esc_attr(sanitize_hex_color($attrs['backgroundColor']) ?: '#fffbd6'),
    esc_attr(sanitize_hex_color($attrs['textColor']) ?: '#000000'),
    esc_attr(sanitize_hex_color($attrs['dotColor']) ?: '#ffffff'),
    esc_attr(sanitize_hex_color($attrs['dotActiveColor']) ?: '#f16a6c'),
    absint($attrs['logoMaxWidth']),
    absint($attrs['logoMaxHeight']),
    absint($attrs['logoGap']),
    esc_attr(max(20, min(100, absint($attrs['logoOpacity']))) / 100),
    !empty($attrs['logoGrayscale']) ? 'grayscale(1)' : 'none',
    absint($attrs['paddingTop']),
    absint($attrs['paddingBottom']),
    absint($attrs['marginTop']),
    absint($attrs['marginBottom'])
);

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'home-clients-v2',
    'style' => $style,
]);
?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <div class="home-clients-v2__inner">
        <?php if (trim((string) $attrs['heading']) !== '') : ?>
            <h2 class="home-clients-v2__heading az-section-heading"><?php echo wp_kses_post($attrs['heading']); ?></h2>
        <?php endif; ?>

        <div class="home-clients-v2__viewport" aria-live="polite">
            <div class="home-clients-v2__track">
                <?php foreach ($slides as $slide_index => $slide) : ?>
                    <div class="home-clients-v2__slide<?php echo 0 === $slide_index ? ' is-active' : ''; ?>" data-slide="<?php echo esc_attr($slide_index); ?>">
                        <?php foreach ($slide as $logo) : ?>
                            <?php $logo_url = trim((string) ($logo['url'] ?? '#')); ?>
                            <<?php echo $logo_url && '#' !== $logo_url ? 'a' : 'span'; ?>
                                class="home-clients-v2__logo"
                                <?php if ($logo_url && '#' !== $logo_url) : ?>
                                    href="<?php echo esc_url($logo_url); ?>"
                                <?php endif; ?>
                            >
                                <?php if (!empty($logo['imageUrl'])) : ?>
                                    <img src="<?php echo esc_url($logo['imageUrl']); ?>" alt="<?php echo esc_attr($logo['imageAlt']); ?>">
                                <?php else : ?>
                                    <span><?php echo wp_kses_post($logo['text']); ?></span>
                                <?php endif; ?>
                            </<?php echo $logo_url && '#' !== $logo_url ? 'a' : 'span'; ?>>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (count($slides) > 1) : ?>
            <div class="home-clients-v2__dots" role="tablist" aria-label="<?php esc_attr_e('Client logo slides', 'ai-zippy-child'); ?>">
                <?php foreach ($slides as $index => $slide) : ?>
                    <button
                        class="home-clients-v2__dot<?php echo 0 === $index ? ' is-active' : ''; ?>"
                        type="button"
                        data-slide="<?php echo esc_attr($index); ?>"
                        aria-label="<?php echo esc_attr(sprintf(__('Show client slide %d', 'ai-zippy-child'), $index + 1)); ?>"
                        aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
                    ></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
