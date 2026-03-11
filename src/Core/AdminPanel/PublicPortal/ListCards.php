<?php

namespace DVICloudDeploy\Core\AdminPanel\PublicPortal;

use DVICloudDeploy\Core\AdminPanel\Support\StyleDetector;

class ListCards
{
    public static function register()
    {
        add_filter('the_content', [self::class, 'markListWrapper'], 5);
        add_action('wpcd_public_wpcd_app_server_table_after_row_actions_for_title', [self::class, 'serverBadge'], 10, 3);
        add_action('wpcd_public_wpcd_app_table_after_row_actions_for_title', [self::class, 'appBadge'], 10, 3);
        add_action('dvicd_public_wpcd_app_server_table_after_row_actions_for_title', [self::class, 'serverBadge'], 10, 3);
        add_action('dvicd_public_wpcd_app_table_after_row_actions_for_title', [self::class, 'appBadge'], 10, 3);
    }

    public static function markListWrapper($content)
    {
        if (is_admin() || !StyleDetector::isActive()) {
            return $content;
        }

        if (!class_exists('WPCD_WORDPRESS_APP_PUBLIC')) {
            return $content;
        }

        $isList = \WPCD_WORDPRESS_APP_PUBLIC::is_servers_list_page()
            || \WPCD_WORDPRESS_APP_PUBLIC::is_apps_list_page();

        if (!$isList) {
            return $content;
        }

        if (false !== strpos((string) $content, 'dvicd-ap-list-cards')) {
            return $content;
        }

        return '<div class="dvicd-ap-list-cards">' . $content . '</div>';
    }

    public static function serverBadge($item, $columnName = 'title', $primary = '')
    {
        if (!StyleDetector::isActive() || empty($item->ID)) {
            return;
        }

        $state = (string) get_post_meta($item->ID, 'wpcd_server_current_state', true);
        $status = self::mapState($state);
        $label  = $state ? ucwords(str_replace(['-', '_'], ' ', $state)) : __('Unknown', 'wpcd');

        echo self::badgeHtml($label, $status, $item, 'server'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public static function appBadge($item, $columnName = 'title', $primary = '')
    {
        if (!StyleDetector::isActive() || empty($item->ID)) {
            return;
        }

        $app = function_exists('WPCD_WORDPRESS_APP') ? \WPCD_WORDPRESS_APP() : null;
        $ssl = $app && method_exists($app, 'get_site_local_ssl_status')
            ? (bool) $app->get_site_local_ssl_status($item->ID)
            : false;

        $html = self::badgeHtml(
            $ssl ? __('SSL On', 'wpcd') : __('SSL Off', 'wpcd'),
            $ssl ? 'positive' : 'negative',
            $item,
            'site'
        );

        $domain = (string) get_post_meta($item->ID, 'wpapp_domain', true);
        if ($domain) {
            $html .= ' <span class="dvicd-ap-badge dvicd-ap-badge--neutral">' . esc_html($domain) . '</span>';
        }

        echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    private static function mapState(string $state): string
    {
        $state = strtolower($state);
        if (in_array($state, ['active', 'on', 'running'], true)) {
            return 'positive';
        }
        if (in_array($state, ['off', 'stopped', 'error', 'failed'], true)) {
            return 'negative';
        }

        return 'neutral';
    }

    private static function badgeHtml(string $label, string $status, $item, string $context): string
    {
        $html = sprintf(
            '<span class="dvicd-ap-badge dvicd-ap-badge--%1$s">%2$s</span>',
            esc_attr($status),
            esc_html($label)
        );

        /**
         * Filter list card badge markup.
         *
         * @param string $html
         * @param mixed  $item
         * @param string $context
         */
        return (string) apply_filters('dvicd_admin_panel_list_card', $html, $item, $context);
    }
}
