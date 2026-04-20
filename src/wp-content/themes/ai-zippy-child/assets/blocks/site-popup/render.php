<?php
/**
 * Site Popup block render.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

$attrs = wp_parse_args($attributes ?? [], [
    'enabled'            => true,
    'popupId'            => 'members-club',
    'width'              => 500,
    'height'             => 0,
    'maxHeight'          => 88,
    'borderRadius'       => 22,
    'bannerHeight'       => 150,
    'trigger'            => 'delay',
    'delaySeconds'       => 5,
    'scrollPercent'      => 40,
    'targetSelector'     => '',
    'showLauncherButton' => false,
    'launcherText'       => 'Open Offer',
    'firstVisitOnly'     => false,
    'skipFirstVisit'     => false,
    'rememberOnClose'    => true,
    'showDontShowAgain'  => true,
    'dontShowLabel'      => "No thanks, I'll browse as guest",
    'storageMode'        => 'localStorage',
    'suppressDays'       => 14,
    'closeOnOverlay'     => true,
    'badgeText'          => 'MEMBERS GET MORE',
    'heroImageUrl'       => '',
    'heroImageAlt'       => '',
    'heroIcons'          => '🧸 🎁 🎨 🧩 🚀',
    'imageNote'          => 'REPLACE WITH BRAND PHOTOGRAPHY',
    'kicker'             => 'MEMBERS GET MORE',
    'heading'            => 'Join the Tom & Stefanie Club',
    'description'        => 'Sign up for early access to new arrivals, exclusive deals, and a birthday treat - just for you.',
    'benefits'           => [],
    'emailPlaceholder'   => 'Enter your email address',
    'formAction'         => '',
    'emailFieldName'     => 'email',
    'buttonText'         => 'Join Free Now',
    'buttonUrl'          => '',
    'buttonBackground'   => '#24231f',
    'buttonColor'        => '#ffffff',
    'overlayColor'       => 'rgba(0, 0, 0, 0.56)',
    'backgroundColor'    => '#ffffff',
]);

if (!(bool) $attrs['enabled']) {
    return;
}

$default_benefits = [
    ['icon' => '🎟️', 'label' => 'Welcome<br>Voucher'],
    ['icon' => '🎂', 'label' => 'Birthday<br>Treat'],
    ['icon' => '⚡', 'label' => 'Early<br>Access'],
    ['icon' => '💰', 'label' => 'Earn & Redeem<br>Points'],
];
$benefits = is_array($attrs['benefits']) && !empty($attrs['benefits']) ? $attrs['benefits'] : $default_benefits;
$popup_id = sanitize_key($attrs['popupId'] ?: 'site-popup');
$style = sprintf(
    '--site-popup-width:%dpx;--site-popup-height:%s;--site-popup-max-height:%dvh;--site-popup-radius:%dpx;--site-popup-banner-height:%dpx;--site-popup-button-bg:%s;--site-popup-button-color:%s;--site-popup-bg:%s;--site-popup-overlay:%s;',
    max(280, absint($attrs['width'])),
    absint($attrs['height']) > 0 ? absint($attrs['height']) . 'px' : 'auto',
    max(40, min(100, absint($attrs['maxHeight']))),
    max(0, min(80, absint($attrs['borderRadius']))),
    max(80, min(420, absint($attrs['bannerHeight']))),
    esc_attr($attrs['buttonBackground']),
    esc_attr($attrs['buttonColor']),
    esc_attr($attrs['backgroundColor']),
    esc_attr($attrs['overlayColor'])
);
$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'site-popup',
    'style' => $style,
]);
?>

<div
    <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    data-popup-id="<?php echo esc_attr($popup_id); ?>"
    data-trigger="<?php echo esc_attr(sanitize_key($attrs['trigger'])); ?>"
    data-delay="<?php echo esc_attr(max(0, absint($attrs['delaySeconds']))); ?>"
    data-scroll-percent="<?php echo esc_attr(max(5, min(95, absint($attrs['scrollPercent'])))); ?>"
    data-target-selector="<?php echo esc_attr($attrs['targetSelector']); ?>"
    data-first-visit-only="<?php echo (bool) $attrs['firstVisitOnly'] ? 'true' : 'false'; ?>"
    data-skip-first-visit="<?php echo (bool) $attrs['skipFirstVisit'] ? 'true' : 'false'; ?>"
    data-remember-on-close="<?php echo (bool) $attrs['rememberOnClose'] ? 'true' : 'false'; ?>"
    data-storage-mode="<?php echo esc_attr(sanitize_key($attrs['storageMode'])); ?>"
    data-suppress-days="<?php echo esc_attr(max(1, absint($attrs['suppressDays']))); ?>"
    data-close-on-overlay="<?php echo (bool) $attrs['closeOnOverlay'] ? 'true' : 'false'; ?>"
>
    <?php if ((bool) $attrs['showLauncherButton']) : ?>
        <button class="site-popup__launcher az-button az-button--medium" type="button">
            <?php echo esc_html($attrs['launcherText']); ?>
        </button>
    <?php endif; ?>

    <div class="site-popup__overlay" hidden></div>
    <div class="site-popup__dialog" role="dialog" aria-modal="true" aria-labelledby="<?php echo esc_attr($popup_id); ?>-heading" tabindex="-1" hidden>
        <button class="site-popup__close" type="button" aria-label="<?php esc_attr_e('Close popup', 'ai-zippy-child'); ?>">×</button>
        <div class="site-popup__hero">
            <?php if (!empty($attrs['heroImageUrl'])) : ?>
                <img src="<?php echo esc_url($attrs['heroImageUrl']); ?>" alt="<?php echo esc_attr($attrs['heroImageAlt']); ?>">
            <?php else : ?>
                <div class="site-popup__hero-icons" aria-hidden="true"><?php echo esc_html($attrs['heroIcons']); ?></div>
            <?php endif; ?>

            <?php if (trim((string) $attrs['badgeText']) !== '') : ?>
                <p class="site-popup__badge"><?php echo esc_html($attrs['badgeText']); ?></p>
            <?php endif; ?>

            <?php if (trim((string) $attrs['imageNote']) !== '') : ?>
                <p class="site-popup__image-note"><?php echo esc_html($attrs['imageNote']); ?></p>
            <?php endif; ?>
        </div>

        <div class="site-popup__body">
            <?php if (trim((string) $attrs['kicker']) !== '') : ?>
                <p class="site-popup__kicker"><?php echo esc_html($attrs['kicker']); ?></p>
            <?php endif; ?>

            <?php if (trim((string) $attrs['heading']) !== '') : ?>
                <h2 class="site-popup__heading" id="<?php echo esc_attr($popup_id); ?>-heading"><?php echo wp_kses_post($attrs['heading']); ?></h2>
            <?php endif; ?>

            <?php if (trim((string) $attrs['description']) !== '') : ?>
                <p class="site-popup__description"><?php echo wp_kses_post($attrs['description']); ?></p>
            <?php endif; ?>

            <div class="site-popup__benefits">
                <?php foreach ($benefits as $benefit) : ?>
                    <?php $benefit = wp_parse_args((array) $benefit, ['icon' => '', 'label' => '']); ?>
                    <div class="site-popup__benefit">
                        <span aria-hidden="true"><?php echo esc_html($benefit['icon']); ?></span>
                        <p><?php echo wp_kses_post($benefit['label']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (trim((string) $attrs['formAction']) !== '') : ?>
                <form class="site-popup__form" action="<?php echo esc_url($attrs['formAction']); ?>" method="post">
                    <input class="site-popup__email" name="<?php echo esc_attr($attrs['emailFieldName'] ?: 'email'); ?>" type="email" placeholder="<?php echo esc_attr($attrs['emailPlaceholder']); ?>" required>
                    <button class="site-popup__submit" type="submit"><?php echo esc_html($attrs['buttonText']); ?></button>
                </form>
            <?php else : ?>
                <div class="site-popup__form">
                    <input class="site-popup__email" name="<?php echo esc_attr($attrs['emailFieldName'] ?: 'email'); ?>" type="email" placeholder="<?php echo esc_attr($attrs['emailPlaceholder']); ?>">
                    <?php if (trim((string) $attrs['buttonUrl']) !== '') : ?>
                        <a class="site-popup__submit" href="<?php echo esc_url($attrs['buttonUrl']); ?>"><?php echo esc_html($attrs['buttonText']); ?></a>
                    <?php else : ?>
                        <button class="site-popup__submit" type="button"><?php echo esc_html($attrs['buttonText']); ?></button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ((bool) $attrs['showDontShowAgain'] && trim((string) $attrs['dontShowLabel']) !== '') : ?>
                <button class="site-popup__dismiss" type="button"><?php echo esc_html($attrs['dontShowLabel']); ?></button>
            <?php endif; ?>
        </div>
    </div>
</div>
