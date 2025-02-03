<?php

namespace DVICloudDeploy\Core\CommandLog\Table;

use MetaBox\CustomTable\API;

class DVICDCreateTable {
    /**
     * Create the custom table for command logs.
     */
    public static function run() {
        global $wpdb;

        API::create(
            $wpdb->prefix . 'dvicd_command_logs',
            [
                'command_type' => 'VARCHAR(255)',
                'command_result'  => 'TEXT',
                'command_reference' => 'VARCHAR(255)',
                'parent_post_id' => 'bigint(20)',
                'created_at' => 'DATETIME',
            ],
            ['command_type', 'parent_post_id'],
            true // Add if not exists
        );
    }
}