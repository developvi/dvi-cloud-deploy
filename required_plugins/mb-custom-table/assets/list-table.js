jQuery( function( $ ) {
	// Delete an item.
	$( document ).on( 'click', '.row-actions .delete a', function( e ) {
		e.preventDefault();

		if ( ! confirm( MbctListTable.confirm ) ) {
			return;
		}

		const $this = $( this );

		$.post( ajaxurl, {
			action: 'mbct_bulk_actions',
			bulk_action: 'bulk_delete',
			ids: [ parseInt( $this.data( 'id' ), 10 ) ],
			model: $( 'input[name="model"]' ).val(),
			_ajax_nonce: MbctListTable.nonceBulkActions,
		}, response => {
			if ( response.success ) {
				$this.closest( 'tr' ).css( 'background', '#ff8383' ).hide( 'slow', function() {
					$( this ).remove();
				} );
			} else {
				alert( response.data );
			}
		} );
	} );

	// Bulk actions.
	$( '#doaction' ).on( 'click', function( e ) {
		e.preventDefault();

		// Because when the bottom bulk action is clicked, the top one is also clicked.
		// so we only need to process the top one.
		const bulkAction = $( '#bulk-action-selector-top' ).val();
		const $items = $( 'input[name="items[]"]' ).filter( ':checked' );
		if ( $items.length === 0 ) {
			return;
		}

		if ( 'bulk-delete' === bulkAction && ! confirm( MbctListTable.confirm ) ) {
			return;
		}

		// Disable the button while processing.
		$( '#doaction, #doaction2' ).prop( 'disabled', true );

		let ids = [];
		$items.each( function() {
			ids.push( parseInt( $( this ).val(), 10 ) );
		} );

		$.post( ajaxurl, {
			action: 'mbct_bulk_actions',
			bulk_action: bulkAction,
			ids,
			model: $( 'input[name="model"]' ).val(),
			_ajax_nonce: MbctListTable.nonceBulkActions,
		}, response => {
			$( '#doaction, #doaction2' ).prop( 'disabled', false );

			if ( ! response || ! response.success ) {
				alert( response.data );
				return;
			}

			if ( 'bulk-delete' === bulkAction ) {
				$items.closest( 'tr' ).css( 'background', '#ff8383' ).hide( 'slow', function() {
					$( this ).remove();
				} );
				return;
			}

			// If a redirect URL specified
			if ( response?.data?.redirect ) {
				location.href = response.data.redirect;
				return;
			}

			location.reload();
		} );
	} );
} );