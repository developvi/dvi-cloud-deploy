<?php

namespace DVICloudDeploy\Core\AdminPanel\Support;

class StyleDetector
{
    public const STYLE = 'adminpanel';

    public static function isSiteStyle(): bool
    {
        if (!function_exists('wpcd_get_early_option')) {
            return false;
        }

        return self::STYLE === wpcd_get_early_option('wordpress_app_tab_style');
    }

    public static function isServerStyle(): bool
    {
        if (!function_exists('wpcd_get_early_option')) {
            return false;
        }

        return self::STYLE === wpcd_get_early_option('wordpress_app_server_tab_style');
    }

    public static function isActive(): bool
    {
        return self::isSiteStyle() || self::isServerStyle();
    }

    public static function shouldLoadForScreen(): bool
    {
        if (!is_admin()) {
            return self::shouldLoadPublic();
        }

        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        if (!$screen) {
            return false;
        }

        // Settings has its own Admin Panel UI — never load site/server ui.js there.
        if (false !== strpos((string) $screen->id, 'wpcd_settings')) {
            return false;
        }

        if ('wpcd_app' === $screen->post_type) {
            return self::isSiteStyle();
        }

        if ('wpcd_app_server' === $screen->post_type) {
            return self::isServerStyle();
        }

        return false;
    }

    public static function shouldLoadPublic(): bool
    {
        if (is_admin() || !self::isActive()) {
            return false;
        }

        if (!class_exists('WPCD_WORDPRESS_APP_PUBLIC') || !\WPCD_WORDPRESS_APP_PUBLIC::is_public_page()) {
            return false;
        }

        if (\WPCD_WORDPRESS_APP_PUBLIC::is_app_edit_page()) {
            return self::isSiteStyle();
        }

        if (\WPCD_WORDPRESS_APP_PUBLIC::is_server_edit_page()) {
            return self::isServerStyle();
        }

        // List / deploy pages when either style is Admin Panel.
        return self::isActive();
    }
}
