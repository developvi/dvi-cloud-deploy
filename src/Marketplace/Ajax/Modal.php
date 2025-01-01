<?php
namespace DVICloudDeploy\Marketplace\Ajax;

use DVICloudDeploy\Marketplace\Handler\FetchingRemotePluginsDate;

/**
 * Handles AJAX requests for fetching plugin data.
 */
class Modal
{
    /**
     * Register the modal-related AJAX actions.
     */
    public static function register()
    {
        add_action('wp_ajax_dvi_modal_action', [self::class, 'fetchPlugins']);
    }

    /**
     * Fetch plugin data for the modal.
     */
    public static function fetchPlugins()
    {
        check_ajax_referer('DVI_install_plugin_nonce', 'nonce');

        $plugins = FetchingRemotePluginsDate::getData();

        if (!empty($plugins)) {
            echo json_encode(['pluginsData' => $plugins]);
            die;
        }

        wp_send_json_error('No plugins available.');
    }
}
