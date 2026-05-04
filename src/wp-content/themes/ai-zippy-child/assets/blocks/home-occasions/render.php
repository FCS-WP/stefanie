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
        'backgroundColor' => '',
        'art'         => 'cake',
        'imageId'     => 0,
        'imageUrl'    => '',
        'imageAlt'    => '',
        'className'   => '',
    ],
    [
        'title'       => 'Just Because',
        'description' => 'Little treats for fun',
        'buttonText'  => 'SHOP',
        'buttonUrl'   => '/shop',
        'color'       => 'red',
        'backgroundColor' => '',
        'art'         => 'gift',
        'imageId'     => 0,
        'imageUrl'    => '',
        'imageAlt'    => '',
        'className'   => '',
    ],
    [
        'title'       => 'Festive Gifting',
        'description' => 'CNY . Christmas . Hari Raya',
        'buttonText'  => 'SHOP',
        'buttonUrl'   => '/shop',
        'color'       => 'green',
        'backgroundColor' => '',
        'art'         => 'sparkles',
        'imageId'     => 0,
        'imageUrl'    => '',
        'imageAlt'    => '',
        'className'   => '',
    ],
    [
        'title'       => 'No time?',
        'description' => "We've got you covered",
        'buttonText'  => 'SHOP',
        'buttonUrl'   => '/shop',
        'color'       => 'yellow',
        'backgroundColor' => '',
        'art'         => 'present',
        'imageId'     => 0,
        'imageUrl'    => '',
        'imageAlt'    => '',
        'className'   => '',
    ],
    [
        'title'       => 'Baby Shower',
        'description' => 'Welcome the little one in style',
        'buttonText'  => 'SHOP',
        'buttonUrl'   => '/shop',
        'color'       => 'green',
        'backgroundColor' => '',
        'art'         => 'balloons',
        'imageId'     => 0,
        'imageUrl'    => '',
        'imageAlt'    => '',
        'className'   => '',
    ],
    [
        'title'       => '1st Month',
        'description' => 'A milestone worth celebrating',
        'buttonText'  => 'SHOP',
        'buttonUrl'   => '/shop',
        'color'       => 'blue',
        'backgroundColor' => '',
        'art'         => 'cupcake',
        'imageId'     => 0,
        'imageUrl'    => '',
        'imageAlt'    => '',
        'className'   => '',
    ],
    [
        'title'       => 'Graduation',
        'description' => 'First day of school and beyond',
        'buttonText'  => 'SHOP',
        'buttonUrl'   => '/shop',
        'color'       => 'red',
        'backgroundColor' => '',
        'art'         => 'hat',
        'imageId'     => 0,
        'imageUrl'    => '',
        'imageAlt'    => '',
        'className'   => '',
    ],
];

$attrs = wp_parse_args($attributes ?? [], [
    'eyebrow'       => "WHAT'S THE OCCASION?",
    'heading'       => 'The Right Gift, for Every Moment.',
    'headingColor'  => '#000000',
    'description'   => "From baby showers to big birthdays - find something they'll actually remember.",
    'paddingTop'    => 78,
    'paddingBottom' => 66,
    'marginTop'     => 0,
    'marginBottom'  => 0,
    'items'         => $default_items,
]);

$items = is_array($attrs['items']) && !empty($attrs['items']) ? $attrs['items'] : $default_items;
$style = sprintf(
    '--home-occasions-padding-top:%dpx;--home-occasions-padding-bottom:%dpx;--home-occasions-margin-top:%dpx;--home-occasions-margin-bottom:%dpx;--home-occasions-heading-color:%s;',
    absint($attrs['paddingTop']),
    absint($attrs['paddingBottom']),
    absint($attrs['marginTop']),
    absint($attrs['marginBottom']),
    esc_attr(sanitize_hex_color($attrs['headingColor']) ?: '#000000')
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
                $card_background = sanitize_hex_color($item['backgroundColor'] ?? '');
                $card_classes = [
                    'home-occasions__card',
                    'home-occasions__card--' . $color,
                    'home-occasions__card--' . $art,
                ];

                if ((int) $index === 0) {
                    $card_classes[] = 'home-occasions__card--featured';
                }

                $custom_card_classes = array_filter(array_map(
                    'sanitize_html_class',
                    preg_split('/\s+/', (string) ($item['className'] ?? ''))
                ));
                $card_classes = array_merge($card_classes, $custom_card_classes);

                $card_style = $card_background ? '--home-occasions-card-bg:' . esc_attr($card_background) . ';' : '';
                ?>
                <article class="<?php echo esc_attr(implode(' ', $card_classes)); ?>" <?php echo $card_style ? 'style="' . esc_attr($card_style) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
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
