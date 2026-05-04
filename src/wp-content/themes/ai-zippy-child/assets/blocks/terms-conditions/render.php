<?php
/**
 * Terms & Conditions block render.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

$attrs = wp_parse_args($attributes ?? [], [
    'eyebrow'             => 'Tom & Stefanie',
    'heading'             => 'Terms & Conditions',
    'description'         => 'Please read these terms carefully before browsing or purchasing from our website.',
    'backgroundColor'     => '#f8f7f4',
    'textColor'           => '#000000',
    'mutedColor'          => '#8f8a83',
    'cardBackgroundColor' => '#ffffff',
    'accentColor'         => '#f16a6c',
    'maxWidth'            => 1180,
    'paddingTop'          => 72,
    'paddingBottom'       => 86,
    'marginTop'           => 0,
    'marginBottom'        => 0,
    'sections'            => [],
]);

$sections = is_array($attrs['sections']) ? $attrs['sections'] : [];
$style = sprintf(
    '--terms-bg:%1$s;--terms-color:%2$s;--terms-muted:%3$s;--terms-card-bg:%4$s;--terms-accent:%5$s;--terms-max-width:%6$dpx;--terms-padding-top:%7$dpx;--terms-padding-bottom:%8$dpx;--terms-margin-top:%9$dpx;--terms-margin-bottom:%10$dpx;',
    esc_attr(sanitize_hex_color($attrs['backgroundColor']) ?: '#f8f7f4'),
    esc_attr(sanitize_hex_color($attrs['textColor']) ?: '#000000'),
    esc_attr(sanitize_hex_color($attrs['mutedColor']) ?: '#8f8a83'),
    esc_attr(sanitize_hex_color($attrs['cardBackgroundColor']) ?: '#ffffff'),
    esc_attr(sanitize_hex_color($attrs['accentColor']) ?: '#f16a6c'),
    absint($attrs['maxWidth']),
    absint($attrs['paddingTop']),
    absint($attrs['paddingBottom']),
    absint($attrs['marginTop']),
    absint($attrs['marginBottom'])
);

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'terms-conditions',
    'style' => $style,
]);

$render_body = static function (string $body): void {
    $lines = array_filter(array_map('trim', preg_split('/\R/', $body) ?: []));

    foreach ($lines as $line) {
        echo '<p>' . wp_kses_post($line) . '</p>';
    }
};
?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <div class="terms-conditions__inner">
        <header class="terms-conditions__header">
            <?php if (trim((string) $attrs['eyebrow']) !== '') : ?>
                <p class="terms-conditions__eyebrow"><?php echo esc_html($attrs['eyebrow']); ?></p>
            <?php endif; ?>

            <?php if (trim((string) $attrs['heading']) !== '') : ?>
                <h1 class="terms-conditions__heading"><?php echo wp_kses_post($attrs['heading']); ?></h1>
            <?php endif; ?>

            <?php if (trim((string) $attrs['description']) !== '') : ?>
                <p class="terms-conditions__description"><?php echo wp_kses_post($attrs['description']); ?></p>
            <?php endif; ?>
        </header>

        <?php if (!empty($sections)) : ?>
            <div class="terms-conditions__list">
                <?php foreach ($sections as $index => $section) : ?>
                    <?php
                    $title = isset($section['title']) ? trim((string) $section['title']) : '';
                    $body = isset($section['body']) ? trim((string) $section['body']) : '';
                    $items = isset($section['items']) && is_array($section['items']) ? array_filter(array_map('trim', $section['items'])) : [];
                    ?>
                    <?php if ($title !== '' || $body !== '' || !empty($items)) : ?>
                        <article class="terms-conditions__section">
                            <div class="terms-conditions__number"><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></div>
                            <div class="terms-conditions__content">
                                <?php if ($title !== '') : ?>
                                    <h2 class="terms-conditions__title"><?php echo esc_html($title); ?></h2>
                                <?php endif; ?>

                                <?php $render_body($body); ?>

                                <?php if (!empty($items)) : ?>
                                    <ul>
                                        <?php foreach ($items as $item) : ?>
                                            <li><?php echo wp_kses_post($item); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
