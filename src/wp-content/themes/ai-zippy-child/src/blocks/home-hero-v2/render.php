<?php
/**
 * Home Hero V2 block render.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

$default_cards = [
    [
        'label' => 'MOST GIFTED',
        'title' => "Birthday Hampers\nthat actually wow",
        'tone'  => 'coral',
        'icon'  => 'gift',
        'linkUrl' => '/shop',
        'imageId' => 0,
        'imageUrl' => '',
        'imageAlt' => '',
        'className' => '',
    ],
    [
        'label' => 'BABY SHOWER',
        'title' => "Welcome\nthe little one",
        'tone'  => 'mint',
        'icon'  => 'cupcake',
        'linkUrl' => '/shop',
        'imageId' => 0,
        'imageUrl' => '',
        'imageAlt' => '',
        'className' => '',
    ],
    [
        'label' => 'NEW IN',
        'title' => "Festive\nPicks",
        'tone'  => 'ink',
        'icon'  => 'sparkles',
        'linkUrl' => '/shop',
        'imageId' => 0,
        'imageUrl' => '',
        'imageAlt' => '',
        'className' => '',
    ],
];

$attrs = wp_parse_args($attributes ?? [], [
    'eyebrow' => "SINGAPORE'S GO-TO TOY & GIFT STORE",
    'heading' => 'The Right Gift,',
    'highlight' => 'Every Time.',
    'description' => 'From first birthdays to last-minute finds - curated toys & gifts that always land well. Online and in-store across Singapore.',
    'primaryButtonText' => 'SHOP ALL TOYS',
    'primaryButtonUrl' => '/shop',
    'secondaryButtonText' => 'JOIN T&S POINTS',
    'secondaryButtonUrl' => '/rewards',
    'backgroundMode' => 'color',
    'backgroundColor' => '#fffbd6',
    'backgroundImageUrl' => '',
    'backgroundImageAlt' => '',
    'imageObjectFit' => 'cover',
    'imageObjectPosition' => 'center center',
    'minHeight' => 650,
    'paddingTop' => 52,
    'paddingRight' => 96,
    'paddingBottom' => 62,
    'paddingLeft' => 96,
    'marginTop' => 0,
    'marginRight' => 0,
    'marginBottom' => 0,
    'marginLeft' => 0,
    'tabletPaddingTop' => 48,
    'tabletPaddingRight' => 48,
    'tabletPaddingBottom' => 56,
    'tabletPaddingLeft' => 48,
    'tabletMarginTop' => 0,
    'tabletMarginRight' => 0,
    'tabletMarginBottom' => 0,
    'tabletMarginLeft' => 0,
    'mobilePaddingTop' => 38,
    'mobilePaddingRight' => 24,
    'mobilePaddingBottom' => 42,
    'mobilePaddingLeft' => 24,
    'mobileMarginTop' => 0,
    'mobileMarginRight' => 0,
    'mobileMarginBottom' => 0,
    'mobileMarginLeft' => 0,
    'cards' => $default_cards,
]);

$allowed_object_fits = ['cover', 'contain', 'fill', 'scale-down'];
$allowed_positions   = ['center center', 'left center', 'right center', 'center top', 'center bottom'];
$allowed_tones       = ['coral', 'mint', 'ink'];
$allowed_icons       = ['gift', 'cupcake', 'sparkles'];
$object_fit          = in_array($attrs['imageObjectFit'], $allowed_object_fits, true) ? $attrs['imageObjectFit'] : 'cover';
$object_position     = in_array($attrs['imageObjectPosition'], $allowed_positions, true) ? $attrs['imageObjectPosition'] : 'center center';
$background_mode     = in_array($attrs['backgroundMode'], ['color', 'image'], true) ? $attrs['backgroundMode'] : 'color';
$has_background_img  = 'image' === $background_mode && !empty($attrs['backgroundImageUrl']);
$min_height          = min(860, max(520, absint($attrs['minHeight'])));
$cards               = is_array($attrs['cards']) && !empty($attrs['cards']) ? array_values($attrs['cards']) : $default_cards;
$style_parts         = [
    '--home-hero-v2-bg:' . esc_attr(sanitize_hex_color($attrs['backgroundColor']) ?: '#fffbd6'),
    '--home-hero-v2-min-height:' . $min_height . 'px',
    '--home-hero-v2-image-fit:' . esc_attr($object_fit),
    '--home-hero-v2-image-position:' . esc_attr($object_position),
];
$spacing_tokens      = [
    'padding-top' => 'paddingTop',
    'padding-right' => 'paddingRight',
    'padding-bottom' => 'paddingBottom',
    'padding-left' => 'paddingLeft',
    'margin-top' => 'marginTop',
    'margin-right' => 'marginRight',
    'margin-bottom' => 'marginBottom',
    'margin-left' => 'marginLeft',
    'padding-tablet-top' => 'tabletPaddingTop',
    'padding-tablet-right' => 'tabletPaddingRight',
    'padding-tablet-bottom' => 'tabletPaddingBottom',
    'padding-tablet-left' => 'tabletPaddingLeft',
    'margin-tablet-top' => 'tabletMarginTop',
    'margin-tablet-right' => 'tabletMarginRight',
    'margin-tablet-bottom' => 'tabletMarginBottom',
    'margin-tablet-left' => 'tabletMarginLeft',
    'padding-mobile-top' => 'mobilePaddingTop',
    'padding-mobile-right' => 'mobilePaddingRight',
    'padding-mobile-bottom' => 'mobilePaddingBottom',
    'padding-mobile-left' => 'mobilePaddingLeft',
    'margin-mobile-top' => 'mobileMarginTop',
    'margin-mobile-right' => 'mobileMarginRight',
    'margin-mobile-bottom' => 'mobileMarginBottom',
    'margin-mobile-left' => 'mobileMarginLeft',
];

foreach ($spacing_tokens as $token => $attribute_key) {
    $style_parts[] = '--home-hero-v2-' . $token . ':' . absint($attrs[$attribute_key]) . 'px';
}

$style = implode(';', $style_parts) . ';';

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'home-hero-v2 ' . ($has_background_img ? 'home-hero-v2--has-bg-image' : 'home-hero-v2--has-bg-color'),
    'style' => $style,
]);

$render_icon = static function (string $icon): void {
    if ('cupcake' === $icon) {
        ?>
        <span class="home-hero-v2__art home-hero-v2__art--cupcake" aria-hidden="true">
            <span class="home-hero-v2__candle"></span>
            <span class="home-hero-v2__frosting"></span>
            <span class="home-hero-v2__wrapper"></span>
        </span>
        <?php
        return;
    }

    if ('sparkles' === $icon) {
        ?>
        <span class="home-hero-v2__art home-hero-v2__art--sparkles" aria-hidden="true">
            <span></span>
            <span></span>
            <span></span>
        </span>
        <?php
        return;
    }
    ?>
    <span class="home-hero-v2__art home-hero-v2__art--gift" aria-hidden="true">
        <span class="home-hero-v2__gift-box"></span>
        <span class="home-hero-v2__gift-lid"></span>
        <span class="home-hero-v2__gift-ribbon"></span>
    </span>
    <?php
};
?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <?php if ($has_background_img) : ?>
        <img
            class="home-hero-v2__background-image"
            src="<?php echo esc_url($attrs['backgroundImageUrl']); ?>"
            alt="<?php echo esc_attr($attrs['backgroundImageAlt']); ?>"
        />
    <?php endif; ?>

    <div class="home-hero-v2__shape home-hero-v2__shape--mint" aria-hidden="true"></div>
    <div class="home-hero-v2__shape home-hero-v2__shape--pink" aria-hidden="true"></div>

    <div class="home-hero-v2__inner">
        <div class="home-hero-v2__content">
            <?php if (trim((string) $attrs['eyebrow']) !== '') : ?>
                <p class="home-hero-v2__eyebrow"><?php echo esc_html($attrs['eyebrow']); ?></p>
            <?php endif; ?>

            <?php if (trim((string) $attrs['heading']) !== '' || trim((string) $attrs['highlight']) !== '') : ?>
                <h1 class="home-hero-v2__heading">
                    <?php if (trim((string) $attrs['heading']) !== '') : ?>
                        <span><?php echo esc_html($attrs['heading']); ?></span>
                    <?php endif; ?>
                    <?php if (trim((string) $attrs['highlight']) !== '') : ?>
                        <span class="home-hero-v2__highlight"><?php echo esc_html($attrs['highlight']); ?></span>
                    <?php endif; ?>
                </h1>
            <?php endif; ?>

            <?php if (trim((string) $attrs['description']) !== '') : ?>
                <p class="home-hero-v2__description"><?php echo esc_html($attrs['description']); ?></p>
            <?php endif; ?>

            <div class="home-hero-v2__actions">
                <?php if (trim((string) $attrs['primaryButtonText']) !== '') : ?>
                    <a class="home-hero-v2__button home-hero-v2__button--primary" href="<?php echo esc_url($attrs['primaryButtonUrl'] ?: '#'); ?>">
                        <?php echo esc_html($attrs['primaryButtonText']); ?>
                    </a>
                <?php endif; ?>

                <?php if (trim((string) $attrs['secondaryButtonText']) !== '') : ?>
                    <a class="home-hero-v2__button home-hero-v2__button--secondary" href="<?php echo esc_url($attrs['secondaryButtonUrl'] ?: '#'); ?>">
                        <?php echo esc_html($attrs['secondaryButtonText']); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="home-hero-v2__cards">
            <?php foreach ($cards as $index => $card) : ?>
                <?php
                $card = wp_parse_args((array) $card, $default_cards[$index] ?? $default_cards[0]);
                $tone = in_array($card['tone'], $allowed_tones, true) ? $card['tone'] : 'coral';
                $icon = in_array($card['icon'], $allowed_icons, true) ? $card['icon'] : 'gift';
                $card_url = trim((string) ($card['linkUrl'] ?? ''));
                $card_tag = '' !== $card_url ? 'a' : 'article';
                $card_image_url = trim((string) ($card['imageUrl'] ?? ''));
                $custom_card_classes = array_filter(array_map(
                    'sanitize_html_class',
                    preg_split('/\s+/', (string) ($card['className'] ?? ''))
                ));
                $card_classes = trim('home-hero-v2__card home-hero-v2__card--' . $tone . ' ' . implode(' ', $custom_card_classes));
                ?>
                <<?php echo esc_html($card_tag); ?>
                    class="<?php echo esc_attr($card_classes); ?>"
                    <?php if ('' !== $card_url) : ?>
                        href="<?php echo esc_url($card_url); ?>"
                    <?php endif; ?>
                >
                    <div class="home-hero-v2__card-copy">
                        <?php if (trim((string) $card['label']) !== '') : ?>
                            <span class="home-hero-v2__tag"><?php echo esc_html($card['label']); ?></span>
                        <?php endif; ?>

                        <?php if (trim((string) $card['title']) !== '') : ?>
                            <h2 class="home-hero-v2__card-title"><?php echo nl2br(esc_html($card['title'])); ?></h2>
                        <?php endif; ?>
                    </div>

                    <?php if ('' !== $card_image_url) : ?>
                        <img
                            class="home-hero-v2__card-image"
                            src="<?php echo esc_url($card_image_url); ?>"
                            alt="<?php echo esc_attr($card['imageAlt'] ?? ''); ?>"
                        />
                    <?php else : ?>
                        <?php $render_icon($icon); ?>
                    <?php endif; ?>
                </<?php echo esc_html($card_tag); ?>>
            <?php endforeach; ?>
        </div>
    </div>
</section>
