<?php
/**
 * Home Age Guide block render.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

$default_cards = [
    [
        'title'       => '0-2 YEARS',
        'description' => 'Sensory & developmental toys for babies',
        'buttonText'  => 'SHOP BABY TOYS',
        'buttonUrl'   => '/shop',
        'imageId'     => 0,
        'imageUrl'    => '',
        'imageAlt'    => '',
        'color'       => 'yellow',
    ],
    [
        'title'       => '3-5 YEARS',
        'description' => 'Imaginative play, building & creativity',
        'buttonText'  => 'SHOP TODDLER TOYS',
        'buttonUrl'   => '/shop',
        'imageId'     => 0,
        'imageUrl'    => '',
        'imageAlt'    => '',
        'color'       => 'green',
    ],
    [
        'title'       => '6-9 YEARS',
        'description' => 'STEM, puzzles, games & outdoor sets',
        'buttonText'  => 'SHOP KIDS TOYS',
        'buttonUrl'   => '/shop',
        'imageId'     => 0,
        'imageUrl'    => '',
        'imageAlt'    => '',
        'color'       => 'blue',
    ],
    [
        'title'       => '10+ YEARS',
        'description' => 'Collectibles, strategy & trend-forward gifts',
        'buttonText'  => 'SHOP TWEEN TOYS',
        'buttonUrl'   => '/shop',
        'imageId'     => 0,
        'imageUrl'    => '',
        'imageAlt'    => '',
        'color'       => 'red',
    ],
];

$attrs = wp_parse_args($attributes ?? [], [
    'heading'       => 'Find The Perfect Toy By Age',
    'description'   => 'Find the perfect toy for every age, occasion & personality.',
    'paddingTop'    => 74,
    'paddingBottom' => 86,
    'marginTop'     => 0,
    'marginBottom'  => 0,
    'cards'         => $default_cards,
]);

$cards = is_array($attrs['cards']) && !empty($attrs['cards']) ? $attrs['cards'] : $default_cards;
$style = sprintf(
    '--home-age-guide-padding-top:%dpx;--home-age-guide-padding-bottom:%dpx;--home-age-guide-margin-top:%dpx;--home-age-guide-margin-bottom:%dpx;',
    absint($attrs['paddingTop']),
    absint($attrs['paddingBottom']),
    absint($attrs['marginTop']),
    absint($attrs['marginBottom'])
);

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'home-age-guide',
    'style' => $style,
]);
?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <div class="home-age-guide__inner">
        <div class="home-age-guide__header">
            <?php if (trim((string) $attrs['heading']) !== '') : ?>
                <h2 class="home-age-guide__heading az-section-heading"><?php echo wp_kses_post($attrs['heading']); ?></h2>
            <?php endif; ?>

            <?php if (trim((string) $attrs['description']) !== '') : ?>
                <p class="home-age-guide__description"><?php echo wp_kses_post($attrs['description']); ?></p>
            <?php endif; ?>
        </div>

        <div class="home-age-guide__grid">
            <?php foreach ($cards as $index => $card) : ?>
                <?php
                $card = wp_parse_args((array) $card, $default_cards[$index] ?? $default_cards[0]);
                $color = sanitize_html_class($card['color'] ?: 'yellow');
                ?>
                <article class="home-age-guide__card home-age-guide__card--<?php echo esc_attr($color); ?>">
                    <div class="home-age-guide__image-wrap">
                        <?php if (!empty($card['imageUrl'])) : ?>
                            <img src="<?php echo esc_url($card['imageUrl']); ?>" alt="<?php echo esc_attr($card['imageAlt'] ?? $card['title']); ?>">
                        <?php else : ?>
                            <span class="home-age-guide__default-art" aria-hidden="true"></span>
                        <?php endif; ?>
                    </div>

                    <?php if (trim((string) $card['title']) !== '') : ?>
                        <h3 class="home-age-guide__card-title"><?php echo wp_kses_post($card['title']); ?></h3>
                    <?php endif; ?>

                    <?php if (trim((string) $card['description']) !== '') : ?>
                        <p class="home-age-guide__card-description"><?php echo wp_kses_post($card['description']); ?></p>
                    <?php endif; ?>

                    <?php if (trim((string) $card['buttonText']) !== '') : ?>
                        <a class="home-age-guide__button az-button" href="<?php echo esc_url($card['buttonUrl'] ?: '#'); ?>">
                            <?php echo esc_html($card['buttonText']); ?>
                        </a>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
