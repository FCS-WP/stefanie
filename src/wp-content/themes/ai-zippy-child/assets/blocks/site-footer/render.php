<?php
/**
 * Site Footer block render.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

$attrs = wp_parse_args($attributes ?? [], [
    'logoBefore'    => 'Tom',
    'logoAccent'    => '&',
    'logoAfter'     => 'Stefanie',
    'logoUrl'       => '/',
    'logoImageId'   => 0,
    'logoImageUrl'  => '',
    'logoImageAlt'  => '',
    'logoImageWidth' => 370,
    'logoImageMinHeight' => 58,
    'logoImageObjectFit' => 'contain',
    'logoImageObjectPosition' => 'left center',
    'taglineLineOne' => 'Kids Toy Store SG',
    'taglineLineTwo' => 'Trend-forward gifts',
    'email'         => 'tomnstefanie@gmail.com',
    'shopMenuId'    => 0,
    'shopTitle'     => 'Shop',
    'shopOneLabel'  => 'All Products',
    'shopOneUrl'    => '/shop',
    'shopTwoLabel'  => 'Shop by Occasion',
    'shopTwoUrl'    => '/shop',
    'shopThreeLabel' => 'Shop by Category',
    'shopThreeUrl'  => '/shop',
    'shopFourLabel' => 'Shop by Age',
    'shopFourUrl'   => '/shop',
    'shopFiveLabel' => 'New In',
    'shopFiveUrl'   => '/shop',
    'infoMenuId'    => 0,
    'infoTitle'     => 'Info',
    'infoOneLabel'  => 'About Us',
    'infoOneUrl'    => '/about-us',
    'infoTwoLabel'  => 'Blog',
    'infoTwoUrl'    => '/blog',
    'infoThreeLabel' => 'Store Locations',
    'infoThreeUrl'  => '/store-locations',
    'infoFourLabel' => 'FAQs',
    'infoFourUrl'   => '/faqs',
    'infoFiveLabel' => 'Returns Policy',
    'infoFiveUrl'   => '/returns-policy',
    'infoSixLabel'  => 'Terms & Conditions',
    'infoSixUrl'    => '/terms-conditions',
    'contactTitle'  => 'Contact',
    'phoneLabel'    => 'WhatsApp: 8086 3940',
    'phoneUrl'      => 'tel:80863940',
    'emailLabel'    => 'tomnstefanie@gmail.com',
    'emailUrl'      => 'mailto:tomnstefanie@gmail.com',
    'storeLabel'    => 'Find a Store',
    'storeUrl'      => '/store-locations',
    'paymentLabel'  => 'PayNow / Credit Card',
    'paymentUrl'    => '/payment',
    'copyright'     => '© 2026 Tom & Stefanie Corporation · Singapore · All rights reserved',
    'legalMenuId'   => 0,
    'privacyLabel'  => 'Privacy Policy',
    'privacyUrl'    => '/privacy-policy',
    'termsLabel'    => 'Terms',
    'termsUrl'      => '/terms-conditions',
    'sitemapLabel'  => 'Sitemap',
    'sitemapUrl'    => '/sitemap',
]);

$shop_links = [
    ['label' => $attrs['shopOneLabel'], 'url' => $attrs['shopOneUrl']],
    ['label' => $attrs['shopTwoLabel'], 'url' => $attrs['shopTwoUrl']],
    ['label' => $attrs['shopThreeLabel'], 'url' => $attrs['shopThreeUrl']],
    ['label' => $attrs['shopFourLabel'], 'url' => $attrs['shopFourUrl']],
    ['label' => $attrs['shopFiveLabel'], 'url' => $attrs['shopFiveUrl']],
];

$info_links = [
    ['label' => $attrs['infoOneLabel'], 'url' => $attrs['infoOneUrl']],
    ['label' => $attrs['infoTwoLabel'], 'url' => $attrs['infoTwoUrl']],
    ['label' => $attrs['infoThreeLabel'], 'url' => $attrs['infoThreeUrl']],
    ['label' => $attrs['infoFourLabel'], 'url' => $attrs['infoFourUrl']],
    ['label' => $attrs['infoFiveLabel'], 'url' => $attrs['infoFiveUrl']],
    ['label' => $attrs['infoSixLabel'], 'url' => $attrs['infoSixUrl']],
];

$contact_links = [
    ['label' => $attrs['phoneLabel'], 'url' => $attrs['phoneUrl'], 'icon' => 'whatsapp'],
    ['label' => $attrs['emailLabel'], 'url' => $attrs['emailUrl'], 'icon' => 'email'],
    ['label' => $attrs['storeLabel'], 'url' => $attrs['storeUrl'], 'icon' => 'store'],
    ['label' => $attrs['paymentLabel'], 'url' => $attrs['paymentUrl'], 'icon' => 'payment'],
];

$legal_links = [
    ['label' => $attrs['privacyLabel'], 'url' => $attrs['privacyUrl']],
    ['label' => $attrs['termsLabel'], 'url' => $attrs['termsUrl']],
    ['label' => $attrs['sitemapLabel'], 'url' => $attrs['sitemapUrl']],
];

$wrapper_attributes = get_block_wrapper_attributes(['class' => 'site-footer']);
$allowed_object_fits      = ['contain', 'cover', 'fill', 'scale-down'];
$allowed_object_positions = ['left center', 'center center', 'right center', 'center top', 'center bottom'];
$logo_image_width         = min(560, max(120, absint($attrs['logoImageWidth'])));
$logo_image_min_height    = min(150, max(28, absint($attrs['logoImageMinHeight'])));
$logo_image_object_fit    = in_array($attrs['logoImageObjectFit'], $allowed_object_fits, true) ? $attrs['logoImageObjectFit'] : 'contain';
$logo_image_position      = in_array($attrs['logoImageObjectPosition'], $allowed_object_positions, true) ? $attrs['logoImageObjectPosition'] : 'left center';
$logo_style               = sprintf(
    '--site-footer-logo-width:%1$dpx;--site-footer-logo-min-height:%2$dpx;--site-footer-logo-fit:%3$s;--site-footer-logo-position:%4$s;',
    $logo_image_width,
    $logo_image_min_height,
    esc_attr($logo_image_object_fit),
    esc_attr($logo_image_position)
);

$get_menu_links = static function (int $menu_id): array {
    if ($menu_id <= 0) {
        return [];
    }

    $items = wp_get_nav_menu_items($menu_id);

    if (empty($items) || is_wp_error($items)) {
        return [];
    }

    $links = [];

    foreach ($items as $item) {
        if ((int) $item->menu_item_parent !== 0) {
            continue;
        }

        $links[] = [
            'label' => $item->title,
            'url'   => $item->url,
        ];
    }

    return $links;
};

$shop_menu_links  = $get_menu_links(absint($attrs['shopMenuId']));
$info_menu_links  = $get_menu_links(absint($attrs['infoMenuId']));
$legal_menu_links = $get_menu_links(absint($attrs['legalMenuId']));
$shop_links       = $shop_menu_links ?: $shop_links;
$info_links       = $info_menu_links ?: $info_links;
$legal_links      = $legal_menu_links ?: $legal_links;

$render_icon = static function (string $name): string {
    $icons = [
        'whatsapp' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 11.8a6.9 6.9 0 0 1-10.1 6.1L5 19l1.2-3.8A6.9 6.9 0 1 1 19 11.8Z M9.5 8.7c.2 2.7 2 4.5 4.8 5 .4-.4.7-.8.9-1.1-.6-.4-1.1-.7-1.6-1.1l-.8.7c-1-.5-1.7-1.2-2.2-2.2l.7-.8c-.3-.5-.7-1-1.1-1.6-.3.2-.7.5-1.1.9Z"></path></svg>',
        'email'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16v12H4z"></path><path d="m4 7 8 6 8-6"></path></svg>',
        'store'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 10h14l-1-5H6z"></path><path d="M7 10v9h10v-9"></path><path d="M10 19v-5h4v5"></path></svg>',
        'payment'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16v10H4z"></path><path d="M4 10h16"></path></svg>',
    ];

    return $icons[$name] ?? '';
};

$render_links = static function (array $links, string $class_name): void {
    foreach ($links as $link) {
        $label = trim((string) ($link['label'] ?? ''));
        $url   = trim((string) ($link['url'] ?? '#'));

        if ($label === '') {
            continue;
        }

        printf(
            '<a class="%1$s" href="%2$s">%3$s</a>',
            esc_attr($class_name),
            esc_url($url ?: '#'),
            esc_html($label)
        );
    }
};

$render_logo = static function () use ($attrs): string {
    if (!empty($attrs['logoImageUrl'])) {
        return sprintf(
            '<img class="site-footer__logo-image" src="%1$s" alt="%2$s" />',
            esc_url($attrs['logoImageUrl']),
            esc_attr($attrs['logoImageAlt'])
        );
    }

    return sprintf(
        '<span>%1$s</span><span class="site-footer__logo-accent">%2$s</span><span>%3$s</span>',
        esc_html($attrs['logoBefore']),
        esc_html($attrs['logoAccent']),
        esc_html($attrs['logoAfter'])
    );
};

$render_contact_links = static function (array $links) use ($render_icon): void {
    foreach ($links as $link) {
        $label = trim((string) ($link['label'] ?? ''));
        $url   = trim((string) ($link['url'] ?? '#'));
        $icon  = (string) ($link['icon'] ?? '');

        if ($label === '') {
            continue;
        }

        printf(
            '<a class="site-footer__contact-row" href="%1$s"><span class="site-footer__contact-icon">%2$s</span><span>%3$s</span></a>',
            esc_url($url ?: '#'),
            $render_icon($icon), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            esc_html($label)
        );
    }
};
?>

<footer <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <div class="site-footer__inner">
        <div class="site-footer__brand">
            <a class="site-footer__logo" href="<?php echo esc_url($attrs['logoUrl'] ?: '/'); ?>" style="<?php echo esc_attr($logo_style); ?>">
                <?php echo $render_logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </a>

            <div class="site-footer__brand-copy">
                <?php if (trim((string) $attrs['taglineLineOne']) !== '') : ?>
                    <p><?php echo esc_html($attrs['taglineLineOne']); ?></p>
                <?php endif; ?>
                <?php if (trim((string) $attrs['taglineLineTwo']) !== '') : ?>
                    <p><?php echo esc_html($attrs['taglineLineTwo']); ?></p>
                <?php endif; ?>
                <?php if (trim((string) $attrs['email']) !== '') : ?>
                    <p><?php echo esc_html($attrs['email']); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <nav class="site-footer__column" aria-label="<?php esc_attr_e('Footer shop links', 'ai-zippy-child'); ?>">
            <h2 class="site-footer__title"><?php echo esc_html($attrs['shopTitle']); ?></h2>
            <?php $render_links($shop_links, 'site-footer__link'); ?>
        </nav>

        <nav class="site-footer__column" aria-label="<?php esc_attr_e('Footer info links', 'ai-zippy-child'); ?>">
            <h2 class="site-footer__title"><?php echo esc_html($attrs['infoTitle']); ?></h2>
            <?php $render_links($info_links, 'site-footer__link'); ?>
        </nav>

        <div class="site-footer__column site-footer__contact">
            <h2 class="site-footer__title"><?php echo esc_html($attrs['contactTitle']); ?></h2>
            <?php $render_contact_links($contact_links); ?>
        </div>
    </div>

    <div class="site-footer__bottom">
        <?php if (trim((string) $attrs['copyright']) !== '') : ?>
            <p><?php echo esc_html($attrs['copyright']); ?></p>
        <?php endif; ?>

        <nav class="site-footer__legal" aria-label="<?php esc_attr_e('Legal links', 'ai-zippy-child'); ?>">
            <?php $render_links($legal_links, 'site-footer__legal-link'); ?>
        </nav>
    </div>
</footer>
