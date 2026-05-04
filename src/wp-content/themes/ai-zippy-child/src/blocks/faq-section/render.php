<?php
/**
 * FAQ Section block render.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

$attrs = wp_parse_args($attributes ?? [], [
    'heading'             => 'Frequently Asked Questions',
    'backgroundColor'     => '#f8f7f4',
    'textColor'           => '#000000',
    'answerColor'         => '#8f8a83',
    'cardBackgroundColor' => '#ffffff',
    'accentColor'         => '#f16a6c',
    'maxWidth'            => 1735,
    'columns'             => 2,
    'gap'                 => 34,
    'paddingTop'          => 56,
    'paddingBottom'       => 64,
    'marginTop'           => 0,
    'marginBottom'        => 0,
    'topics'              => [],
]);

$topics = is_array($attrs['topics']) ? $attrs['topics'] : [];
$columns = max(1, min(2, absint($attrs['columns'])));

$style = sprintf(
    '--faq-section-bg:%1$s;--faq-section-color:%2$s;--faq-section-answer:%3$s;--faq-section-card-bg:%4$s;--faq-section-accent:%5$s;--faq-section-max-width:%6$dpx;--faq-section-columns:%7$d;--faq-section-gap:%8$dpx;--faq-section-padding-top:%9$dpx;--faq-section-padding-bottom:%10$dpx;--faq-section-margin-top:%11$dpx;--faq-section-margin-bottom:%12$dpx;',
    esc_attr(sanitize_hex_color($attrs['backgroundColor']) ?: '#f8f7f4'),
    esc_attr(sanitize_hex_color($attrs['textColor']) ?: '#000000'),
    esc_attr(sanitize_hex_color($attrs['answerColor']) ?: '#8f8a83'),
    esc_attr(sanitize_hex_color($attrs['cardBackgroundColor']) ?: '#ffffff'),
    esc_attr(sanitize_hex_color($attrs['accentColor']) ?: '#f16a6c'),
    absint($attrs['maxWidth']),
    $columns,
    absint($attrs['gap']),
    absint($attrs['paddingTop']),
    absint($attrs['paddingBottom']),
    absint($attrs['marginTop']),
    absint($attrs['marginBottom'])
);

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'faq-section',
    'style' => $style,
]);

$block_id = wp_unique_id('faq-section-');
?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <div class="faq-section__inner">
        <?php if (trim((string) $attrs['heading']) !== '') : ?>
            <h2 class="faq-section__heading"><?php echo wp_kses_post($attrs['heading']); ?></h2>
        <?php endif; ?>

        <?php if (!empty($topics)) : ?>
            <div class="faq-section__topics">
                <?php foreach ($topics as $topic_index => $topic) : ?>
                    <?php
                    $topic_title = isset($topic['title']) ? trim((string) $topic['title']) : '';
                    $items = isset($topic['items']) && is_array($topic['items']) ? $topic['items'] : [];
                    ?>
                    <?php if (!empty($items)) : ?>
                        <div class="faq-section__topic">
                            <?php if ($topic_title !== '') : ?>
                                <p class="faq-section__topic-title"><?php echo esc_html($topic_title); ?></p>
                            <?php endif; ?>

                            <div class="faq-section__list">
                                <?php foreach ($items as $item_index => $item) : ?>
                                    <?php
                                    $question = isset($item['question']) ? trim((string) $item['question']) : '';
                                    $answer = isset($item['answer']) ? trim((string) $item['answer']) : '';
                                    $item_id = esc_attr($block_id . '-' . $topic_index . '-' . $item_index);
                                    ?>
                                    <?php if ($question !== '' || $answer !== '') : ?>
                                        <article class="faq-section__item">
                                            <?php if ($question !== '') : ?>
                                                <button class="faq-section__question" type="button" aria-expanded="false" aria-controls="<?php echo $item_id; ?>">
                                                    <span><?php echo wp_kses_post($question); ?></span>
                                                    <span class="faq-section__icon" aria-hidden="true"></span>
                                                </button>
                                            <?php endif; ?>

                                            <?php if ($answer !== '') : ?>
                                                <div class="faq-section__answer" id="<?php echo $item_id; ?>" hidden>
                                                    <?php echo wp_kses_post(wpautop($answer)); ?>
                                                </div>
                                            <?php endif; ?>
                                        </article>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
