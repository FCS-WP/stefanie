<?php
/**
 * Site Header block render.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

$attrs = wp_parse_args($attributes ?? [], [
    'promoText'     => 'Free delivery above $60 · Spend $80, get $12 off · New in: Micro Scooters just dropped · Shop Now ->',
    'promoUrl'      => '/shop',
    'logoBefore'    => 'Tom',
    'logoAccent'    => '&',
    'logoAfter'     => 'Stefanie',
    'logoUrl'       => '/',
    'logoImageId'   => 0,
    'logoImageUrl'  => '',
    'logoImageAlt'  => '',
    'logoImageWidth' => 320,
    'logoImageMinHeight' => 54,
    'logoImageObjectFit' => 'contain',
    'logoImageObjectPosition' => 'center center',
    'menuId'        => 0,
    'homeLabel'     => 'Home',
    'homeUrl'       => '/',
    'shopLabel'     => 'Shop',
    'shopUrl'       => '/shop',
    'giftLabel'     => 'Gift',
    'giftUrl'       => '/gift',
    'trendingLabel' => 'Trending',
    'trendingUrl'   => '/trending',
    'contactLabel'  => 'Contact Us',
    'contactUrl'    => '/contact-us',
    'searchUrl'     => '/?s=',
    'wishlistUrl'   => '/wishlist',
    'cartUrl'       => '/cart',
    'buttonText'    => 'Shop Now',
    'buttonUrl'     => '/shop',
    'showPromo'     => true,
    'showSearch'    => true,
    'showWishlist'  => true,
    'showCart'      => true,
]);

$fallback_links = [
    [
        'label' => $attrs['homeLabel'],
        'url'   => $attrs['homeUrl'],
    ],
    [
        'label' => $attrs['shopLabel'],
        'url'   => $attrs['shopUrl'],
    ],
    [
        'label' => $attrs['giftLabel'],
        'url'   => $attrs['giftUrl'],
    ],
    [
        'label' => $attrs['trendingLabel'],
        'url'   => $attrs['trendingUrl'],
    ],
    [
        'label' => $attrs['contactLabel'],
        'url'   => $attrs['contactUrl'],
    ],
];

$nav_links = [];
$menu_id   = absint($attrs['menuId']);

if ($menu_id > 0) {
    $menu_items = wp_get_nav_menu_items($menu_id);

    if (!empty($menu_items) && !is_wp_error($menu_items)) {
        foreach ($menu_items as $menu_item) {
            if ((int) $menu_item->menu_item_parent !== 0) {
                continue;
            }

            $nav_links[] = [
                'label' => $menu_item->title,
                'url'   => $menu_item->url,
            ];
        }
    }
}

if (!$nav_links) {
    $nav_links = $fallback_links;
}

$wrapper_attributes = get_block_wrapper_attributes(['class' => 'site-header']);
$allowed_object_fits      = ['contain', 'cover', 'fill', 'scale-down'];
$allowed_object_positions = ['center center', 'left center', 'right center', 'center top', 'center bottom'];
$logo_image_width         = min(520, max(80, absint($attrs['logoImageWidth'])));
$logo_image_min_height    = min(140, max(24, absint($attrs['logoImageMinHeight'])));
$logo_image_object_fit    = in_array($attrs['logoImageObjectFit'], $allowed_object_fits, true) ? $attrs['logoImageObjectFit'] : 'contain';
$logo_image_position      = in_array($attrs['logoImageObjectPosition'], $allowed_object_positions, true) ? $attrs['logoImageObjectPosition'] : 'center center';
$logo_style               = sprintf(
    '--site-header-logo-width:%1$dpx;--site-header-logo-min-height:%2$dpx;--site-header-logo-fit:%3$s;--site-header-logo-position:%4$s;',
    $logo_image_width,
    $logo_image_min_height,
    esc_attr($logo_image_object_fit),
    esc_attr($logo_image_position)
);

$render_icon = static function (string $name): string {
    $icons = [
        'search' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"></circle><path d="m16 16 5 5"></path></svg>',
        'heart'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.8 4.6a5.2 5.2 0 0 0-7.4 0L12 6l-1.4-1.4a5.2 5.2 0 1 0-7.4 7.4L12 20.8l8.8-8.8a5.2 5.2 0 0 0 0-7.4Z"></path></svg>',
        'cart'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h2l2.2 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.6L20 8H6"></path><circle cx="9.5" cy="20" r="1"></circle><circle cx="17" cy="20" r="1"></circle></svg>',
        'menu'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"></path></svg>',
        'close'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"></path></svg>',
    ];

    return $icons[$name] ?? '';
};

$render_logo = static function () use ($attrs): string {
    if (!empty($attrs['logoImageUrl'])) {
        return sprintf(
            '<img class="site-header__logo-image" src="%1$s" alt="%2$s" />',
            esc_url($attrs['logoImageUrl']),
            esc_attr($attrs['logoImageAlt'])
        );
    }

    return sprintf(
        '<span>%1$s</span><span class="site-header__logo-accent">%2$s</span><span>%3$s</span>',
        esc_html($attrs['logoBefore']),
        esc_html($attrs['logoAccent']),
        esc_html($attrs['logoAfter'])
    );
};

$render_nav = static function (array $links, string $class_name): void {
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
?>

<header <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <?php if (!empty($attrs['showPromo']) && trim((string) $attrs['promoText']) !== '') : ?>
        <div class="site-header__promo">
            <a class="site-header__promo-link" href="<?php echo esc_url($attrs['promoUrl'] ?: '#'); ?>">
                <?php echo wp_kses_post($attrs['promoText']); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="site-header__main">
        <div class="site-header__inner">
            <a class="site-header__logo" href="<?php echo esc_url($attrs['logoUrl'] ?: '/'); ?>" aria-label="<?php esc_attr_e('Home', 'ai-zippy-child'); ?>" style="<?php echo esc_attr($logo_style); ?>">
                <?php echo $render_logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </a>

            <nav class="site-header__nav" aria-label="<?php esc_attr_e('Primary navigation', 'ai-zippy-child'); ?>">
                <?php $render_nav($nav_links, 'site-header__nav-link'); ?>
            </nav>

            <div class="site-header__actions">
                <?php if (!empty($attrs['showSearch'])) : ?>
                    <a class="site-header__icon" href="<?php echo esc_url($attrs['searchUrl'] ?: '/?s='); ?>" aria-label="<?php esc_attr_e('Search', 'ai-zippy-child'); ?>">
                        <?php echo $render_icon('search'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </a>
                <?php endif; ?>

                <?php if (!empty($attrs['showWishlist'])) : ?>
                    <a class="site-header__icon" href="<?php echo esc_url($attrs['wishlistUrl'] ?: '#'); ?>" aria-label="<?php esc_attr_e('Wishlist', 'ai-zippy-child'); ?>">
                        <?php echo $render_icon('heart'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </a>
                <?php endif; ?>

                <?php if (!empty($attrs['showCart'])) : ?>
                    <a class="site-header__icon" href="<?php echo esc_url($attrs['cartUrl'] ?: '#'); ?>" aria-label="<?php esc_attr_e('Cart', 'ai-zippy-child'); ?>">
                        <?php echo $render_icon('cart'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </a>
                <?php endif; ?>

                <?php if (trim((string) $attrs['buttonText']) !== '') : ?>
                    <a class="site-header__cta" href="<?php echo esc_url($attrs['buttonUrl'] ?: '#'); ?>">
                        <?php echo esc_html($attrs['buttonText']); ?>
                    </a>
                <?php endif; ?>

                <button class="site-header__menu-toggle" type="button" aria-label="<?php esc_attr_e('Open menu', 'ai-zippy-child'); ?>" aria-expanded="false" data-site-header-toggle>
                    <?php echo $render_icon('menu'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </button>
            </div>
        </div>
    </div>

    <div class="site-header__drawer" aria-hidden="true">
        <div class="site-header__drawer-panel">
            <div class="site-header__drawer-head">
                <a class="site-header__logo site-header__logo--drawer" href="<?php echo esc_url($attrs['logoUrl'] ?: '/'); ?>" style="<?php echo esc_attr($logo_style); ?>">
                    <?php echo $render_logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </a>
                <button class="site-header__drawer-close" type="button" aria-label="<?php esc_attr_e('Close menu', 'ai-zippy-child'); ?>" data-site-header-close>
                    <?php echo $render_icon('close'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </button>
            </div>

            <nav class="site-header__drawer-nav" aria-label="<?php esc_attr_e('Mobile navigation', 'ai-zippy-child'); ?>">
                <?php $render_nav($nav_links, 'site-header__drawer-link'); ?>
            </nav>

            <?php if (trim((string) $attrs['buttonText']) !== '') : ?>
                <a class="site-header__drawer-cta" href="<?php echo esc_url($attrs['buttonUrl'] ?: '#'); ?>">
                    <?php echo esc_html($attrs['buttonText']); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
