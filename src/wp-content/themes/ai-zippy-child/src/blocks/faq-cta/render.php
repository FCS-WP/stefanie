<?php
/**
 * FAQ CTA block render.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

$attrs = wp_parse_args($attributes ?? [], [
    'backgroundColor'      => '#ffffff',
    'cardBackgroundColor'  => '#f8f7f4',
    'textColor'            => '#000000',
    'mutedColor'           => '#8f8a83',
    'whatsappColor'        => '#25d366',
    'submitColor'          => '#cba542',
    'whatsappTitle'        => 'Chat with us on WhatsApp',
    'whatsappNote'         => 'Quick replies within business hours',
    'whatsappPhone'        => '+65 8086 3940',
    'whatsappButtonText'   => 'WhatsApp Now',
    'whatsappUrl'          => 'https://wa.me/6580863940',
    'whatsappImageId'      => 0,
    'whatsappImageUrl'     => '',
    'whatsappImageAlt'     => '',
    'formHeading'          => 'Send Us a Message',
    'formShortcode'        => '',
    'nameLabel'            => 'Your Name',
    'namePlaceholder'      => 'First & last name',
    'emailLabel'           => 'Email Address',
    'emailPlaceholder'     => 'hello@email.com',
    'messageLabel'         => 'Message',
    'messagePlaceholder'   => 'What would you like to know?',
    'submitText'           => 'Send Message',
    'paddingTop'           => 48,
    'paddingBottom'        => 52,
    'marginTop'            => 0,
    'marginBottom'         => 0,
]);

$style = sprintf(
    '--faq-cta-bg:%1$s;--faq-cta-card-bg:%2$s;--faq-cta-color:%3$s;--faq-cta-muted:%4$s;--faq-cta-whatsapp:%5$s;--faq-cta-submit:%6$s;--faq-cta-padding-top:%7$dpx;--faq-cta-padding-bottom:%8$dpx;--faq-cta-margin-top:%9$dpx;--faq-cta-margin-bottom:%10$dpx;',
    esc_attr(sanitize_hex_color($attrs['backgroundColor']) ?: '#ffffff'),
    esc_attr(sanitize_hex_color($attrs['cardBackgroundColor']) ?: '#f8f7f4'),
    esc_attr(sanitize_hex_color($attrs['textColor']) ?: '#000000'),
    esc_attr(sanitize_hex_color($attrs['mutedColor']) ?: '#8f8a83'),
    esc_attr(sanitize_hex_color($attrs['whatsappColor']) ?: '#25d366'),
    esc_attr(sanitize_hex_color($attrs['submitColor']) ?: '#cba542'),
    absint($attrs['paddingTop']),
    absint($attrs['paddingBottom']),
    absint($attrs['marginTop']),
    absint($attrs['marginBottom'])
);

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'faq-cta',
    'style' => $style,
]);

$form_shortcode = trim((string) $attrs['formShortcode']);
$whatsapp_image_url = trim((string) $attrs['whatsappImageUrl']);
?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <div class="faq-cta__inner">
        <div class="faq-cta__whatsapp-card">
            <span class="faq-cta__whatsapp-mark<?php echo $whatsapp_image_url !== '' ? ' has-image' : ''; ?>" aria-hidden="true">
                <?php if ($whatsapp_image_url !== '') : ?>
                    <img src="<?php echo esc_url($whatsapp_image_url); ?>" alt="<?php echo esc_attr($attrs['whatsappImageAlt']); ?>">
                <?php endif; ?>
            </span>

            <?php if (trim((string) $attrs['whatsappTitle']) !== '') : ?>
                <h3 class="faq-cta__whatsapp-title"><?php echo wp_kses_post($attrs['whatsappTitle']); ?></h3>
            <?php endif; ?>

            <?php if (trim((string) $attrs['whatsappNote']) !== '') : ?>
                <p class="faq-cta__whatsapp-note"><?php echo wp_kses_post($attrs['whatsappNote']); ?></p>
            <?php endif; ?>

            <?php if (trim((string) $attrs['whatsappPhone']) !== '') : ?>
                <p class="faq-cta__whatsapp-phone"><?php echo esc_html($attrs['whatsappPhone']); ?></p>
            <?php endif; ?>

            <?php if (trim((string) $attrs['whatsappButtonText']) !== '') : ?>
                <a class="faq-cta__whatsapp-button az-button az-button--medium" href="<?php echo esc_url($attrs['whatsappUrl'] ?: '#'); ?>">
                    <?php echo esc_html($attrs['whatsappButtonText']); ?> <span aria-hidden="true">-&gt;</span>
                </a>
            <?php endif; ?>
        </div>

        <div class="faq-cta__message-card">
            <?php if (trim((string) $attrs['formHeading']) !== '') : ?>
                <h3 class="faq-cta__form-heading"><?php echo wp_kses_post($attrs['formHeading']); ?></h3>
            <?php endif; ?>

            <?php if ($form_shortcode !== '') : ?>
                <div class="faq-cta__form-shell">
                    <?php echo do_shortcode(shortcode_unautop($form_shortcode)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            <?php else : ?>
                <div class="faq-cta__form" aria-label="<?php esc_attr_e('Message form preview', 'ai-zippy-child'); ?>">
                    <label class="faq-cta__field">
                        <span><?php echo esc_html($attrs['nameLabel']); ?></span>
                        <input type="text" placeholder="<?php echo esc_attr($attrs['namePlaceholder']); ?>">
                    </label>
                    <label class="faq-cta__field">
                        <span><?php echo esc_html($attrs['emailLabel']); ?></span>
                        <input type="email" placeholder="<?php echo esc_attr($attrs['emailPlaceholder']); ?>">
                    </label>
                    <label class="faq-cta__field faq-cta__field--wide">
                        <span><?php echo esc_html($attrs['messageLabel']); ?></span>
                        <input type="text" placeholder="<?php echo esc_attr($attrs['messagePlaceholder']); ?>">
                    </label>
                    <button class="faq-cta__submit" type="button">
                        <?php echo esc_html($attrs['submitText']); ?> <span aria-hidden="true">-&gt;</span>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
