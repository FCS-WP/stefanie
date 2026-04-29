<?php
/**
 * Home Boxes block render.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

$attrs = wp_parse_args($attributes ?? [], [
    'boxOneValue' => '2,400+',
    'boxOneLabel' => 'Happy families',
    'boxTwoValue' => '300+',
    'boxTwoLabel' => 'Curated products',
    'boxThreeValue' => 'NEW',
    'boxThreeLabel' => 'Drop weekly',
    'boxOneBackground' => 'linear-gradient(105deg, #f37565 0%, #ff8c69 100%)',
    'boxTwoBackground' => 'linear-gradient(105deg, #36b79a 0%, #2b9f8a 100%)',
    'boxThreeBackground' => 'linear-gradient(105deg, #2c3e7a 0%, #111850 100%)',
    'sectionBackground' => '#ffffff',
    'maxWidth' => 1735,
    'gap' => 65,
    'paddingTop' => 112,
    'paddingRight' => 96,
    'paddingBottom' => 118,
    'paddingLeft' => 96,
    'marginTop' => 0,
    'marginBottom' => 0,
]);

$sanitize_background = static function ($background): string {
    $background = wp_strip_all_tags((string) $background);
    $background = str_replace([';', '{', '}'], '', $background);

    return trim($background);
};

$boxes = [
    [
        'value' => $attrs['boxOneValue'],
        'label' => $attrs['boxOneLabel'],
        'class' => 'home-boxes__card--boxOne',
    ],
    [
        'value' => $attrs['boxTwoValue'],
        'label' => $attrs['boxTwoLabel'],
        'class' => 'home-boxes__card--boxTwo',
    ],
    [
        'value' => $attrs['boxThreeValue'],
        'label' => $attrs['boxThreeLabel'],
        'class' => 'home-boxes__card--boxThree',
    ],
];

$style = sprintf(
    '--home-boxes-section-bg:%1$s;--home-boxes-max-width:%2$dpx;--home-boxes-gap:%3$dpx;--home-boxes-padding-top:%4$dpx;--home-boxes-padding-right:%5$dpx;--home-boxes-padding-bottom:%6$dpx;--home-boxes-padding-left:%7$dpx;--home-boxes-margin-top:%8$dpx;--home-boxes-margin-bottom:%9$dpx;--home-boxes-one-bg:%10$s;--home-boxes-two-bg:%11$s;--home-boxes-three-bg:%12$s;',
    esc_attr(sanitize_hex_color($attrs['sectionBackground']) ?: '#ffffff'),
    min(1735, max(900, absint($attrs['maxWidth']))),
    min(110, max(12, absint($attrs['gap']))),
    min(220, absint($attrs['paddingTop'])),
    min(180, absint($attrs['paddingRight'])),
    min(220, absint($attrs['paddingBottom'])),
    min(180, absint($attrs['paddingLeft'])),
    min(180, absint($attrs['marginTop'])),
    min(180, absint($attrs['marginBottom'])),
    esc_attr($sanitize_background($attrs['boxOneBackground'])),
    esc_attr($sanitize_background($attrs['boxTwoBackground'])),
    esc_attr($sanitize_background($attrs['boxThreeBackground']))
);

$wrapper_attributes = get_block_wrapper_attributes(['class' => 'home-boxes', 'style' => $style]);
?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <div class="home-boxes__inner">
        <?php foreach ($boxes as $box) : ?>
            <div class="home-boxes__card <?php echo esc_attr($box['class']); ?>">
                <?php if (trim((string) $box['value']) !== '') : ?>
                    <p class="home-boxes__value"><?php echo esc_html($box['value']); ?></p>
                <?php endif; ?>

                <?php if (trim((string) $box['label']) !== '') : ?>
                    <p class="home-boxes__label"><?php echo esc_html($box['label']); ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>
