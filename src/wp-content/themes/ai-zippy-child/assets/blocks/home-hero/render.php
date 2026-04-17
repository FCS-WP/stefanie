<?php
/**
 * Home Hero block render.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

$attrs = wp_parse_args($attributes ?? [], [
    'eyebrow' => "Singapore's go-to toy & gift store",
    'heading' => "The Right Gift,\nEvery Time.",
    'description' => 'From first birthdays to last-minute finds - curated toys & gifts that always land well. Online and in-store across Singapore.',
    'primaryButtonText' => 'Shop All Toys',
    'primaryButtonUrl' => '/shop',
    'secondaryButtonText' => 'Join T&S Points',
    'secondaryButtonUrl' => '/rewards',
    'backgroundImageUrl' => '',
    'backgroundImageAlt' => '',
    'imageObjectFit' => 'cover',
    'imageObjectPosition' => 'center center',
    'minHeight' => 850,
    'overlayOpacity' => 48,
]);

$allowed_object_fits = ['cover', 'contain', 'fill', 'scale-down'];
$allowed_positions   = ['center center', 'left center', 'right center', 'center top', 'center bottom'];
$object_fit          = in_array($attrs['imageObjectFit'], $allowed_object_fits, true) ? $attrs['imageObjectFit'] : 'cover';
$object_position     = in_array($attrs['imageObjectPosition'], $allowed_positions, true) ? $attrs['imageObjectPosition'] : 'center center';
$min_height          = min(900, max(320, absint($attrs['minHeight'])));
$overlay_opacity     = min(80, max(0, absint($attrs['overlayOpacity']))) / 100;
$style               = sprintf(
    '--home-hero-min-height:%1$dpx;--home-hero-overlay-opacity:%2$s;--home-hero-image-fit:%3$s;--home-hero-image-position:%4$s;',
    $min_height,
    esc_attr((string) $overlay_opacity),
    esc_attr($object_fit),
    esc_attr($object_position)
);

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'home-hero',
    'style' => $style,
]);
?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <?php if (!empty($attrs['backgroundImageUrl'])) : ?>
        <img
            class="home-hero__image"
            src="<?php echo esc_url($attrs['backgroundImageUrl']); ?>"
            alt="<?php echo esc_attr($attrs['backgroundImageAlt']); ?>"
        />
    <?php endif; ?>

    <div class="home-hero__overlay" aria-hidden="true"></div>

    <div class="home-hero__inner">
        <?php if (trim((string) $attrs['eyebrow']) !== '') : ?>
            <p class="home-hero__eyebrow"><?php echo esc_html($attrs['eyebrow']); ?></p>
        <?php endif; ?>

        <?php if (trim((string) $attrs['heading']) !== '') : ?>
            <h1 class="home-hero__heading az-title-heading"><?php echo nl2br(esc_html($attrs['heading'])); ?></h1>
        <?php endif; ?>

        <?php if (trim((string) $attrs['description']) !== '') : ?>
            <p class="home-hero__description"><?php echo esc_html($attrs['description']); ?></p>
        <?php endif; ?>

        <div class="home-hero__actions">
            <?php if (trim((string) $attrs['primaryButtonText']) !== '') : ?>
                <a class="home-hero__button home-hero__button--primary az-button az-button--medium" href="<?php echo esc_url($attrs['primaryButtonUrl'] ?: '#'); ?>">
                    <?php echo esc_html($attrs['primaryButtonText']); ?>
                </a>
            <?php endif; ?>

            <?php if (trim((string) $attrs['secondaryButtonText']) !== '') : ?>
                <a class="home-hero__button home-hero__button--secondary az-button az-button--medium" href="<?php echo esc_url($attrs['secondaryButtonUrl'] ?: '#'); ?>">
                    <?php echo esc_html($attrs['secondaryButtonText']); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>
