<?php

namespace DVICloudDeploy\Core\AdminPanel\Admin;

use DVICloudDeploy\Core\AdminPanel\Support\StyleDetector;

class SettingsIntegration
{
    public static function register()
    {
        add_filter('wpcd_wordpress-app_settings_fields', [self::class, 'addStyleOption'], 30);
        add_filter('dvicd_wordpress-app_settings_fields', [self::class, 'addStyleOption'], 30);
    }

    public static function addStyleOption(array $fields): array
    {
        foreach ($fields as $i => $field) {
            if (empty($field['id']) || empty($field['options']) || !is_array($field['options'])) {
                continue;
            }

            if (!in_array($field['id'], ['wordpress_app_tab_style', 'wordpress_app_server_tab_style'], true)) {
                continue;
            }

            if (!isset($field['options'][StyleDetector::STYLE])) {
                $fields[$i]['options'][StyleDetector::STYLE] = __('Admin Panel', 'wpcd');
            }
        }

        /**
         * Allow plugins to alter Admin Panel settings field injection.
         *
         * @param array $fields
         */
        return apply_filters('dvicd_admin_panel_settings_fields', $fields);
    }
}
