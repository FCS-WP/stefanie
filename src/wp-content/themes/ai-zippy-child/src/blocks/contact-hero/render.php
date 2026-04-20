<?php
defined('ABSPATH') || exit;

$attrs = wp_parse_args($attributes ?? [], [
    'sectionId' => 'contact-hero',
    'eyebrow' => 'C1 · PAGE HERO',
    'breadcrumbHomeLabel' => 'Home',
    'breadcrumbHomeUrl' => '/',
    'breadcrumbCurrentLabel' => 'Contact Us',
    'heading' => 'Get in Touch',
    'description' => "Visit us in store, WhatsApp us, or drop us a message - we'd love to hear from you.",
    'chips' => [],
    'paddingTop' => 28,
    'paddingBottom' => 34,
    'marginTop' => 0,
    'marginBottom' => 0,
]);

$chips = is_array($attrs['chips']) ? $attrs['chips'] : [];
$section_id = sanitize_title($attrs['sectionId']);
$style = sprintf(
    '--contact-hero-padding-top:%dpx;--contact-hero-padding-bottom:%dpx;--contact-hero-margin-top:%dpx;--contact-hero-margin-bottom:%dpx;',
    absint($attrs['paddingTop']),
    absint($attrs['paddingBottom']),
    absint($attrs['marginTop']),
    absint($attrs['marginBottom'])
);
$wrapper_attributes = get_block_wrapper_attributes(['class' => 'contact-hero', 'style' => $style]);
?>
<section id="<?php echo esc_attr($section_id); ?>" <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <div class="contact-hero__inner">
        <?php if (!empty($attrs['eyebrow'])) : ?><p class="contact-hero__eyebrow"><?php echo esc_html($attrs['eyebrow']); ?></p><?php endif; ?>
        <nav class="contact-hero__breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'ai-zippy-child'); ?>">
            <a href="<?php echo esc_url($attrs['breadcrumbHomeUrl']); ?>"><?php echo esc_html($attrs['breadcrumbHomeLabel']); ?></a>
            <span aria-hidden="true">/</span>
            <span><?php echo esc_html($attrs['breadcrumbCurrentLabel']); ?></span>
        </nav>
        <?php if (!empty($attrs['heading'])) : ?><h1 class="contact-hero__heading"><?php echo wp_kses_post($attrs['heading']); ?></h1><?php endif; ?>
        <?php if (!empty($attrs['description'])) : ?><p class="contact-hero__description"><?php echo wp_kses_post($attrs['description']); ?></p><?php endif; ?>
        <?php if (!empty($chips)) : ?>
            <div class="contact-hero__chips">
                <?php foreach ($chips as $chip) : ?>
                    <?php
                    $chip = wp_parse_args((array) $chip, ['label' => '', 'icon' => 'chat', 'target' => '#']);
                    if (trim((string) $chip['label']) === '') {
                        continue;
                    }
                    ?>
                    <a class="contact-hero__chip contact-hero__chip--<?php echo esc_attr(sanitize_html_class($chip['icon'])); ?>" href="<?php echo esc_url($chip['target'] ?: '#'); ?>">
                        <span class="contact-hero__chip-icon" aria-hidden="true"></span>
                        <span><?php echo esc_html($chip['label']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
