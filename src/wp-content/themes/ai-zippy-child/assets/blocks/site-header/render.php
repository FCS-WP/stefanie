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
    'enableMegaMenu' => true,
    'megaMenuParentLabel' => 'Shop',
    'megaMenuAssignments' => [
        [
            'parentLabel' => 'Shop',
            'megaMenuId'  => 'shop-default',
        ],
    ],
    'megaMenuColumns' => [
        [
            'heading' => 'BY OCCASION',
            'items'   => [
                ['icon' => '⚡', 'label' => 'Last Minute Birthday Gift', 'url' => '/shop-by-occasion/last-minute-birthday-gift', 'badge' => 'URGENT', 'highlight' => false],
                ['icon' => '🎂', 'label' => 'Birthday', 'url' => '/shop-by-occasion/birthday', 'badge' => '', 'highlight' => false],
                ['icon' => '👶', 'label' => 'Baby Shower & 1st Month', 'url' => '/shop-by-occasion/baby-shower', 'badge' => '', 'highlight' => false],
                ['icon' => '🌙', 'label' => 'Full Month Celebration', 'url' => '/shop-by-occasion/full-month', 'badge' => '', 'highlight' => false],
                ['icon' => '🎄', 'label' => 'Festive & Christmas', 'url' => '/shop-by-occasion/festive-christmas', 'badge' => '', 'highlight' => false],
                ['icon' => '🎓', 'label' => 'Graduation', 'url' => '/shop-by-occasion/graduation', 'badge' => '', 'highlight' => false],
            ],
        ],
        [
            'heading' => 'BY AGE',
            'items'   => [
                ['icon' => '🍼', 'label' => '0-12 Months', 'url' => '/shop-by-age/0-12-months', 'badge' => '', 'highlight' => false],
                ['icon' => '🚶', 'label' => '1-3 Years', 'url' => '/shop-by-age/1-3-years', 'badge' => '', 'highlight' => false],
                ['icon' => '🧠', 'label' => '4-6 Years', 'url' => '/shop-by-age/4-6-years', 'badge' => '', 'highlight' => false],
                ['icon' => '⚽', 'label' => '7-10 Years', 'url' => '/shop-by-age/7-10-years', 'badge' => '', 'highlight' => false],
                ['icon' => '🎮', 'label' => '11-14 Years', 'url' => '/shop-by-age/11-14-years', 'badge' => '', 'highlight' => false],
                ['icon' => '☀️', 'label' => '14+', 'url' => '/shop-by-age/14-plus', 'badge' => '', 'highlight' => false],
            ],
        ],
        [
            'heading' => 'COLLECTIONS',
            'items'   => [
                ['icon' => '✨', 'label' => 'New Arrivals', 'url' => '/new-arrivals', 'badge' => '', 'highlight' => false],
                ['icon' => '🔥', 'label' => 'Trending Now', 'url' => '/trending', 'badge' => '', 'highlight' => false],
                ['icon' => '🎁', 'label' => 'Gift Sets & Hampers', 'url' => '/gift-sets-hampers', 'badge' => '', 'highlight' => false],
                ['icon' => '💝', 'label' => 'Curated Hampers', 'url' => '/curated-hampers', 'badge' => '', 'highlight' => true],
                ['icon' => '🔄', 'label' => 'Back in Stock', 'url' => '/back-in-stock', 'badge' => '', 'highlight' => false],
                ['icon' => '💻', 'label' => 'Online Exclusives', 'url' => '/online-exclusives', 'badge' => '', 'highlight' => false],
            ],
        ],
    ],
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
$cart_count = function_exists('WC') && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;

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

$normalize_mega_columns = static function ($columns): array {
    if (!is_array($columns)) {
        return [];
    }

    $normalized = [];

    foreach ($columns as $column) {
        if (!is_array($column)) {
            continue;
        }

        $items = [];

        foreach (($column['items'] ?? []) as $item) {
            if (!is_array($item)) {
                continue;
            }

            $label = trim((string) ($item['label'] ?? ''));

            if ($label === '') {
                continue;
            }

            $items[] = [
                'icon'      => trim((string) ($item['icon'] ?? '')),
                'label'     => $label,
                'url'       => trim((string) ($item['url'] ?? '#')),
                'badge'     => trim((string) ($item['badge'] ?? '')),
                'highlight' => !empty($item['highlight']),
            ];
        }

        $heading = trim((string) ($column['heading'] ?? ''));

        if ($heading === '' && !$items) {
            continue;
        }

        $normalized[] = [
            'heading' => $heading,
            'items'   => $items,
        ];
    }

    return $normalized;
};

$mega_columns = $normalize_mega_columns($attrs['megaMenuColumns']);
$mega_trigger = strtolower(trim((string) $attrs['megaMenuParentLabel']));
$saved_mega_menus = function_exists('ai_zippy_child_get_mega_menus') ? ai_zippy_child_get_mega_menus() : [];
$mega_assignments = is_array($attrs['megaMenuAssignments']) ? $attrs['megaMenuAssignments'] : [];

$find_assigned_mega_columns = static function (string $label) use ($mega_assignments, $saved_mega_menus): array {
    $label_key = strtolower(trim($label));

    if ($label_key === '') {
        return [];
    }

    foreach ($mega_assignments as $assignment) {
        if (!is_array($assignment)) {
            continue;
        }

        $parent_label = strtolower(trim((string) ($assignment['parentLabel'] ?? '')));
        $mega_menu_id = sanitize_key($assignment['megaMenuId'] ?? '');

        if ($parent_label === '' || $mega_menu_id === '' || $parent_label !== $label_key) {
            continue;
        }

        foreach ($saved_mega_menus as $menu) {
            if (($menu['id'] ?? '') === $mega_menu_id) {
                return is_array($menu['columns'] ?? null) ? $menu['columns'] : [];
            }
        }
    }

    return [];
};

$render_mega_menu = static function (array $columns): void {
    if (!$columns) {
        return;
    }
    ?>
    <div class="site-header__mega">
        <div class="site-header__mega-grid">
            <?php foreach ($columns as $column) : ?>
                <div class="site-header__mega-column">
                    <?php if ($column['heading'] !== '') : ?>
                        <div class="site-header__mega-heading"><?php echo esc_html($column['heading']); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($column['items'])) : ?>
                        <div class="site-header__mega-list">
                            <?php foreach ($column['items'] as $item) : ?>
                                <a class="site-header__mega-link<?php echo !empty($item['highlight']) ? ' is-highlighted' : ''; ?>" href="<?php echo esc_url($item['url'] ?: '#'); ?>">
                                    <?php if ($item['icon'] !== '') : ?>
                                        <span class="site-header__mega-icon" aria-hidden="true"><?php echo esc_html($item['icon']); ?></span>
                                    <?php endif; ?>
                                    <span class="site-header__mega-label"><?php echo esc_html($item['label']); ?></span>
                                    <?php if ($item['badge'] !== '') : ?>
                                        <span class="site-header__mega-badge"><?php echo esc_html($item['badge']); ?></span>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
};

$render_nav = static function (array $links, string $class_name) use ($attrs, $mega_columns, $mega_trigger, $find_assigned_mega_columns, $render_mega_menu): void {
    foreach ($links as $link) {
        $label = trim((string) ($link['label'] ?? ''));
        $url   = trim((string) ($link['url'] ?? '#'));

        if ($label === '') {
            continue;
        }

        $assigned_columns = !empty($attrs['enableMegaMenu']) ? $find_assigned_mega_columns($label) : [];
        $active_mega_columns = $assigned_columns;

        if (!$active_mega_columns && !empty($attrs['enableMegaMenu']) && $mega_columns && $mega_trigger !== '' && strtolower($label) === $mega_trigger) {
            $active_mega_columns = $mega_columns;
        }

        $has_mega = !empty($active_mega_columns);
        $item_class = 'site-header__nav-item' . ($has_mega ? ' site-header__nav-item--mega' : '');

        echo '<span class="' . esc_attr($item_class) . '">';
        printf(
            '<a class="%1$s" href="%2$s"%4$s>%3$s</a>',
            esc_attr($class_name),
            esc_url($url ?: '#'),
            esc_html($label),
            $has_mega ? ' aria-haspopup="true"' : ''
        );

        if ($has_mega) {
            $render_mega_menu($active_mega_columns);
        }

        echo '</span>';
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
                    <a class="site-header__icon site-header__cart-link" href="<?php echo esc_url($attrs['cartUrl'] ?: '#'); ?>" aria-label="<?php echo esc_attr(sprintf(_n('Cart, %d item', 'Cart, %d items', $cart_count, 'ai-zippy-child'), $cart_count)); ?>">
                        <?php echo $render_icon('cart'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <span class="site-header__cart-count<?php echo $cart_count > 0 ? '' : ' is-empty'; ?>" data-site-header-cart-count>
                            <?php echo esc_html((string) $cart_count); ?>
                        </span>
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
