<?php
/**
 * Return & Exchange Policy block render.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

$attrs = wp_parse_args($attributes ?? [], [
    'eyebrow'             => 'Tom & Stefanie',
    'heading'             => 'Return & Exchange Policy',
    'description'         => 'At Tom & Stefanie, we take extra care in ensuring all products are checked before delivery. Due to hygiene and safety considerations for baby and children products, our return policy is as follows:',
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
    '--return-bg:%1$s;--return-color:%2$s;--return-muted:%3$s;--return-card-bg:%4$s;--return-accent:%5$s;--return-max-width:%6$dpx;--return-padding-top:%7$dpx;--return-padding-bottom:%8$dpx;--return-margin-top:%9$dpx;--return-margin-bottom:%10$dpx;',
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
    'class' => 'return-policy',
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
    <div class="return-policy__inner">
        <header class="return-policy__header">
            <?php if (trim((string) $attrs['eyebrow']) !== '') : ?>
                <p class="return-policy__eyebrow"><?php echo esc_html($attrs['eyebrow']); ?></p>
            <?php endif; ?>

            <?php if (trim((string) $attrs['heading']) !== '') : ?>
                <h1 class="return-policy__heading"><?php echo wp_kses_post($attrs['heading']); ?></h1>
            <?php endif; ?>

            <?php if (trim((string) $attrs['description']) !== '') : ?>
                <p class="return-policy__description"><?php echo wp_kses_post($attrs['description']); ?></p>
            <?php endif; ?>
        </header>

        <?php if (!empty($sections)) : ?>
            <div class="return-policy__list">
                <?php foreach ($sections as $index => $section) : ?>
                    <?php
                    $title = isset($section['title']) ? trim((string) $section['title']) : '';
                    $body = isset($section['body']) ? trim((string) $section['body']) : '';
                    $items = isset($section['items']) && is_array($section['items']) ? array_filter(array_map('trim', $section['items'])) : [];
                    ?>
                    <?php if ($title !== '' || $body !== '' || !empty($items)) : ?>
                        <article class="return-policy__section">
                            <div class="return-policy__number"><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></div>
                            <div class="return-policy__content">
                                <?php if ($title !== '') : ?>
                                    <h2 class="return-policy__title"><?php echo esc_html($title); ?></h2>
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
