<?php
/**
 * Home Occasions block render.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

$default_items = [
    [
        'title'       => 'Birthday Party',
        'description' => "Perfect for your child's friend",
        'buttonText'  => 'SHOP',
        'buttonUrl'   => '/shop',
        'color'       => 'blue',
        'art'         => 'cake',
        'imageId'     => 0,
        'imageUrl'    => '',
        'imageAlt'    => '',
    ],
    [
        'title'       => 'Just Because',
        'description' => 'Little treats for fun',
        'buttonText'  => 'SHOP',
        'buttonUrl'   => '/shop',
        'color'       => 'red',
        'art'         => 'gift',
        'imageId'     => 0,
        'imageUrl'    => '',
        'imageAlt'    => '',
    ],
    [
        'title'       => 'Festive Gifting',
        'description' => 'CNY . Christmas . Hari Raya',
        'buttonText'  => 'SHOP',
        'buttonUrl'   => '/shop',
        'color'       => 'green',
        'art'         => 'sparkles',
        'imageId'     => 0,
        'imageUrl'    => '',
        'imageAlt'    => '',
    ],
    [
        'title'       => 'No time?',
        'description' => "We've got you covered",
        'buttonText'  => 'SHOP',
        'buttonUrl'   => '/shop',
        'color'       => 'yellow',
        'art'         => 'present',
        'imageId'     => 0,
        'imageUrl'    => '',
        'imageAlt'    => '',
    ],
    [
        'title'       => 'Baby Shower',
        'description' => 'Welcome the little one in style',
        'buttonText'  => 'SHOP',
        'buttonUrl'   => '/shop',
        'color'       => 'green',
        'art'         => 'balloons',
        'imageId'     => 0,
        'imageUrl'    => '',
        'imageAlt'    => '',
    ],
    [
        'title'       => '1st Month',
        'description' => 'A milestone worth celebrating',
        'buttonText'  => 'SHOP',
        'buttonUrl'   => '/shop',
        'color'       => 'blue',
        'art'         => 'cupcake',
        'imageId'     => 0,
        'imageUrl'    => '',
        'imageAlt'    => '',
    ],
    [
        'title'       => 'Graduation',
        'description' => 'First day of school and beyond',
        'buttonText'  => 'SHOP',
        'buttonUrl'   => '/shop',
        'color'       => 'red',
        'art'         => 'hat',
        'imageId'     => 0,
        'imageUrl'    => '',
        'imageAlt'    => '',
    ],
];

$attrs = wp_parse_args($attributes ?? [], [
    'eyebrow'       => "WHAT'S THE OCCASION?",
    'heading'       => 'The Right Gift, for Every Moment.',
    'description'   => "From baby showers to big birthdays - find something they'll actually remember.",
    'paddingTop'    => 78,
    'paddingBottom' => 66,
    'marginTop'     => 0,
    'marginBottom'  => 0,
    'items'         => $default_items,
]);

$items = is_array($attrs['items']) && !empty($attrs['items']) ? $attrs['items'] : $default_items;
$style = sprintf(
    '--home-occasions-padding-top:%dpx;--home-occasions-padding-bottom:%dpx;--home-occasions-margin-top:%dpx;--home-occasions-margin-bottom:%dpx;',
    absint($attrs['paddingTop']),
    absint($attrs['paddingBottom']),
    absint($attrs['marginTop']),
    absint($attrs['marginBottom'])
);

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'home-occasions',
    'style' => $style,
]);
?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <div class="home-occasions__inner">
        <div class="home-occasions__header">
            <?php if (trim((string) $attrs['eyebrow']) !== '') : ?>
                <p class="home-occasions__eyebrow"><?php echo wp_kses_post($attrs['eyebrow']); ?></p>
            <?php endif; ?>

            <?php if (trim((string) $attrs['heading']) !== '') : ?>
                <h2 class="home-occasions__heading az-section-heading"><?php echo wp_kses_post($attrs['heading']); ?></h2>
            <?php endif; ?>

            <?php if (trim((string) $attrs['description']) !== '') : ?>
                <p class="home-occasions__description"><?php echo wp_kses_post($attrs['description']); ?></p>
            <?php endif; ?>
        </div>

        <div class="home-occasions__grid">
            <?php foreach ($items as $index => $item) : ?>
                <?php
                $item = wp_parse_args($item, $default_items[$index] ?? $default_items[0]);
                $color = sanitize_html_class($item['color'] ?: 'blue');
                $art = sanitize_html_class($item['art'] ?: 'gift');
                $card_classes = [
                    'home-occasions__card',
                    'home-occasions__card--' . $color,
                    'home-occasions__card--' . $art,
                ];

                if ((int) $index === 0) {
                    $card_classes[] = 'home-occasions__card--featured';
                }
                ?>
                <article class="<?php echo esc_attr(implode(' ', $card_classes)); ?>">
                    <div class="home-occasions__art" aria-hidden="true">
                        <?php if (!empty($item['imageUrl'])) : ?>
                            <img src="<?php echo esc_url($item['imageUrl']); ?>" alt="<?php echo esc_attr($item['imageAlt'] ?? ''); ?>">
                        <?php else : ?>
                            <span class="home-occasions__default-art"></span>
                        <?php endif; ?>
                    </div>

                    <div class="home-occasions__content">
                        <?php if (trim((string) $item['title']) !== '') : ?>
                            <h3 class="home-occasions__card-title"><?php echo wp_kses_post($item['title']); ?></h3>
                        <?php endif; ?>

                        <?php if (trim((string) $item['description']) !== '') : ?>
                            <p class="home-occasions__card-description"><?php echo wp_kses_post($item['description']); ?></p>
                        <?php endif; ?>

                        <?php if (trim((string) $item['buttonText']) !== '') : ?>
                            <a class="home-occasions__button az-button az-button--small" href="<?php echo esc_url($item['buttonUrl'] ?: '#'); ?>">
                                <?php echo esc_html($item['buttonText']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
