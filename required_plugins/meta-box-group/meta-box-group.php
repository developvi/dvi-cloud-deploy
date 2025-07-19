<?php
/**
 * Plugin Name: MB Group
 * Plugin URI:  https://metabox.io/plugins/meta-box-group/
 * Description: Put fields into groups for better organization
 * Version:     1.4.6
 * Author:      MetaBox.io
 * Author URI:  https://metabox.io
 * License:     GPL2+
 * Text Domain: meta-box-group
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

if ( ! class_exists( 'RWMB_Group' ) ) {
	class RWMB_Group {
		/**
		 * Indicate that the meta box is saved or not.
		 * This variable is used inside group field to show child fields.
		 *
		 * @var bool
		 */
		public static $saved = false;

		public function __construct() {
			// Hook to 'init' with priority 5 to make sure all actions are registered before Meta Box 4.9.0 runs.
			add_action( 'init', [ $this, 'load_files' ], 5 );

			add_action( 'rwmb_before', [ $this, 'set_saved' ] );
			add_action( 'rwmb_after', [ $this, 'unset_saved' ] );
		}

		public function load_files() {
			if ( class_exists( 'RWMB_Field' ) && ! class_exists( 'RWMB_Group_Field' ) ) {
				require_once __DIR__ . '/group-field.php';

				load_plugin_textdomain( 'meta-box-group', false, plugin_basename( __DIR__ ) . '/languages/' );
			}
		}

		/**
		 * Check if current meta box is saved.
		 * This variable is used inside group field to show child fields.
		 *
		 * @param object $obj Meta Box object.
		 */
		public function set_saved( $obj ) {
			self::$saved = $obj->is_saved();
		}

		/**
		 * Unset 'saved' variable, to be ready for next meta box.
		 */
		public function unset_saved() {
			self::$saved = false;
		}
	}

	new RWMB_Group;
}
