<?php

namespace DVICloudDeploy\Core\AdminPanel\PublicPortal;

use DVICloudDeploy\Core\AdminPanel\Service\StatsCollector;
use DVICloudDeploy\Core\AdminPanel\Support\StyleDetector;

class StatsSidebar
{
    public static function register()
    {
        add_filter('the_content', [self::class, 'inject'], 40);
        add_filter('dvicd_admin_panel_overview_fields', [self::class, 'embedInOverview'], 10, 3);
        add_action('edit_form_after_title', [self::class, 'renderAdminOpen'], 5);
        add_action('edit_form_after_editor', [self::class, 'renderAdminClose'], 99);
    }

    public static function inject($content)
    {
        if (is_admin() || !StyleDetector::isActive()) {
            return $content;
        }

        if (!class_exists('WPCD_WORDPRESS_APP_PUBLIC')) {
            return $content;
        }

        $context = null;
        if (\WPCD_WORDPRESS_APP_PUBLIC::is_app_edit_page() && StyleDetector::isSiteStyle()) {
            $context = 'site';
        } elseif (\WPCD_WORDPRESS_APP_PUBLIC::is_server_edit_page() && StyleDetector::isServerStyle()) {
            $context = 'server';
        }

        if (!$context) {
            return $content;
        }

        $postId = get_the_ID();
        if (!$postId) {
            return $content;
        }

        $sidebar = self::renderHtml((int) $postId, $context, false);
        if ('' === $sidebar) {
            return $content;
        }

        return '<div class="dvicd-ap-layout"><div class="dvicd-ap-main">' . $content . '</div>' . $sidebar . '</div>';
    }

    /**
     * Merge summary rows into Site/Server Overview metabox (admin).
     *
     * @param array  $fields
     * @param int    $postId
     * @param string $context site|server
     */
    public static function embedInOverview(array $fields, $postId, string $context): array
    {
        if (!is_admin() || !StyleDetector::isActive()) {
            return $fields;
        }

        if ('site' === $context && !StyleDetector::isSiteStyle()) {
            return $fields;
        }
        if ('server' === $context && !StyleDetector::isServerStyle()) {
            return $fields;
        }

        // Drop the first IP row — summary already shows IP.
        $fields = array_values(array_filter($fields, static function ($field) {
            $class = isset($field['class']) ? (string) $field['class'] : '';
            return ! str_contains( $class, '_top_row_ip' );
        }));

        $html = self::renderHtml((int) $postId, $context, true);
        if ('' === $html) {
            return $fields;
        }

        $fields[] = [
            'type'  => 'custom_html',
            'std'   => $html,
            'class' => 'dvicd-ap-overview-summary',
        ];

        return $fields;
    }

    public static function renderAdminOpen($post)
    {
        // No-op: unclosed wrappers broke admin DOM.
    }

    public static function renderAdminClose($post)
    {
        // Kept for compatibility.
    }

    private static function renderHtml(int $postId, string $context, bool $embedded = false): string
    {
        $collector = new StatsCollector();
        $items     = $collector->collect($postId, $context);

        if (empty($items)) {
            return '';
        }

        $title = 'server' === $context
            ? __('Server Summary', 'wpcd')
            : __('Site Summary', 'wpcd');

        /**
         * Filter stats sidebar title.
         *
         * @param string $title
         * @param string $context
         */
        $title = apply_filters('dvicd_admin_panel_stats_title', $title, $context);

        $view = DVICD_PATH . 'src/Core/AdminPanel/views/stats-sidebar.php';
        if (!file_exists($view)) {
            return '';
        }

        ob_start();
        include $view;

        return (string) ob_get_clean();
    }
}
