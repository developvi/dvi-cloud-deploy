<?php

namespace DVICloudDeploy\Core\AdminPanel\Support;

/**
 * All deprecated Admin Panel filters live here.
 * Prefer dvicd_* hooks in new code.
 */
class Deprecated
{
    private const VERSION = '6.2.1';

    public static function register()
    {
        // Priority 1: hydrate from legacy registrations before other dvicd callbacks.
        add_filter('dvicd_app_wordpress-app_get_tabnames', [self::class, 'appTabNames'], 1, 2);
        add_filter('dvicd_app_wordpress-app_get_tabs', [self::class, 'appTabs'], 1, 2);
        add_filter('dvicd_server_wordpress-app_get_tabnames', [self::class, 'serverTabNames'], 1, 2);
        add_filter('dvicd_server_wordpress-app_get_tabs', [self::class, 'serverTabs'], 1, 2);
        add_filter('dvicd_public_table_views_app_server', [self::class, 'publicServerViews'], 1, 1);
        add_filter('dvicd_public_table_views_app', [self::class, 'publicAppViews'], 1, 1);
        add_filter('dvicd_wordpress-app_settings_fields', [self::class, 'wordpressAppSettingsFields'], 1, 1);

        // Priority 999: optional legacy aliases after new filters finish.
        add_filter('dvicd_admin_panel_tab_groups', [self::class, 'tabGroups'], 999, 2);
        add_filter('dvicd_admin_panel_stats_items', [self::class, 'statsItems'], 999, 3);
        add_filter('dvicd_admin_panel_script_data', [self::class, 'scriptData'], 999, 2);
        add_filter('dvicd_admin_panel_metabox', [self::class, 'metabox'], 999, 2);
        add_filter('dvicd_admin_panel_settings_fields', [self::class, 'settingsFields'], 999, 1);
        add_filter('dvicd_admin_panel_should_enqueue', [self::class, 'shouldEnqueue'], 999, 1);
        add_filter('dvicd_admin_panel_portal_nav', [self::class, 'portalNav'], 999, 1);
        add_filter('dvicd_admin_panel_list_card', [self::class, 'listCard'], 999, 3);
    }

    public static function tabGroups($map, $context = 'site')
    {
        return apply_filters_deprecated(
            'wpcd_cpanel_tab_groups',
            [$map, $context],
            self::VERSION,
            'dvicd_admin_panel_tab_groups'
        );
    }

    public static function appTabNames($tabs, $id = 0)
    {
        return apply_filters_deprecated(
            'wpcd_app_wordpress-app_get_tabnames',
            [$tabs, $id],
            self::VERSION,
            'dvicd_app_wordpress-app_get_tabnames'
        );
    }

    public static function appTabs($fields, $id = 0)
    {
        return apply_filters_deprecated(
            'wpcd_app_wordpress-app_get_tabs',
            [$fields, $id],
            self::VERSION,
            'dvicd_app_wordpress-app_get_tabs'
        );
    }

    public static function serverTabNames($tabs, $id = 0)
    {
        return apply_filters_deprecated(
            'wpcd_server_wordpress-app_get_tabnames',
            [$tabs, $id],
            self::VERSION,
            'dvicd_server_wordpress-app_get_tabnames'
        );
    }

    public static function serverTabs($fields, $id = 0)
    {
        return apply_filters_deprecated(
            'wpcd_server_wordpress-app_get_tabs',
            [$fields, $id],
            self::VERSION,
            'dvicd_server_wordpress-app_get_tabs'
        );
    }

    public static function publicServerViews($views)
    {
        return apply_filters_deprecated(
            'wpcd_public_table_views_wpcd_app_server',
            [$views],
            self::VERSION,
            'dvicd_public_table_views_app_server'
        );
    }

    public static function publicAppViews($views)
    {
        return apply_filters_deprecated(
            'wpcd_public_table_views_wpcd_app',
            [$views],
            self::VERSION,
            'dvicd_public_table_views_app'
        );
    }

    public static function statsItems($items, $postId = 0, $context = 'site')
    {
        return apply_filters_deprecated(
            'wpcd_cpanel_stats_items',
            [$items, $postId, $context],
            self::VERSION,
            'dvicd_admin_panel_stats_items'
        );
    }

    public static function scriptData($data, $context = 'site')
    {
        return apply_filters_deprecated(
            'wpcd_cpanel_script_data',
            [$data, $context],
            self::VERSION,
            'dvicd_admin_panel_script_data'
        );
    }

    public static function metabox($box, $index = 0)
    {
        return apply_filters_deprecated(
            'wpcd_cpanel_metabox',
            [$box, $index],
            self::VERSION,
            'dvicd_admin_panel_metabox'
        );
    }

    public static function settingsFields($fields)
    {
        return apply_filters_deprecated(
            'wpcd_cpanel_settings_fields',
            [$fields],
            self::VERSION,
            'dvicd_admin_panel_settings_fields'
        );
    }

    public static function shouldEnqueue($load)
    {
        return apply_filters_deprecated(
            'wpcd_cpanel_should_enqueue',
            [$load],
            self::VERSION,
            'dvicd_admin_panel_should_enqueue'
        );
    }

    public static function portalNav($nav)
    {
        return apply_filters_deprecated(
            'wpcd_cpanel_portal_nav',
            [$nav],
            self::VERSION,
            'dvicd_admin_panel_portal_nav'
        );
    }

    public static function listCard($html, $item = null, $context = 'site')
    {
        return apply_filters_deprecated(
            'wpcd_cpanel_list_card',
            [$html, $item, $context],
            self::VERSION,
            'dvicd_admin_panel_list_card'
        );
    }

    public static function wordpressAppSettingsFields($fields)
    {
        return apply_filters_deprecated(
            'wpcd_wordpress-app_settings_fields',
            [$fields],
            self::VERSION,
            'dvicd_wordpress-app_settings_fields'
        );
    }
}
