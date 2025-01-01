<?php
namespace MetaBox\CustomTable\Model;

use MetaBox\CustomTable\Utils\Resolver;

class Ajax {
	public function __construct() {
		add_action( 'wp_ajax_mbct_bulk_actions', [ $this, 'bulk_actions' ] );
	}

	public function bulk_actions() {
		check_ajax_referer( 'bulk-actions' );

		$request = rwmb_request();
		$action  = $request->post( 'bulk_action' );
		$action  = sanitize_text_field( $action );
		$action  = str_replace( '-', '_', $action );

		$resolver = new Resolver();

		$ids        = array_map( 'absint', $request->post( 'ids' ) );
		$model_name = sanitize_text_field( $request->post( 'model' ) );
		$model      = Factory::get( $model_name );

		if ( ! $ids || ! $model_name || ! $model ) {
			wp_send_json_error( __( 'Invalid request', 'mb-custom-table' ) );
		}

		// We use Resolver to bind the variables to the callback function
		// This allows us using any of the variables in the callback function regardless of the order
		$resolver->bind( compact( [
			'request',
			'ids',
			'model',
		] ) );

		// Allows user to define their own bulk action
		if ( function_exists( "mbct_{$action}_bulk_action" ) ) {
			return $resolver->resolve( "mbct_{$action}_bulk_action" );
		}

		if ( method_exists( $this, "{$action}_bulk_action" ) ) {
			return $resolver->resolve( [ $this, "{$action}_bulk_action" ] );
		}

		wp_send_json_error( 'Invalid action', 'mb-custom-table' );
	}

	public function bulk_delete_bulk_action( $ids, $model ) {
		global $wpdb;

		$ids_string = implode( ',', $ids );

		foreach ( $ids as $id ) {
			do_action( 'mbct_before_delete', $id, $model->table );
		}

		$rows_affected = $wpdb->query(
			$wpdb->prepare( "DELETE FROM %i WHERE id IN ($ids_string)",
				$model->table
			)
		);

		if ( ! $rows_affected ) {
			wp_send_json_error( __( 'No rows deleted', 'mb-custom-table' ) );
		}

		// Only fire the action if rows were actually deleted
		foreach ( $ids as $id ) {
			do_action( 'mbct_before_delete', $id, $model->table );
		}

		wp_send_json_success();
	}
}
