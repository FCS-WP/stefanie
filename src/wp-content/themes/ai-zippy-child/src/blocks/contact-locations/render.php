<?php
defined('ABSPATH') || exit;

$attrs = wp_parse_args($attributes ?? [], [
    'sectionId' => 'contact-stores',
    'eyebrow' => 'C2 · STORE LOCATIONS + MAP',
    'note' => 'Note: Hillion Mall outlet is closing May 2026 - excluded from website. Update if timeline changes.',
    'mapSectionId' => 'contact-map',
    'mapHeading' => 'Store Map',
    'mapShortcode' => '',
    'mapHtmlOne' => '',
    'mapHtmlTwo' => '',
    'mapHtmlThree' => '',
    'mapPlaceholder' => '[ Google Maps Embed - 3 Outlet Pins ]',
    'stores' => [],
    'paddingTop' => 36,
    'paddingBottom' => 70,
    'marginTop' => 0,
    'marginBottom' => 0,
]);

$stores = is_array($attrs['stores']) ? $attrs['stores'] : [];
$section_id = sanitize_title($attrs['sectionId']);
$map_section_id = sanitize_title($attrs['mapSectionId']);
$map_shortcode = trim((string) $attrs['mapShortcode']);
$map_slots = array_values(array_filter([
    trim((string) $attrs['mapHtmlOne']),
    trim((string) $attrs['mapHtmlTwo']),
    trim((string) $attrs['mapHtmlThree']),
]));
$allowed_map_html = [
    'iframe' => [
        'src' => true,
        'width' => true,
        'height' => true,
        'style' => true,
        'allow' => true,
        'allowfullscreen' => true,
        'loading' => true,
        'referrerpolicy' => true,
        'title' => true,
        'class' => true,
    ],
    'div' => [
        'class' => true,
        'style' => true,
        'id' => true,
    ],
    'span' => [
        'class' => true,
        'style' => true,
    ],
    'p' => [
        'class' => true,
        'style' => true,
    ],
    'br' => [],
    'small' => [
        'class' => true,
        'style' => true,
    ],
    'a' => [
        'href' => true,
        'target' => true,
        'rel' => true,
        'style' => true,
        'class' => true,
    ],
];
$style = sprintf(
    '--contact-locations-padding-top:%dpx;--contact-locations-padding-bottom:%dpx;--contact-locations-margin-top:%dpx;--contact-locations-margin-bottom:%dpx;',
    absint($attrs['paddingTop']),
    absint($attrs['paddingBottom']),
    absint($attrs['marginTop']),
    absint($attrs['marginBottom'])
);
$wrapper_attributes = get_block_wrapper_attributes(['class' => 'contact-locations', 'style' => $style]);
?>
<section id="<?php echo esc_attr($section_id); ?>" <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <div class="contact-locations__inner">
        <?php if (!empty($attrs['eyebrow'])) : ?><p class="contact-locations__eyebrow"><?php echo esc_html($attrs['eyebrow']); ?></p><?php endif; ?>
        <?php if (!empty($stores)) : ?>
            <div class="contact-locations__grid">
                <?php foreach ($stores as $store) : ?>
                    <?php
                    $store = wp_parse_args((array) $store, [
                        'name' => '',
                        'address' => '',
                        'phone' => '',
                        'hours' => '',
                        'directionsUrl' => '#',
                    ]);
                    ?>
                    <article class="contact-locations__card">
                        <?php if (!empty($store['name'])) : ?><h3 class="contact-locations__name"><?php echo esc_html($store['name']); ?></h3><?php endif; ?>
                        <?php if (!empty($store['address'])) : ?><p class="contact-locations__address"><?php echo nl2br(esc_html($store['address'])); ?></p><?php endif; ?>
                        <?php if (!empty($store['phone'])) : ?>
                            <p class="contact-locations__line"><span aria-hidden="true">☎</span> <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $store['phone'])); ?>"><?php echo esc_html($store['phone']); ?></a></p>
                        <?php endif; ?>
                        <?php if (!empty($store['hours'])) : ?><p class="contact-locations__line"><span aria-hidden="true">◷</span> <?php echo esc_html($store['hours']); ?></p><?php endif; ?>
                        <a class="contact-locations__button az-button az-button--small" href="<?php echo esc_url($store['directionsUrl'] ?: '#'); ?>" target="_blank" rel="noopener">📍 <?php esc_html_e('Get Directions', 'ai-zippy-child'); ?></a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($attrs['note'])) : ?><p class="contact-locations__note">⚠ <?php echo esc_html($attrs['note']); ?></p><?php endif; ?>

        <div id="<?php echo esc_attr($map_section_id); ?>" class="contact-locations__map-wrap">
            <?php if (!empty($attrs['mapHeading'])) : ?><h2 class="contact-locations__map-heading"><?php echo esc_html($attrs['mapHeading']); ?></h2><?php endif; ?>
            <div class="contact-locations__map<?php echo count($map_slots) > 1 ? ' contact-locations__map--multi' : ''; ?>">
                <?php if (!empty($map_slots)) : ?>
                    <?php foreach ($map_slots as $map_html) : ?>
                        <div class="contact-locations__map-item">
                            <?php echo wp_kses(do_shortcode(shortcode_unautop($map_html)), $allowed_map_html); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    <?php endforeach; ?>
                <?php elseif ($map_shortcode !== '') : ?>
                    <div class="contact-locations__map-item">
                        <?php echo wp_kses(do_shortcode(shortcode_unautop($map_shortcode)), $allowed_map_html); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                <?php else : ?>
                    <span><?php echo esc_html($attrs['mapPlaceholder']); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
