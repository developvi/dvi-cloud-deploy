<?php
namespace DVICloudDeploy\Marketplace\Ajax;

/**
 * Handles AJAX actions for installing, updating, and activating plugins.
 */
class PluginActions
{
    /**
     * Register AJAX actions.
     */
    public static function register()
    {
        add_action('wp_ajax_dvi_install_plugin', [self::class, 'installPlugin']);
        add_action('wp_ajax_activate_plugin', [self::class, 'activatePlugin']);
    }

    /**
     * Install a plugin from a GitHub download URL.
     */
    public static function installPlugin()
    {
        check_ajax_referer('DVI_install_plugin_nonce', 'nonce');

        $download_url = $_POST['download_url'] ?? '';

        if (empty($download_url)) {
            wp_send_json_error('Invalid download URL.');
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        WP_Filesystem();
        $skin     = new \WP_Ajax_Upgrader_Skin();

        $upgrader = new \Plugin_Upgrader($skin);
        $result = $upgrader->install($download_url);

        if ($result === true) {
            wp_send_json_success();
        }

        wp_send_json_error('Installation failed.');
    }



    /**
     * Activate or deactivate a plugin.
     */
    public static function activatePlugin()
    {
        check_ajax_referer('DVI_install_plugin_nonce', 'nonce');

        $plugin_info = sanitize_text_field($_POST['plugin_info']);
        $status = $_POST['plugin_active'];

        if ($status === 'inactive') {
            $result = activate_plugin($plugin_info);
            if (is_wp_error($result)) {
                wp_send_json_error($result->get_error_message());
            }
        } else {
            deactivate_plugins($plugin_info);
        }

        wp_send_json_success();
    }
}
