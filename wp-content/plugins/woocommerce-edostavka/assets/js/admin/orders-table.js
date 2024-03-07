;(function( $ ) {

	if ( typeof wc_edostavka_orders_params === 'undefined' ) {
		return false;
	}

	const CallCourierOrder = function () {

		this.onCallAction = this.onCallAction.bind( this )
		this.onResponse = this.onResponse.bind( this )
		this.onModalLoaded = this.onModalLoaded.bind( this )

		$( document.body )
			.on( 'click', 'input[name="call-courier-action"]', this.onCallAction )
			.on( 'click', '#create-call-courier-order:not(.disabled)', this.onResponse )
			.on( 'wc_backbone_modal_loaded', this.onModalLoaded )
	}

	CallCourierOrder.prototype.onModalLoaded = function ( event, target ) {

		if( 'wc-edostavka-modal-call-courier' === target ) {

			$( event.target ).trigger( 'wc-enhanced-select-init' );

			$( event.target )
				.find( '.wc-edostavka-call-courier-content' )
				.find( 'form' )
				.on( 'input validate change', this.validateField );
		}
	}

	CallCourierOrder.prototype.onCallAction = function (event) {

		event.preventDefault();

		$( this ).WCBackboneModal( {
			template: 'wc-edostavka-modal-call-courier'
		} );
	}

	CallCourierOrder.prototype.onResponse = function (event) {
		const $button = $( event.target ),
			$parent = $button.closest( 'section' ),
			$form = $parent.find( 'form' ),
			data = this.getFormData( $form );

		$.ajax( {
			type:	'POST',
			url:	wc_edostavka_orders_params.ajax_url,
			beforeSend: function() {

				$parent
					.find( 'article' )
					.find( 'div.woocommerce-message' )
					.remove();

				$parent.block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});

				$button.addClass( 'disabled' );
			},
			data:	{
				...data,
				action: 'edostavka_create_courier_call',
				security: wc_edostavka_orders_params.orders_nonce
			},
			success: function ( response ) {
				if ( response.success ) {

					if ( -1 === response.data.redirect.indexOf( 'https://' ) || -1 === response.data.redirect.indexOf( 'http://' ) ) {
						window.location = response.data.redirect;
					} else {
						window.location = decodeURI( response.data.redirect );
					}

				} else {
					$form.find( 'select, input' ).trigger( 'validate' ).trigger( 'blur' );
					if( response.data && typeof response.data == 'string' ) {
						$parent
							.find( 'article' )
							.append( $( '<div />', {
								class: 'error woocommerce-message',
								html: '<p>' + response.data + '</p>'
							} ) );
					}
				}
			}

		} ).always( function () {
			$parent.unblock();
			$button.removeClass( 'disabled' );
		} );
	}

	CallCourierOrder.prototype.getFormData = function (form) {
		var data = {};

		$.each( form.serializeArray(), function( index, item ) {
			if ( item.name.indexOf( '[]' ) !== -1 ) {
				item.name = item.name.replace( '[]', '' );
				data[ item.name ] = $.makeArray( data[ item.name ] );
				data[ item.name ].push( item.value );
			} else {
				data[ item.name ] = item.value;
			}
		} );

		return data;
	}

	CallCourierOrder.prototype.validateField = function ( event ) {
		var $this             = $( event.target ),
			$parent           = $this.closest( '.form-row' ),
			validated         = true,
			validate_required = $parent.is( '.validate-required' ),
			event_type        = event.type;

		if ( 'input' === event_type ) {
			$parent.removeClass( 'woocommerce-invalid woocommerce-validated' );
		}

		if ( 'validate' === event_type || 'change' === event_type ) {

			if ( validate_required ) {

				if ( 'select' === $this.attr( 'type' ) && ! $this.find( 'option' ).is( ':selected' ) ) {
					$parent.removeClass( 'woocommerce-validated' ).addClass( 'woocommerce-invalid' );
					validated = false;
				} else if ( $this.val() === '' ) {
					$parent.removeClass( 'woocommerce-validated' ).addClass( 'woocommerce-invalid' );
					validated = false;
				}
			}

			if ( validated ) {
				$parent.removeClass( 'woocommerce-invalid' ).addClass( 'woocommerce-validated' );
			}
		}
	}

	new CallCourierOrder();

	$( document.body ).on( 'click', '.order-preview:not(.disabled)', function() {

		var $previewButton    = $( this ),
			$order_id         = $previewButton.data( 'orderId' );

		if ( $previewButton.data( 'order-data' ) ) {

			$( this ).WCBackboneModal( {
				template: 'wc-edostavka-modal-view-order',
				variable : $previewButton.data( 'orderData' )
			} );

		} else {

			$.ajax( {
				url:     wc_edostavka_orders_params.ajax_url,
				data:    {
					order_id: $order_id,
					action  : 'edostavka_get_order_details',
					security: wc_edostavka_orders_params.preview_nonce
				},
				type:    'GET',
				beforeSend: function() {
					$previewButton.addClass( 'disabled' );
				},
				success: function( response ) {

					if ( response.success ) {

						$previewButton.data( 'orderData', response.data );

						$( this ).WCBackboneModal( {
							template: 'wc-edostavka-modal-view-order',
							variable : response.data
						} );
					}
				}

			} ).always( function () {
				$( '.order-preview' ).removeClass( 'disabled' );
			} );
		}

		return false;
	} );

} )( jQuery );
