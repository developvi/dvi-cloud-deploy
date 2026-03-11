<?php

namespace DVICloudDeploy\Core\AdminPanel\PublicPortal;

use DVICloudDeploy\Core\AdminPanel\Service\StatsCollector;
use DVICloudDeploy\Core\AdminPanel\Support\StyleDetector;

class StatsSidebar
{
    public static function register()
    {
        add_filter('the_content', [self::class, 'inject'], 40);
        add_action('edit_form_after_title', [self::class, 'renderAdminOpen'], 5);
        add_action('edit_form_after_editor', [self::class, 'renderAdminClose'], 99);
        add_action('admin_footer-post.php', [self::class, 'renderAdminFooterFallback']);
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

        $sidebar = self::renderHtml((int) $postId, $context);
        if ('' === $sidebar) {
            return $content;
        }

        return '<div class="dvicd-ap-layout"><div class="dvicd-ap-main">' . $content . '</div>' . $sidebar . '</div>';
    }

    public static function renderAdminOpen($post)
    {
        // No-op: unclosed wrappers broke admin DOM. Footer JS wraps once.
    }

    public static function renderAdminClose($post)
    {
        // Kept for compatibility; footer fallback prints the sidebar.
    }

    public static function renderAdminFooterFallback()
    {
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        if (!$screen || !in_array($screen->post_type, ['wpcd_app', 'wpcd_app_server'], true)) {
            return;
        }

        if (!StyleDetector::isActive()) {
            return;
        }

        global $post;
        if (!$post) {
            return;
        }

        $context = 'wpcd_app_server' === $post->post_type ? 'server' : 'site';
        if ('site' === $context && !StyleDetector::isSiteStyle()) {
            return;
        }
        if ('server' === $context && !StyleDetector::isServerStyle()) {
            return;
        }

        $html = self::renderHtml((int) $post->ID, $context);
        if ('' === $html) {
            return;
        }

        // Mount only — ui.js merges this into Site/Server Overview on the right.
        echo '<div id="dvicd-ap-stats-mount" style="display:none">' . $html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    private static function shouldRenderAdmin($post): bool
    {
        if (!is_admin() || !StyleDetector::isActive() || !$post) {
            return false;
        }

        if ('wpcd_app' === $post->post_type) {
            return StyleDetector::isSiteStyle();
        }

        if ('wpcd_app_server' === $post->post_type) {
            return StyleDetector::isServerStyle();
        }

        return false;
    }

    private static function renderHtml(int $postId, string $context): string
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
