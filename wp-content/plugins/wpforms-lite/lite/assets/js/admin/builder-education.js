/* global wpforms_builder_lite, wpforms_builder */
/**
 * WPForms Form Builder Education function.
 *
 * @since 1.5.1
 */

'use strict';

var WPFormsBuilderEducation = window.WPFormsBuilderEducation || ( function( document, window, $ ) {

	/**
	 * Public functions and properties.
	 *
	 * @since 1.5.1
	 *
	 * @type {object}
	 */
	var app = {

		/**
		 * Start the engine.
		 *
		 * @since 1.5.1
		 */
		init: function() {
			$( app.ready );
		},

		/**
		 * Document ready.
		 *
		 * @since 1.5.1
		 */
		ready: function() {
			app.events();
		},

		/**
		 * Register JS events.
		 *
		 * @since 1.5.1
		 */
		events: function() {
			app.clickEvents();
		},

		/**
		 * Registers JS click events.
		 *
		 * @since 1.5.1
		 */
		clickEvents: function() {

			$( document ).on(
				'click',
				'.wpforms-add-fields-button, .wpforms-panel-sidebar-section, .wpforms-builder-settings-block-add, .wpforms-field-option-group-toggle, .wpforms-field-option-row',
				function( event ) {

					var $this = $( this );

					if ( $this.hasClass( 'upgrade-modal' ) ) {

						event.preventDefault();
						event.stopImmediatePropagation();

						app.upgradeModal( {
							feature: $this.hasClass( 'wpforms-add-fields-button' ) ?
								$this.text() + ' ' + wpforms_builder.field :
								$this.data( 'name' ),
							license: $this.data( 'license' ),
							message: $this.data( 'message' ),
						} );
					}
				}
			);

			// "Did You Know?" Click on the dismiss button.
			$( '.wpforms-dyk' ).on( 'click', '.dismiss', function( e ) {

				var $t = $( this ),
					$dyk = $t.closest( '.wpforms-dyk' ),
					data = {
						action: 'wpforms_dyk_dismiss',
						nonce: wpforms_builder.nonce,
						section: $t.attr( 'data-section' ),
					};

				$dyk.find( '.wpforms-dyk-fbox' ).addClass( 'out' );
				setTimeout(
					function() {
						$dyk.remove();
					},
					300
				);

				$.get( wpforms_builder.ajax_url, data );
			} );
		},

		/**
		 * Upgrade modal.
		 *
		 * @since 1.5.1
		 *
		 * @param {object} args Arguments.
		 */
		upgradeModal: function( args ) {

			// Provide a default value.
			if ( typeof args.license === 'undefined' || args.license.length === 0 ) {
				args.license = 'pro';
			}

			// Make sure we received only supported type.
			if ( $.inArray( args.license, [ 'pro', 'elite' ] ) < 0 ) {
				return;
			}

			var message = args.message && args.message.length ?
					args.message :
					wpforms_builder_lite.upgrade[ args.license ].message.replace( /%name%/g, args.feature ),
				upgradeURL = wpforms_builder_lite.upgrade[ args.license ].url + '&utm_content=' + encodeURIComponent( args.feature.trim() );

			$.alert( {
				title   : args.feature + ' ' + wpforms_builder_lite.upgrade[args.license].title,
				icon    : 'fa fa-lock',
				content : message,
				boxWidth: '550px',
				onOpenBefore: function() {

					this.$btnc.after( '<div class="discount-note">' + wpforms_builder_lite.upgrade[args.license].bonus + wpforms_builder_lite.upgrade[args.license].doc + '</div>' );
					this.$body.find( '.jconfirm-content' ).addClass( 'lite-upgrade' );
				},
				buttons : {
					confirm: {
						text    : wpforms_builder_lite.upgrade[args.license].button,
						btnClass: 'btn-confirm',
						keys    : [ 'enter' ],
						action: function() {
							window.open( upgradeURL, '_blank' );
							$.alert( {
								title   : false,
								content : wpforms_builder_lite.upgrade[args.license].modal,
								icon    : 'fa fa-info-circle',
								type    : 'blue',
								boxWidth: '565px',
								buttons : {
									confirm: {
										text    : wpforms_builder.ok,
										btnClass: 'btn-confirm',
										keys    : [ 'enter' ],
									},
								},
							} );
						},
					},
				},
			} );
		},
	};

	// Provide access to public functions/properties.
	return app;

}( document, window, jQuery ) );

// Initialize.
WPFormsBuilderEducation.init();
