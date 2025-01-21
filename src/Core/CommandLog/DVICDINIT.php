<?php

namespace DVICloudDeploy\Core\CommandLog;

use DVICloudDeploy\Core\CommandLog\Table\DVICDMetaBox;

class DVICDINIT
{
    public static function init()
    {
        // Register the error log model with MetaBox
        add_action('init', [DVICDMetaBox::class, 'registerCommandLogModel']);
        add_action('init', [DVICDMetaBox::class,"removePostType"],20);
        add_filter('mbct_dvicd_command_logs_columns', [DVICDMetaBox::class, 'filterColumns']);
        add_filter('mbct_dvicd_command_logs_column_output', [DVICDMetaBox::class, 'customizeColumnOutput'], 22, 3);
        add_filter('mbct_dvicd_command_logs_row_actions', [DVICDMetaBox::class, 'removeEditAction']);
        add_filter('rwmb_meta_boxes', [DVICDMetaBox::class, 'addMetaBoxes']);
        add_action('admin_menu', [DVICDMetaBox::class, 'add_command_logs_to_menu']);
        add_action('admin_menu', [DVICDMetaBox::class, 'remove_command_logs_to_menu'], 20);
        add_filter('mbct_dvicd_command_logs_admin_render', [DVICDMetaBox::class, 'dvcid_command_logs_view_override'], 10, 3);
        add_filter( "wpcd_main_menu_submenus_order",[DVICDMetaBox::class,"unRegisterMainMenuSubmenusOrder"] );
    }
}