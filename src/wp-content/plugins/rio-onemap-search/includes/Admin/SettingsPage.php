<?php
namespace Rio_OneMap_Search\Admin;

if (!defined('ABSPATH')) exit;

class SettingsPage
{
    public function register_hooks(): void
    {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_menu(): void
    {
        add_submenu_page(
            'edit.php?post_type=rio_store',
            'OneMap Settings',
            'OneMap Settings',
            'manage_options',
            'rio-onemap-settings',
            [$this, 'render']
        );
    }

    public function register_settings(): void
    {
        register_setting('rio_onemap_settings', 'rio_onemap_api_token', ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('rio_onemap_settings', 'rio_onemap_api_email', ['sanitize_callback' => 'sanitize_email']);
        register_setting('rio_onemap_settings', 'rio_onemap_api_password', ['sanitize_callback' => [$this, 'sanitize_password']]);
        register_setting('rio_onemap_settings', 'rio_onemap_save_addresses', ['sanitize_callback' => 'absint']);
    }

    public function sanitize_password($value): string
    {
        return is_string($value) ? trim(wp_unslash($value)) : '';
    }

    public function render(): void
    {
        ?>
        <div class="wrap">
            <h1>Rio-OneMap Search Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('rio_onemap_settings'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">OneMap API Token</th>
                        <td>
                            <input type="password" name="rio_onemap_api_token" value="<?php echo esc_attr(get_option('rio_onemap_api_token', '')); ?>" class="regular-text" autocomplete="off">
                            <p class="description">Used in Authorization header for OneMap APIs. This is refreshed automatically when email and password are saved below.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">OneMap Account Email</th>
                        <td>
                            <input type="email" name="rio_onemap_api_email" value="<?php echo esc_attr(get_option('rio_onemap_api_email', '')); ?>" class="regular-text" autocomplete="off">
                            <p class="description">Registered OneMap account email used to generate a fresh API token.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">OneMap Account Password</th>
                        <td>
                            <input type="password" name="rio_onemap_api_password" value="<?php echo esc_attr(get_option('rio_onemap_api_password', '')); ?>" class="regular-text" autocomplete="off">
                            <p class="description">Used server-side only to refresh the token when needed.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Save selected user addresses</th>
                        <td>
                            <label>
                                <input type="checkbox" name="rio_onemap_save_addresses" value="1" <?php checked((int) get_option('rio_onemap_save_addresses', 0), 1); ?>>
                                Enable address collection table
                            </label>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
            <p>Shortcode: <code>[rio_onemap_search]</code></p>
        </div>
        <?php
    }
}
