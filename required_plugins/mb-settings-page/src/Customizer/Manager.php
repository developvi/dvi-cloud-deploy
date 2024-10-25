<?php
namespace MBSP\Customizer;

use MBSP\Factory;

class Manager {
	public function __construct() {
		// Priority 30 ensures it fires after meta boxes are registered.
		add_action( 'init', [ $this, 'init' ], 30 );
		add_filter( 'rwmb_meta_boxes', [ $this, 'customizer_change_input_file' ] );
	}

	public function customizer_change_input_file( $meta_boxes ) {
		foreach ( $meta_boxes as $k => $meta_box ) {

			// Meta box that has a settings page.
			if ( isset( $meta_box['settings_pages'] ) && Factory::get( $meta_box['settings_pages'], 'customizer' ) ) {
				array_walk( $meta_box['fields'], [ $this, 'change_input_file' ] );
				$meta_boxes[ $k ] = $meta_box;
				continue;
			}

			// Meta box that doesn't have a settings page.
			if ( isset( $meta_box['panel'] ) && count( $meta_box['fields'] ) > 0 ) {
				array_walk( $meta_box['fields'], [ $this, 'change_input_file' ] );
				$meta_boxes[ $k ] = $meta_box;
			}
		}

		return $meta_boxes;
	}

	private function change_input_file( &$field ) {
		$field['type'] = ( $field['type'] === 'file' || $field['type'] === 'image' ) ? $field['type'] . '_advanced' : $field['type'];

		if ( 'group' === $field['type'] && count( $field['fields'] ) > 0 ) {
			array_walk( $field['fields'], [ $this, 'change_input_file' ] );
		}
	}

	public function init() {
		$meta_boxes = rwmb_get_registry( 'meta_box' )->all();

		// Meta box that has a settings page.
		$settings_sections = array_filter( $meta_boxes, function( $meta_box ) {
			return $meta_box->settings_pages && Factory::get( $meta_box->settings_pages, 'customizer' );
		} );
		array_walk( $settings_sections, [ $this, 'register_settings_section' ] );

		// Meta box that doesn't have a settings page.
		$normal_sections = array_filter( $meta_boxes, function( $meta_box ) {
			return isset( $meta_box->meta_box['panel'] );
		} );
		array_walk( $normal_sections, [ $this, 'register_normal_section' ] );
	}

	private function register_settings_section( $meta_box ) {
		$panels   = Factory::get( $meta_box->settings_pages, 'customizer' );
		$meta_box = new SettingsSection( $meta_box->meta_box );
		$meta_box->register_fields();
		foreach ( $panels as $panel ) {
			$meta_box->object_id = $panel->option_name;
			new Setting( $meta_box );
		}
	}

	private function register_normal_section( $meta_box ) {
		new Setting( $meta_box );
	}
}
