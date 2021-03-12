( function ( $ ) {
	var switch_to_new_ui = function () {
		$( 'a.switch-to-new-ui, .cartflows-use-new-ui-save-btn' ).on(
			'click',
			function ( e ) {
				e.preventDefault();

				let href = $( this ).attr( 'href' ),
					params = new URLSearchParams( href ),
					nonce = params.get( 'wcf_switch_ui' );

				var data = {
					action: 'wcf_switch_to_new_ui',
					button_action: 'update',
					security: nonce,
				};

				$( this ).text( 'Switching UI...' );

				$.ajax( {
					type: 'POST',
					url: ajaxurl,
					data: data,

					success: function ( response ) {
						if ( response.success ) {
							window.location.href =
								response.data[ 'redirect_to' ];
						}
					},
				} );
			}
		);
	};

	$( document ).ready( function ( $ ) {
		switch_to_new_ui();
	} );
} )( jQuery );
