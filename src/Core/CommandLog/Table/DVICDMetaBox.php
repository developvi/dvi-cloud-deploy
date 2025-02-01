<?php

namespace DVICloudDeploy\Core\CommandLog\Table;

use \wpdb;

/**
 * Class DVICDMetaBox
 */
class DVICDMetaBox
{
    /**
     * Register the Command log model with MetaBox
     */
    public static function registerCommandLogModel()
    {
        global $wpdb;

        mb_register_model('dvicd_command_logs', [
            'table'  => $wpdb->prefix . 'dvicd_command_logs',
            'labels' => [
                'name'          => 'Command Logs',
                'singular_name' => 'dvicd_command_logs',
                "add_new_item"  => null,
                'add_new'       => null,
                "edit_item"     => null,
            ],
            'menu_icon' => 'dashicons-money-alt',
            'show_ui'             => true,
            'menu_position'       => null,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => false,
            'public'              => true,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'hierarchical'        => false,
            'supports'            => array(''),
            'rewrite'             => null,
            'capability_type'     => 'post',
            'capabilities'        => array(
                'create_posts' => false,
                'read_posts'   => 'wpcd_manage_logs',
                'edit_posts'   => 'wpcd_manage_logs',
            ),
            'map_meta_cap'        => true,

        ]);
    }
    /**
     * Filter to override the view path for the dvicd_command_logs model.
     *
     * @param string $default_view_path The default view path.
     * @param string $view The view name.
     * @param object $model The model object.
     * @return string The overridden view path if custom exists, otherwise the default view path.
     */
    public static function dvcid_command_logs_view_override($default_view_path, $view, $model)
    {
        global $wpdb;
        // Path to custom view
        if ($view == 'edit') {
            $custom_view_path = plugin_dir_path(__FILE__) . "src/Core/CommandLog/views/command_log.php";
            // Check if custom view file exists, use it if so
            if (file_exists($custom_view_path)) {
                return $custom_view_path;
            }
        }
        if ($view == 'add') {
            wp_die("You don't have permission to access this page.");
        }

        // Otherwise, return the default view path
        return $default_view_path;
    }
    // add function remove_command_logs_to_menu

    public static function remove_command_logs_to_menu()
    {
        /**
         * Removes the 'model-dvicd_command_logs' menu page from the WordPress admin menu.
         */
        remove_menu_page('model-dvicd_command_logs');
    }

    public static function unRegisterMainMenuSubmenusOrder($submenu)
    {
        if (isset($submenu['edit.php?post_type=wpcd_command_log'])) {
            unset($submenu['edit.php?post_type=wpcd_command_log']);
        }
        return $submenu;
    }
    public static function removePostType()
    {

        unregister_post_type('wpcd_command_log');
    }


    public static function add_command_logs_to_menu()
    {

        // Add a submenu item under the specified parent menu
        add_submenu_page(
            'edit.php?post_type=wpcd_app_server', // Parent menu slug
            __('Command Logs', 'dvicd'),    // Page title
            __('Command Logs', 'dvicd'),    // Menu title
            'manage_options',             // Required capability
            'model-dvicd_command_logs',     // Slug of the registered model
            null                          // Leave the callback null as mb_register_model will handle the display
        );
    }

    /**
     * Filter the columns of the command logs
     */
    public static function filterColumns($columns)
    {
        unset($columns['id']);
        return $columns;
    }

    /**
     * Customize the column output for Command logs
     */
    public static function customizeColumnOutput($output, $column_name, $item)
    {
        if ($column_name == 'command_result') {
            $output = mb_strimwidth($output, 0, 150, '...');
            $output = "<a href='edit.php?post_type=wpcd_app_server&page=model-dvicd_command_logs&model-action=edit&model-id={$item['ID']}'>{$output}</a>";
        }
        return $output;
    }

    /**
     * Remove the edit action in the Command logs table
     */
    public static function removeEditAction($actions)
    {
        unset($actions['edit']);
        return $actions;
    }

    /**
     * Add custom fields to the MetaBox meta boxes
     */
    public static function addMetaBoxes($meta_boxes)
    {
        global $wpdb;
        $meta_boxes[] = [
            'title'        => __('Command Log Details', 'textdomain'),
            'storage_type' => 'custom_table',
            'table'        => $wpdb->prefix . 'dvicd_command_logs',
            'models'       => ['dvicd_command_logs'],
            'fields'       => [
                [
                    'id'   => 'command_result',
                    'name' => __('Command Result', 'textdomain'),
                    'admin_columns' => true,
                    'type' => 'textarea',
                    'desc' => __('Detailed Command message', 'textdomain'),
                ],
                [
                    'id'   => 'command_type',
                    'name' => __('Command Type', 'textdomain'),
                    'type' => 'text',
                    'admin_columns' => true,
                    'desc' => __('Type of the Command (e.g., Warning, Critical)', 'textdomain'),
                ],
                [
                    'id'   => 'command_reference',
                    'name' => __('Command reference', 'textdomain'),
                    'admin_columns' => true,
                    'type' => 'text',
                    'desc' => __('File where the Command occurred', 'textdomain'),
                ],
                [
                    'id'   => 'parent_post_id',
                    'name' => __('Parent Post Id', 'textdomain'),
                    'admin_columns' => true,
                    'type' => 'number',
                    'desc' => __('Line number of the Command', 'textdomain'),
                ],
                
                [
                    'id'   => 'created_at',
                    'name' => __('Created At', 'textdomain'),
                    'type' => 'datetime',
                    'desc' => __('Time when the Command was logged', 'textdomain'),
                    'admin_columns' => [
                        'sort' => true,
                    ],
                    'js_options' => [
                        'dateFormat' => 'yy-mm-dd',
                        'timeFormat' => 'HH:mm:ss',
                    ],
                ],
            ],
        ];

        return $meta_boxes;
    }
}