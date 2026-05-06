<?php

/**
 * Product category parent editor.
 */

defined('ABSPATH') || exit;

function ai_zippy_child_can_customize_categories(): bool
{
    return current_user_can('manage_product_terms') || current_user_can('manage_woocommerce');
}

function ai_zippy_child_add_customize_categories_admin_page(): void
{
    add_submenu_page(
        'edit.php?post_type=product',
        __('Customize Categories', 'ai-zippy-child'),
        __('Customize Categories', 'ai-zippy-child'),
        'manage_product_terms',
        'customize-categories',
        'ai_zippy_child_render_customize_categories_admin_page'
    );
}
add_action('admin_menu', 'ai_zippy_child_add_customize_categories_admin_page');

function ai_zippy_child_get_product_categories_for_customizer(): array
{
    if (!taxonomy_exists('product_cat')) {
        return [];
    }

    $terms = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ]);

    if (is_wp_error($terms) || empty($terms)) {
        return [];
    }

    return $terms;
}

function ai_zippy_child_category_parent_label(WP_Term $term, array $terms_by_id): string
{
    $labels  = [$term->name];
    $parent  = (int) $term->parent;
    $visited = [$term->term_id => true];

    while ($parent && isset($terms_by_id[$parent]) && empty($visited[$parent])) {
        array_unshift($labels, $terms_by_id[$parent]->name);
        $visited[$parent] = true;
        $parent = (int) $terms_by_id[$parent]->parent;
    }

    return implode(' > ', $labels);
}

function ai_zippy_child_save_customize_categories(array $terms_by_id): void
{
    if (
        !isset($_POST['ai_zippy_child_customize_categories_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['ai_zippy_child_customize_categories_nonce'])),
            'ai_zippy_child_save_customize_categories'
        )
    ) {
        return;
    }

    if (!ai_zippy_child_can_customize_categories()) {
        wp_die(esc_html__('You do not have permission to edit product categories.', 'ai-zippy-child'));
    }

    $parent_map = isset($_POST['category_parent']) && is_array($_POST['category_parent'])
        ? wp_unslash($_POST['category_parent'])
        : [];

    $updated = 0;
    $errors  = [];

    foreach ($parent_map as $term_id => $parent_id) {
        $term_id   = absint($term_id);
        $parent_id = absint($parent_id);

        if (!isset($terms_by_id[$term_id])) {
            continue;
        }

        $term = $terms_by_id[$term_id];

        if ((int) $term->parent === $parent_id) {
            continue;
        }

        if ($parent_id === $term_id) {
            $errors[] = sprintf(
                /* translators: %s: category name */
                __('Skipped "%s" because a category cannot be its own parent.', 'ai-zippy-child'),
                $term->name
            );
            continue;
        }

        if ($parent_id && !isset($terms_by_id[$parent_id])) {
            $errors[] = sprintf(
                /* translators: %s: category name */
                __('Skipped "%s" because the selected parent category no longer exists.', 'ai-zippy-child'),
                $term->name
            );
            continue;
        }

        $children = get_term_children($term_id, 'product_cat');

        if (!is_wp_error($children) && in_array($parent_id, array_map('absint', $children), true)) {
            $errors[] = sprintf(
                /* translators: %s: category name */
                __('Skipped "%s" because a category cannot be moved under its own child.', 'ai-zippy-child'),
                $term->name
            );
            continue;
        }

        $result = wp_update_term($term_id, 'product_cat', [
            'parent' => $parent_id,
        ]);

        if (is_wp_error($result)) {
            $errors[] = sprintf(
                /* translators: 1: category name, 2: error message */
                __('Could not update "%1$s": %2$s', 'ai-zippy-child'),
                $term->name,
                $result->get_error_message()
            );
            continue;
        }

        $updated++;
    }

    if ($updated > 0) {
        printf(
            '<div class="notice notice-success is-dismissible"><p>%s</p></div>',
            esc_html(sprintf(
                /* translators: %d: number of updated categories */
                _n('%d category updated.', '%d categories updated.', $updated, 'ai-zippy-child'),
                $updated
            ))
        );
    }

    foreach ($errors as $error) {
        printf(
            '<div class="notice notice-error is-dismissible"><p>%s</p></div>',
            esc_html($error)
        );
    }
}

function ai_zippy_child_render_customize_categories_admin_page(): void
{
    if (!ai_zippy_child_can_customize_categories()) {
        wp_die(esc_html__('You do not have permission to edit product categories.', 'ai-zippy-child'));
    }

    $terms       = ai_zippy_child_get_product_categories_for_customizer();
    $terms_by_id = [];

    foreach ($terms as $term) {
        $terms_by_id[(int) $term->term_id] = $term;
    }

    ai_zippy_child_save_customize_categories($terms_by_id);

    $terms       = ai_zippy_child_get_product_categories_for_customizer();
    $terms_by_id = [];

    foreach ($terms as $term) {
        $terms_by_id[(int) $term->term_id] = $term;
    }

    ?>
    <div class="wrap ai-zippy-customize-categories">
        <h1><?php esc_html_e('Customize Categories', 'ai-zippy-child'); ?></h1>
        <p><?php esc_html_e('Choose the parent category for each WooCommerce product category.', 'ai-zippy-child'); ?></p>

        <?php if (empty($terms)) : ?>
            <div class="notice notice-info">
                <p><?php esc_html_e('No product categories found.', 'ai-zippy-child'); ?></p>
            </div>
        <?php else : ?>
            <form method="post">
                <?php wp_nonce_field('ai_zippy_child_save_customize_categories', 'ai_zippy_child_customize_categories_nonce'); ?>

                <p>
                    <label class="screen-reader-text" for="ai-zippy-category-search">
                        <?php esc_html_e('Search categories', 'ai-zippy-child'); ?>
                    </label>
                    <input
                        id="ai-zippy-category-search"
                        class="regular-text"
                        type="search"
                        placeholder="<?php esc_attr_e('Search categories...', 'ai-zippy-child'); ?>"
                        data-category-search
                    />
                </p>

                <table class="widefat striped ai-zippy-category-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Category', 'ai-zippy-child'); ?></th>
                            <th><?php esc_html_e('Slug', 'ai-zippy-child'); ?></th>
                            <th><?php esc_html_e('Category URL', 'ai-zippy-child'); ?></th>
                            <th><?php esc_html_e('Products', 'ai-zippy-child'); ?></th>
                            <th><?php esc_html_e('Parent Category', 'ai-zippy-child'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($terms as $term) : ?>
                            <?php
                            $term_id      = (int) $term->term_id;
                            $children     = get_term_children($term_id, 'product_cat');
                            $children     = is_wp_error($children) ? [] : array_map('absint', $children);
                            $term_link    = get_term_link($term);
                            $term_url     = is_wp_error($term_link) ? '' : $term_link;
                            $search_value = strtolower($term->name . ' ' . $term->slug);
                            ?>
                            <tr data-category-row data-category-search-value="<?php echo esc_attr($search_value); ?>">
                                <td>
                                    <strong><?php echo esc_html(ai_zippy_child_category_parent_label($term, $terms_by_id)); ?></strong>
                                </td>
                                <td><code><?php echo esc_html($term->slug); ?></code></td>
                                <td>
                                    <?php if ($term_url) : ?>
                                        <a href="<?php echo esc_url($term_url); ?>" target="_blank" rel="noopener noreferrer">
                                            <?php echo esc_html($term_url); ?>
                                        </a>
                                    <?php else : ?>
                                        &mdash;
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html((string) $term->count); ?></td>
                                <td>
                                    <select name="category_parent[<?php echo esc_attr((string) $term_id); ?>]">
                                        <option value="0"><?php esc_html_e('No parent', 'ai-zippy-child'); ?></option>
                                        <?php foreach ($terms as $parent_term) : ?>
                                            <?php
                                            $parent_id = (int) $parent_term->term_id;

                                            if ($parent_id === $term_id || in_array($parent_id, $children, true)) {
                                                continue;
                                            }
                                            ?>
                                            <option value="<?php echo esc_attr((string) $parent_id); ?>" <?php selected((int) $term->parent, $parent_id); ?>>
                                                <?php echo esc_html(ai_zippy_child_category_parent_label($parent_term, $terms_by_id)); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php submit_button(__('Save Category Parents', 'ai-zippy-child')); ?>
            </form>
        <?php endif; ?>
    </div>

    <style>
        .ai-zippy-customize-categories .ai-zippy-category-table th:first-child {
            width: 34%;
        }

        .ai-zippy-customize-categories .ai-zippy-category-table select {
            max-width: 520px;
            width: 100%;
        }

        .ai-zippy-customize-categories .ai-zippy-category-table code {
            white-space: nowrap;
        }
    </style>

    <script>
        (() => {
            const search = document.querySelector("[data-category-search]");
            const rows = Array.from(document.querySelectorAll("[data-category-row]"));

            if (!search || rows.length === 0) {
                return;
            }

            search.addEventListener("input", () => {
                const query = search.value.trim().toLowerCase();

                rows.forEach((row) => {
                    const value = row.getAttribute("data-category-search-value") || "";
                    row.hidden = query !== "" && !value.includes(query);
                });
            });
        })();
    </script>
    <?php
}
