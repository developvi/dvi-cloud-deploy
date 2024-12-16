<?php
namespace MetaBox\CustomTable\Utils;

class Helpers {

	public static function is_edit_screen(): bool {
		$page = (string) rwmb_request()->get( 'page' );

		if ( strpos( $page, 'model-' ) !== 0 ) {
			return false;
		}

		$action = rwmb_request()->get( 'model-action' );

		return in_array( $action, [ 'add', 'edit' ] );
	}
}
