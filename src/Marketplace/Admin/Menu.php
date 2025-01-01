<?php

namespace DVICloudDeploy\Marketplace\Admin;

/**
 * Handles the addition of admin menus and enqueuing assets.
 */
class Menu
{
    /**
     * Register admin menu and scripts.
     */
    public static function register()
    {
        add_action('admin_menu', [self::class, 'addMenu']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueueScripts']);
    }

    /**
     * Add the custom marketplace menu to the admin dashboard.
     */
    public static function addMenu()
    {
        add_submenu_page(
            'edit.php?post_type=wpcd_app_server', // Parent menu slug (change as needed, e.g., 'woocommerce' for WooCommerce menu)
            'Marketplace', // Page title
            'Marketplace', // Menu title
            'manage_options', // Capability
            'dvicd-marketplace', // Menu slug
            [self::class, 'renderPage'] // Callback
        );
    }

    /**
     * Render the marketplace page.
     */
    public static function renderPage()
    {
        require_once DVICD_PATH . '/templates/Admin/Marketplace/marketplace-page.php';
    }

    /**
     * Enqueue scripts and styles for the marketplace page.
     *
     * @param string $hook The current admin page hook.
     */
    public static function enqueueScripts($hook)
    {
        if ('wpcd_app_server_page_dvicd-marketplace' !== $hook) {
            return;
        }

        wp_enqueue_script(
            'dvicd-marketplace-js',
            DVICD_URL . '/assets/marketplace/script.js',
            ['jquery'],
            1.0,
            true
        );

        wp_enqueue_style(
            'dvicd-marketplace-css',
            DVICD_URL . '/assets/marketplace/css/style.css',
            [],
            DVICD_VERSION
        );

        wp_localize_script('dvicd-marketplace-js', 'PluginData', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('DVI_install_plugin_nonce'),
            'nonce_updates'=>wp_create_nonce( 'updates' )
        ]);
    }
}
