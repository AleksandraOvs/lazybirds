( function( $, params ) {

	const wc_edostavka_city_select = {
		init: function() {
			this.loadCityfield 			= this.loadCityfield.bind( this );
			this.billingCountry 		= this.billingCountry.bind( this );
			this.loadCityfieldForCart 	= this.loadCityfieldForCart.bind( this );
			this.refreshAddressField 	= this.refreshAddressField.bind( this );
			this.setCustomerLocation 	= this.setCustomerLocation.bind( this );
			this.isBlocked				= this.isBlocked.bind( this );
			this.blockNode				= this.blockNode.bind( this );
			this.unBlock				= this.unBlock.bind( this );

			$( document.body ).on( 'updated_checkout', this.loadCityfield );
			//$( document.body ).on( 'country_to_state_changed', this.loadCityfield );
			$( document.body ).on( 'change', '.country_to_state', this.loadCityfieldForCart );
			$( document.body ).on( 'updated_checkout_city', this.refreshAddressField );

			if( !!params.is_edit_address ) {
				this.loadCityfield();
			}
		},
		loadCityfield: function () {

			const self 		= this;
			const fields 	= self.cityFieldElements.join( ', ' );

			if( params.enable_dropdown_city == 'enable' || ( params.enable_dropdown_city == 'zone' && $.inArray( this.billingCountry(), params.allowed_zone ) !== -1 ) ) {

				$( fields ).off( 'select2:select' );

				$( fields ).WCEdostavkaSelectCity( {
					placeholder: params.i18n_strings.enter_city_name,
					defaultValue: params.default_city,
					countryCode: this.billingCountry(),
					allowTags: params.enable_custom_city,
					minimumInputLength: 0,
					ajaxUrl: params.ajax_url,
					customerLocation: params.customer_location
				} );

				$( fields ).on( 'select2:select', function( event ) {

					const stateFields = self.stateFieldElements.join( ', ' );
					const data = event.params.data;

					if( $( stateFields ).length && $( stateFields ).is( 'input' ) ) {
						const region = data.region ? data.region : null;
						$( stateFields ).val( region ).change();
					}

					self.setCustomerLocation( data );

					params.customer_location = {
						city: data?.id,
						city_code: data?.code,
						country_code: data?.country_code,
						latitude: data?.latitude,
						longitude: data?.longitude,
						region: data?.region,
						region_code: data?.region_code
					}

					if( !!params.is_checkout ) {
						//$( document.body ).trigger( 'update_checkout' );
						$( document.body ).trigger( 'updated_checkout_city', [ data ] );
					}
				} );

				$( fields ).on('select2:open', function() {
					$('.select2-search__field').prop('placeholder', params.i18n_strings.enter_city_name );
				} );

			} else {

				var $elements = $( fields ),
					input_name    = $elements.attr( 'name' ),
					input_id      = $elements.attr('id'),
					input_classes = $elements.attr('data-input-classes'),
					value         = $elements.val(),
					placeholder   = $elements.attr( 'placeholder' ) || $elements.attr( 'data-placeholder' ) || '',
					$newelements;

				if ( $elements.is( 'select, input[type="hidden"]' ) ) {
					$newelements = $( '<input type="text" />' )
						.prop( 'id', input_id )
						.prop( 'name', input_name )
						.prop('placeholder', placeholder)
						.attr('data-input-classes', input_classes )
						.addClass( 'input-text  ' + input_classes );
					$elements.filter( '.select2-hidden-accessible' ).selectWoo( 'destroy' );
					$elements.replaceWith( $newelements );
					$elements.off( 'select2:select' );
				}
			}
		},
		loadCityfieldForCart: function( event ) {
			if( !!params.is_cart ) {
				this.loadCityfield();
			}
		},
		refreshAddressField: function () {
			if( !!params.clean_address_field ) {
				$( this.addressFields.join( ', ' ) ).val( null ).change();
			}
		},
		setCustomerLocation: function( location ) {
			const self = this;
			if( location ) {
				$.post( params.wc_ajax_url.toString().replace( '%%endpoint%%', 'edostavka_set_customer_location' ), {
					//action: 'edostavka_set_customer_location',
					customer_id: params.customer_id,
					location,
					beforeSend: function() {

						if( !! params.is_cart ) {
							self.blockNode( $( 'form.woocommerce-shipping-calculator' ) );
							$( 'button[name="calc_shipping"]').prop( 'disabled', true );
						}

						if( !! params.is_checkout ) {
							$( self.cityFieldElements.join( ', ' ) )
								.prop( 'disabled', true )
								.parent()
								.find( '.select2-container' )
								.css( { opacity: .65 } )
								.find( '.select2-selection' )
								.css( 'cursor', 'wait' );
							self.blockNode( $( '.woocommerce-checkout-review-order-table' ) );
						}
					}
				} ).done(function () {

					if( !! params.is_cart ) {
						self.unBlock( $('form.woocommerce-shipping-calculator') );
						$( 'button[name="calc_shipping"]' ).prop('disabled', false);
					}

					if( !! params.is_checkout ) {
						$( self.cityFieldElements.join( ', ' ) )
							.prop( 'disabled', false )
							.parent()
							.find( '.select2-container' )
							.css( { opacity: 1 } )
							.find( '.select2-selection' )
							.css( 'cursor', 'pointer' );
						self.unBlock( $( '.woocommerce-checkout-review-order-table' ) );
						$( document.body ).trigger( 'update_checkout' );
					}
				} );
			}
		},
		countryFieldElements: [
			'#billing_country',
			'#shipping_country',
			'#calc_shipping_country'
		],
		stateFieldElements: [
			'#billing_state',
			'#shipping_state',
			'#calc_shipping_state'
		],
		addressFields: [
			'#billing_address_1',
			'#shipping_address_1'
		],
		cityFieldElements: [
			'#billing_city',
			'#shipping_city',
			'#calc_shipping_city'
		],
		billingCountry: function () {
			/**
			 * Тут код может выглядеть немного запутанным.
			 * Мы сначала получаем значение из поля "Страна", и если значение default или undefined то тогда возвращаем страну пользователя.
			 * Страну пользователя получаем следующим алгоритмом, если значение country_value default, то используем страну магазина по умолчанию, иначе используем значение страны пользователя.
			 */
			const country_value = $( this.countryFieldElements.join( ',' ) ).val();
			return $.inArray( country_value, [ 'default', 'undefined' ] ) !== -1 ?
				( params.customer_country ?
						( country_value == 'default' ? params.default_country : params.customer_country ) :
						params.default_country
				) :
				country_value
		},
		isBlocked: function ( $node ) {
			return $node.is( '.processing' ) || $node.parents( '.processing' ).length;
		},
		blockNode: function( $node, message = null ) {
			if ( ! this.isBlocked( $node ) ) {
				$node.addClass( 'processing' ).block( {
					message: message,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				} );
			}
		},
		unBlock:function( $node ) {
			$node.removeClass( 'processing' ).unblock();
		}
	};

	wc_edostavka_city_select.init();

} )( jQuery, edostavka_checkout_params );
