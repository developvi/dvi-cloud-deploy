<?php

namespace DVICloudDeploy\Core\AdminPanel\PublicPortal;

use DVICloudDeploy\Core\AdminPanel\Support\StyleDetector;

class PortalShell
{
    public static function register()
    {
        add_filter('the_content', [self::class, 'wrap'], 99);
    }

    public static function wrap($content)
    {
        if (is_admin() || !StyleDetector::isActive()) {
            return $content;
        }

        if (!class_exists('WPCD_WORDPRESS_APP_PUBLIC') || !\WPCD_WORDPRESS_APP_PUBLIC::is_public_page()) {
            return $content;
        }

        if (false !== strpos((string) $content, 'dvicd-ap-portal-shell')) {
            return $content;
        }

        /**
         * Whether to wrap public content with Admin Panel portal shell.
         *
         * @param bool   $wrap
         * @param string $content
         */
        if (!apply_filters('dvicd_admin_panel_use_portal_shell', true, $content)) {
            return $content;
        }

        $user = wp_get_current_user();
        $brand = function_exists('wpcd_get_short_product_name')
            ? wpcd_get_short_product_name()
            : 'DVICloudDeploy';

        $nav = self::navItems();

        /**
         * Filter portal sidebar navigation.
         *
         * @param array $nav
         */
        $nav = apply_filters('dvicd_admin_panel_portal_nav', $nav);

        $view = DVICD_PATH . 'src/Core/AdminPanel/views/portal.php';
        if (!file_exists($view)) {
            return $content;
        }

        ob_start();
        $user_name  = $user ? $user->display_name : '';
        $logout_url = wp_logout_url(home_url('/'));
        include $view;

        return ob_get_clean();
    }

    private static function navItems(): array
    {
        $serversId = class_exists('WPCD_WORDPRESS_APP_PUBLIC')
            ? \WPCD_WORDPRESS_APP_PUBLIC::get_servers_list_page_id()
            : 0;
        $appsId = class_exists('WPCD_WORDPRESS_APP_PUBLIC')
            ? \WPCD_WORDPRESS_APP_PUBLIC::get_apps_list_page_id()
            : 0;
        $deployId = class_exists('WPCD_WORDPRESS_APP_PUBLIC')
            ? \WPCD_WORDPRESS_APP_PUBLIC::get_server_deploy_page_id()
            : 0;

        $current = get_queried_object_id();

        $items = [
            [
                'label'  => __('My Servers', 'wpcd'),
                'url'    => $serversId ? get_permalink($serversId) : '#',
                'icon'   => 'fad fa-server',
                'active' => (int) $current === (int) $serversId,
            ],
            [
                'label'  => __('My Sites', 'wpcd'),
                'url'    => $appsId ? get_permalink($appsId) : '#',
                'icon'   => 'fad fa-globe',
                'active' => (int) $current === (int) $appsId,
            ],
        ];

        if ($deployId) {
            $items[] = [
                'label'  => __('Deploy Server', 'wpcd'),
                'url'    => get_permalink($deployId),
                'icon'   => 'fad fa-plus-circle',
                'active' => (int) $current === (int) $deployId,
            ];
        }

        return $items;
    }
}
