<?php

namespace DVICloudDeploy\Core\CommandLog\Table;

use MetaBox\CustomTable\API;

class DVICDCreateTable {
    /**
     * Create the custom table for error logs.
     */
    public static function run() {
        global $wpdb;

        API::create(
            $wpdb->prefix . 'dvicd_command_logs',
            [
                'command_type' => 'VARCHAR(255)',
                'command_result'  => 'TEXT',
                'command_reference' => 'VARCHAR(255)',
                'parent_post_id' => 'INT(11)',
                'created_at' => 'DATETIME',
            ],
            ['command_type', 'command_result'], // Optional indexes
            true // Add if not exists
        );
    }
}