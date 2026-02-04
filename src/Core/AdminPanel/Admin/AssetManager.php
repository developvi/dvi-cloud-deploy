<?php

namespace DVICloudDeploy\Core\AdminPanel\Admin;

use DVICloudDeploy\Core\AdminPanel\Repository\TabGroupRepository;
use DVICloudDeploy\Core\AdminPanel\Support\StyleDetector;

class AssetManager
{
    public static function register()
    {
        add_action('admin_enqueue_scripts', [self::class, 'enqueue'], 20);
        add_action('wp_enqueue_scripts', [self::class, 'enqueue'], 20);
    }

    public static function enqueue()
    {
        $load = is_admin()
            ? StyleDetector::shouldLoadForScreen()
            : StyleDetector::shouldLoadPublic();

        if (!$load) {
            return;
        }

        /**
         * Whether Admin Panel assets should load.
         *
         * @param bool $load
         */
        if (!apply_filters('dvicd_admin_panel_should_enqueue', true)) {
            return;
        }

        $ver = defined('DVICD_VERSION') ? DVICD_VERSION : '1.0';
        $url = defined('DVICD_URL') ? DVICD_URL : '';

        if (!wp_script_is('wpcd-fontawesome-pro', 'registered')) {
            wp_register_script(
                'wpcd-fontawesome-pro',
                'https://kit.fontawesome.com/4fa00a8874.js',
                [],
                6.2,
                true
            );
        }
        wp_script_add_data('wpcd-fontawesome-pro', 'crossorigin', 'anonymous');
        wp_enqueue_script('wpcd-fontawesome-pro');

        // Webfont fallback so dynamically injected card icons still render (fas/fab).
        wp_enqueue_style(
            'dvicd-admin-panel-fa',
            'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css',
            [],
            '6.5.2'
        );

        wp_enqueue_style(
            'dvicd-admin-panel-theme',
            $url . 'assets/admin-panel/css/theme.css',
            ['dvicd-admin-panel-fa'],
            $ver
        );

        wp_enqueue_style(
            'dvicd-admin-panel-portal',
            $url . 'assets/admin-panel/css/portal.css',
            ['dvicd-admin-panel-theme'],
            $ver
        );

        wp_enqueue_script(
            'dvicd-admin-panel-ui',
            $url . 'assets/admin-panel/js/ui.js',
            ['jquery', 'wpcd-fontawesome-pro'],
            $ver,
            true
        );

        $repo     = new TabGroupRepository();
        $context  = self::resolveContext();
        $i10n     = [
            'backLabel'    => __('Back to Home', 'wpcd'),
            'searchLabel'  => __('Search features…', 'wpcd'),
            'searchEmpty'  => __('No matching features', 'wpcd'),
            'openLabel'    => __('Open', 'wpcd'),
            'hashPrefix'   => 'ap',
            'context'      => $context,
            'tabGroups'    => $repo->get($context),
            'isRtl'        => is_rtl(),
        ];

        /**
         * Filter localized script data for Admin Panel UI.
         *
         * @param array  $i10n
         * @param string $context
         */
        $i10n = apply_filters('dvicd_admin_panel_script_data', $i10n, $context);

        wp_localize_script('dvicd-admin-panel-ui', 'DvicdAdminPanel', $i10n);

        do_action('dvicd_admin_panel_assets_enqueued', $context);
    }

    private static function resolveContext(): string
    {
        if (is_admin()) {
            $screen = function_exists('get_current_screen') ? get_current_screen() : null;
            if ($screen && 'wpcd_app_server' === $screen->post_type) {
                return 'server';
            }
            return 'site';
        }

        if (class_exists('WPCD_WORDPRESS_APP_PUBLIC') && \WPCD_WORDPRESS_APP_PUBLIC::is_server_edit_page()) {
            return 'server';
        }

        return 'site';
    }
}
