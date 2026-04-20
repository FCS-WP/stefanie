<?php
/**
 * Mega menu admin and REST helpers.
 *
 * @package AI_Zippy_Child
 */

defined('ABSPATH') || exit;

const AI_ZIPPY_CHILD_MEGA_MENUS_OPTION = 'ai_zippy_child_mega_menus';

function ai_zippy_child_default_mega_menu_columns(): array
{
    return [
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
    ];
}

function ai_zippy_child_sanitize_mega_menu_columns($columns): array
{
    if (!is_array($columns)) {
        return [];
    }

    $clean = [];

    foreach ($columns as $column) {
        if (!is_array($column)) {
            continue;
        }

        $items = [];

        foreach (($column['items'] ?? []) as $item) {
            if (!is_array($item)) {
                continue;
            }

            $label = sanitize_text_field($item['label'] ?? '');

            if ($label === '') {
                continue;
            }

            $items[] = [
                'icon'      => sanitize_text_field($item['icon'] ?? ''),
                'label'     => $label,
                'url'       => esc_url_raw($item['url'] ?? '#'),
                'badge'     => sanitize_text_field($item['badge'] ?? ''),
                'highlight' => !empty($item['highlight']),
            ];
        }

        $heading = sanitize_text_field($column['heading'] ?? '');

        if ($heading === '' && !$items) {
            continue;
        }

        $clean[] = [
            'heading' => $heading,
            'items'   => $items,
        ];
    }

    return $clean;
}

function ai_zippy_child_sanitize_mega_menus($menus): array
{
    if (!is_array($menus)) {
        return [];
    }

    $clean = [];

    foreach ($menus as $index => $menu) {
        if (!is_array($menu)) {
            continue;
        }

        $title   = sanitize_text_field($menu['title'] ?? '');
        $columns = ai_zippy_child_sanitize_mega_menu_columns($menu['columns'] ?? []);

        if ($title === '' && !$columns) {
            continue;
        }

        $id = sanitize_key($menu['id'] ?? '');

        if ($id === '') {
            $id = 'mega-' . ((int) $index + 1) . '-' . wp_generate_password(6, false, false);
        }

        $clean[] = [
            'id'      => $id,
            'title'   => $title ?: __('Mega Menu', 'ai-zippy-child'),
            'columns' => $columns,
        ];
    }

    return $clean;
}

function ai_zippy_child_get_mega_menus(): array
{
    $menus = get_option(AI_ZIPPY_CHILD_MEGA_MENUS_OPTION, null);

    if (!is_array($menus)) {
        $menus = [
            [
                'id'      => 'shop-default',
                'title'   => 'Shop Mega Menu',
                'columns' => ai_zippy_child_default_mega_menu_columns(),
            ],
        ];
    }

    return ai_zippy_child_sanitize_mega_menus($menus);
}

function ai_zippy_child_find_mega_menu(string $id): ?array
{
    foreach (ai_zippy_child_get_mega_menus() as $menu) {
        if ($menu['id'] === $id) {
            return $menu;
        }
    }

    return null;
}

function ai_zippy_child_add_mega_menus_admin_page(): void
{
    add_theme_page(
        __('Mega Menus', 'ai-zippy-child'),
        __('Mega Menus', 'ai-zippy-child'),
        'edit_theme_options',
        'ai-zippy-child-mega-menus',
        'ai_zippy_child_render_mega_menus_admin_page'
    );
}
add_action('admin_menu', 'ai_zippy_child_add_mega_menus_admin_page');

function ai_zippy_child_render_mega_menus_admin_page(): void
{
    if (!current_user_can('edit_theme_options')) {
        wp_die(esc_html__('You do not have permission to edit mega menus.', 'ai-zippy-child'));
    }

    if (isset($_POST['ai_zippy_child_mega_menus_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['ai_zippy_child_mega_menus_nonce'])), 'ai_zippy_child_save_mega_menus')) {
        $raw_json = isset($_POST['ai_zippy_child_mega_menus_json']) ? wp_unslash($_POST['ai_zippy_child_mega_menus_json']) : '[]';
        $decoded  = json_decode((string) $raw_json, true);
        update_option(AI_ZIPPY_CHILD_MEGA_MENUS_OPTION, ai_zippy_child_sanitize_mega_menus($decoded), false);
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Mega menus saved.', 'ai-zippy-child') . '</p></div>';
    }

    $menus = ai_zippy_child_get_mega_menus();
    ?>
    <div class="wrap ai-zippy-mega-menus">
        <h1><?php esc_html_e('Mega Menus', 'ai-zippy-child'); ?></h1>
        <p><?php esc_html_e('Create reusable mega menus here, then assign them inside the Site Header block settings.', 'ai-zippy-child'); ?></p>

        <form method="post" data-mega-menu-form>
            <?php wp_nonce_field('ai_zippy_child_save_mega_menus', 'ai_zippy_child_mega_menus_nonce'); ?>
            <input type="hidden" name="ai_zippy_child_mega_menus_json" data-mega-menu-json value="<?php echo esc_attr(wp_json_encode($menus)); ?>" />
            <div data-mega-menu-app></div>
            <?php submit_button(__('Save Mega Menus', 'ai-zippy-child')); ?>
        </form>
    </div>

    <style>
        .ai-zippy-mega-menus [data-menu-card],
        .ai-zippy-mega-menus [data-column-card],
        .ai-zippy-mega-menus [data-item-card] {
            background: #fff;
            border: 1px solid #dcdcde;
            border-radius: 8px;
            margin: 16px 0;
            padding: 16px;
        }
        .ai-zippy-mega-menus [data-menu-head],
        .ai-zippy-mega-menus [data-column-head],
        .ai-zippy-mega-menus [data-item-grid] {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr)) auto;
        }
        .ai-zippy-mega-menus [data-column-list] {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }
        .ai-zippy-mega-menus label {
            display: grid;
            font-weight: 600;
            gap: 6px;
        }
        .ai-zippy-mega-menus input[type="text"],
        .ai-zippy-mega-menus input[type="url"] {
            width: 100%;
        }
        .ai-zippy-mega-menus .button-link-delete {
            color: #b32d2e;
        }
    </style>

    <script>
        (() => {
            const app = document.querySelector("[data-mega-menu-app]");
            const form = document.querySelector("[data-mega-menu-form]");
            const jsonField = document.querySelector("[data-mega-menu-json]");

            if (!app || !form || !jsonField) {
                return;
            }

            const defaultItem = { icon: "✨", label: "Menu item", url: "#", badge: "", highlight: false };
            const createId = () => `mega-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;
            let menus = [];

            try {
                menus = JSON.parse(jsonField.value || "[]");
            } catch (error) {
                menus = [];
            }

            const sync = () => {
                jsonField.value = JSON.stringify(menus);
            };

            const field = (label, value, onInput, type = "text") => {
                const wrapper = document.createElement("label");
                const text = document.createElement("span");
                const input = document.createElement("input");
                text.textContent = label;
                input.type = type;
                input.value = value || "";
                input.addEventListener("input", () => {
                    onInput(input.value);
                    sync();
                });
                wrapper.append(text, input);
                return wrapper;
            };

            const button = (label, onClick, variant = "secondary") => {
                const control = document.createElement("button");
                control.type = "button";
                control.className = variant === "delete" ? "button button-link-delete" : "button";
                control.textContent = label;
                control.addEventListener("click", onClick);
                return control;
            };

            const render = () => {
                app.replaceChildren();

                menus.forEach((menu, menuIndex) => {
                    const menuCard = document.createElement("section");
                    menuCard.dataset.menuCard = "";

                    const menuHead = document.createElement("div");
                    menuHead.dataset.menuHead = "";
                    menuHead.append(
                        field("Menu Name", menu.title, (value) => menu.title = value),
                        field("Menu ID", menu.id, (value) => menu.id = value),
                        button("Delete Menu", () => {
                            menus.splice(menuIndex, 1);
                            sync();
                            render();
                        }, "delete")
                    );

                    const columnList = document.createElement("div");
                    columnList.dataset.columnList = "";

                    (menu.columns || []).forEach((column, columnIndex) => {
                        const columnCard = document.createElement("div");
                        columnCard.dataset.columnCard = "";

                        const columnHead = document.createElement("div");
                        columnHead.dataset.columnHead = "";
                        columnHead.append(
                            field("Column Heading", column.heading, (value) => column.heading = value),
                            document.createElement("span"),
                            button("Delete Column", () => {
                                menu.columns.splice(columnIndex, 1);
                                sync();
                                render();
                            }, "delete")
                        );

                        (column.items || []).forEach((item, itemIndex) => {
                            const itemCard = document.createElement("div");
                            itemCard.dataset.itemCard = "";

                            const itemGrid = document.createElement("div");
                            itemGrid.dataset.itemGrid = "";
                            itemGrid.append(
                                field("Icon", item.icon, (value) => item.icon = value),
                                field("Label", item.label, (value) => item.label = value),
                                button("Delete Item", () => {
                                    column.items.splice(itemIndex, 1);
                                    sync();
                                    render();
                                }, "delete"),
                                field("URL", item.url, (value) => item.url = value, "url"),
                                field("Badge", item.badge, (value) => item.badge = value)
                            );

                            const highlightLabel = document.createElement("label");
                            const checkbox = document.createElement("input");
                            checkbox.type = "checkbox";
                            checkbox.checked = Boolean(item.highlight);
                            checkbox.addEventListener("change", () => {
                                item.highlight = checkbox.checked;
                                sync();
                            });
                            highlightLabel.append(checkbox, " Highlight item");

                            itemCard.append(itemGrid, highlightLabel);
                            columnCard.append(itemCard);
                        });

                        columnCard.prepend(columnHead);
                        columnCard.append(button("Add Item", () => {
                            column.items = [...(column.items || []), { ...defaultItem }];
                            sync();
                            render();
                        }));
                        columnList.append(columnCard);
                    });

                    menuCard.append(menuHead, columnList, button("Add Column", () => {
                        menu.columns = [...(menu.columns || []), { heading: "COLUMN TITLE", items: [{ ...defaultItem }] }];
                        sync();
                        render();
                    }));
                    app.append(menuCard);
                });

                app.append(button("Add Mega Menu", () => {
                    menus.push({ id: createId(), title: "New Mega Menu", columns: [{ heading: "COLUMN TITLE", items: [{ ...defaultItem }] }] });
                    sync();
                    render();
                }));

                sync();
            };

            form.addEventListener("submit", sync);
            render();
        })();
    </script>
    <?php
}

function ai_zippy_child_register_mega_menu_rest_routes(): void
{
    register_rest_route('ai-zippy-child/v1', '/mega-menus', [
        'methods'             => 'GET',
        'callback'            => static fn() => ai_zippy_child_get_mega_menus(),
        'permission_callback' => static fn() => current_user_can('edit_theme_options'),
    ]);
}
add_action('rest_api_init', 'ai_zippy_child_register_mega_menu_rest_routes');
