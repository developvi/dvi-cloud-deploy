<?php

namespace DVICloudDeploy\Core\AdminPanel\Service;

/**
 * First-class extension points for Admin Panel.
 * Deprecated aliases are registered in Support\Deprecated.
 */
class HookBridge
{
    public static function register()
    {
        /**
         * Fires when Admin Panel hooks are ready.
         * Add-ons can register filters/actions here.
         */
        do_action('dvicd_admin_panel_hooks_registered');

        // Documented extension filters (no-op pass-through defaults).
        add_filter('dvicd_admin_panel_feature_cards', [self::class, 'identity'], 10, 2);
        add_filter('dvicd_admin_panel_groups', [self::class, 'identity'], 10, 2);
        add_filter('dvicd_admin_panel_portal_content', [self::class, 'identity'], 10, 1);
        add_action('dvicd_admin_panel_before_grid', [self::class, 'noop']);
        add_action('dvicd_admin_panel_after_grid', [self::class, 'noop']);
        add_action('dvicd_admin_panel_before_stats', [self::class, 'noop']);
        add_action('dvicd_admin_panel_after_stats', [self::class, 'noop']);
    }

    public static function identity($value, $context = null)
    {
        return $value;
    }

    public static function noop()
    {
    }
}
