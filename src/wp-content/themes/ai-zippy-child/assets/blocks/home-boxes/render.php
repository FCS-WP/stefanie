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
]);

$boxes = [
    [
        'value' => $attrs['boxOneValue'],
        'label' => $attrs['boxOneLabel'],
    ],
    [
        'value' => $attrs['boxTwoValue'],
        'label' => $attrs['boxTwoLabel'],
    ],
    [
        'value' => $attrs['boxThreeValue'],
        'label' => $attrs['boxThreeLabel'],
    ],
];

$wrapper_attributes = get_block_wrapper_attributes(['class' => 'home-boxes']);
?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <div class="home-boxes__inner">
        <?php foreach ($boxes as $box) : ?>
            <div class="home-boxes__card">
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
