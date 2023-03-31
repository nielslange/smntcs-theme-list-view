jQuery( document ).ready( function ( $ ) {
	$( '.activate-theme' ).on( 'click', function ( e ) {
		e.preventDefault();

		var activateUrl = $( this ).data( 'url' );
		var themeRow = $( this ).closest( 'tr' );
		var ajaxUrl = new URL( activateUrl );
		var action = ajaxUrl.searchParams.get( 'action' );
		var stylesheet = ajaxUrl.searchParams.get( 'stylesheet' );
		var nonce = ajaxUrl.searchParams.get( '_wpnonce' );

		$.ajax( {
			url: ajax_object.ajax_url,
			method: 'POST',
			data: {
				action: 'wp_ajax_switch_theme',
				switch_theme_action: action,
				stylesheet: stylesheet,
				_wpnonce: nonce,
			},
			success: function ( response ) {
				$( '.activate-theme' )
					.prop( 'disabled', false )
					.text( 'Activate' );
				themeRow
					.find( '.activate-theme' )
					.prop( 'disabled', true )
					.text( 'Active' );
			},
			error: function ( response ) {
				alert( 'Failed to activate the theme. Please try again.' );
			},
		} );
	} );
} );
