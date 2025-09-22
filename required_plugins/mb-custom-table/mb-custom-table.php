<?php
/**
 * Plugin Name: MB Custom Table
 * Plugin URI:  https://metabox.io/plugins/mb-custom-table/
 * Description: Save custom fields data to custom table instead of the default meta tables.
 * Version:     2.2.4
 * Author:      MetaBox.io
 * Author URI:  https://metabox.io
 * License:     GPL2+
 * Text Domain: mb-custom-table
 * Domain Path: /languages/
 *
 * Copyright (C) 2010-2025 Tran Ngoc Tuan Anh. All rights reserved.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

// Prevent loading this file directly.
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

if ( ! function_exists( 'mb_custom_table_load' ) ) {
	if ( file_exists( __DIR__ . '/vendor' ) ) {
		require __DIR__ . '/vendor/autoload.php';
	}

	/**
	 * Hook to 'init' with priority 5 to make sure all actions are registered before Meta Box 4.9.0 runs
	 */
	add_action( 'init', 'mb_custom_table_load', 5 );

	/**
	 * Load plugin files after Meta Box is loaded
	 */
	function mb_custom_table_load(): void {
		if ( ! defined( 'RWMB_VER' ) ) {
			return;
		}

		define( 'MBCT_DIR', __DIR__ );
		list( , $url ) = RWMB_Loader::get_path( __DIR__ );
		define( 'MBCT_URL', $url );

		new MetaBox\CustomTable\Loader();
		new MetaBox\CustomTable\Model\Ajax();

		load_plugin_textdomain( 'mb-custom-table', false, plugin_basename( MBCT_DIR ) . '/languages/' );
	}
}
