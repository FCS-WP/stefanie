<?php
defined('ABSPATH') || exit;

$attrs = wp_parse_args($attributes ?? [], [
    'heading' => '3 Stores Across Singapore',
    'stores' => [],
    'paddingTop' => 74,
    'paddingBottom' => 90,
    'marginTop' => 0,
    'marginBottom' => 0,
]);

$stores = is_array($attrs['stores']) ? $attrs['stores'] : [];
$style = sprintf('--home-stores-padding-top:%dpx;--home-stores-padding-bottom:%dpx;--home-stores-margin-top:%dpx;--home-stores-margin-bottom:%dpx;', absint($attrs['paddingTop']), absint($attrs['paddingBottom']), absint($attrs['marginTop']), absint($attrs['marginBottom']));
$wrapper_attributes = get_block_wrapper_attributes(['class' => 'home-stores', 'style' => $style]);
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <div class="home-stores__inner">
        <h2 class="home-stores__heading az-section-heading"><?php echo wp_kses_post($attrs['heading']); ?></h2>
        <div class="home-stores__grid">
            <?php foreach ($stores as $store) : ?>
                <?php $store = wp_parse_args((array) $store, ['title' => '', 'address' => '', 'buttonText' => 'GET DIRECTION', 'buttonUrl' => '#', 'imageUrl' => '', 'imageAlt' => '']); ?>
                <article class="home-stores__card">
                    <div class="home-stores__image">
                        <?php if (!empty($store['imageUrl'])) : ?>
                            <img src="<?php echo esc_url($store['imageUrl']); ?>" alt="<?php echo esc_attr($store['imageAlt'] ?: $store['title']); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="home-stores__body">
                        <h3 class="home-stores__title"><?php echo esc_html($store['title']); ?></h3>
                        <p class="home-stores__address"><?php echo wp_kses_post($store['address']); ?></p>
                        <a class="home-stores__button az-button az-button--small" href="<?php echo esc_url($store['buttonUrl'] ?: '#'); ?>"><?php echo esc_html($store['buttonText']); ?></a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
