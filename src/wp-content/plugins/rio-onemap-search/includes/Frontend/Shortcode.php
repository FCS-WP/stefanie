<?php
namespace Rio_OneMap_Search\Frontend;

if (!defined('ABSPATH')) exit;

class Shortcode
{
    public function register_hooks(): void
    {
        add_shortcode('rio_onemap_search', [$this, 'render']);
        add_action('wp_enqueue_scripts', [$this, 'register_assets']);
    }

    public function register_assets(): void
    {
        wp_register_style('rio-onemap-search', RIO_ONEMAP_SEARCH_URL . 'assets/css/store-locator.css', [], RIO_ONEMAP_SEARCH_VERSION);
        wp_register_script('rio-onemap-search', RIO_ONEMAP_SEARCH_URL . 'assets/js/store-locator.js', ['jquery'], RIO_ONEMAP_SEARCH_VERSION, true);

        wp_localize_script('rio-onemap-search', 'RioOneMapSearch', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('rio_onemap_search_nonce'),
        ]);
    }

    public function render(array $atts = []): string
    {
        $atts = shortcode_atts([
            'title' => 'Your Search Result',
            'search_title' => 'Search nearest store by Singapore postal code',
        ], $atts, 'rio_onemap_search');
        $title = sanitize_text_field($atts['title']);
        $search_title = sanitize_text_field($atts['search_title']);

        wp_enqueue_style('rio-onemap-search');
        wp_enqueue_script('rio-onemap-search');

        ob_start();
        ?>
        <div class="rio-onemap-search" data-rio-onemap-search>
            <div class="rio-onemap-search__form">
                <label for="rio-onemap-postal-code"><?php echo esc_html($search_title); ?></label>
                <div class="rio-onemap-search__row">
                    <input id="rio-onemap-postal-code" type="text" inputmode="numeric" maxlength="6" placeholder="Example: 569933" data-rio-postal-code>
                    <button type="button" data-rio-search-btn>Search</button>
                </div>
                <div class="rio-onemap-search__message" data-rio-message></div>
            </div>
            <div class="rio-onemap-search__popup" data-rio-results-popup aria-hidden="true">
                <div class="rio-onemap-search__popup-panel" role="dialog" aria-modal="true" aria-label="Nearest store result">
                    <button type="button" class="rio-onemap-search__popup-close" data-rio-close-results aria-label="Close results">&times;</button>
                    <div class="rio-onemap-search__popup-header">
                        <span>Nearest Store</span>
                        <h3><?php echo esc_html($title); ?></h3>
                    </div>
                    <div class="rio-onemap-search__results" data-rio-results></div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
