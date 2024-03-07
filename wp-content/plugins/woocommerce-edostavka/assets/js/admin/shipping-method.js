( function( $ ) {

	function getEnhancedSelectFormatString() {
		return {
			'language': {
				errorLoading: function() {
					return wc_enhanced_select_params.i18n_searching;
				},
				inputTooLong: function( args ) {
					var overChars = args.input.length - args.maximum;

					if ( 1 === overChars ) {
						return wc_enhanced_select_params.i18n_input_too_long_1;
					}

					return wc_enhanced_select_params.i18n_input_too_long_n.replace( '%qty%', overChars );
				},
				inputTooShort: function( args ) {
					var remainingChars = args.minimum - args.input.length;

					if ( 1 === remainingChars ) {
						return wc_enhanced_select_params.i18n_input_too_short_1;
					}

					return wc_enhanced_select_params.i18n_input_too_short_n.replace( '%qty%', remainingChars );
				},
				loadingMore: function() {
					return wc_enhanced_select_params.i18n_load_more;
				},
				maximumSelected: function( args ) {
					if ( args.maximum === 1 ) {
						return wc_enhanced_select_params.i18n_selection_too_long_1;
					}

					return wc_enhanced_select_params.i18n_selection_too_long_n.replace( '%qty%', args.maximum );
				},
				noResults: function() {
					return wc_enhanced_select_params.i18n_no_matches;
				},
				searching: function() {
					return wc_enhanced_select_params.i18n_searching;
				}
			}
		};
	}

	$( 'select[name="woocommerce_edostavka_tariff"]' ).on( 'change', function( event ) {

		$.post( wc_edostavka_shipping_settings_params.ajax_url, {
			action: 'edostavka_get_tariff_by_code',
			code: event.target.value,
			beforeSend: function() {
				$( '.wrap.woocommerce .cdek-method-description' ).block( {
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				} )
			}
		}, function( response ) {

			if( response.success && response.data ) {
				const template = wp.template( 'wc-edostavka-tariff-info' );
				$( '.wrap.woocommerce .cdek-method-description' ).empty().html( template( response.data ) );
			} else {
				console.warn( response );
			}

		} ).always( function() {
			$( '.wrap.woocommerce .cdek-method-description' ).unblock();
		} );

	} );

	$( ':input.wc-edostavka-sender-city-select' ).filter( ':not(.enhanced)' ).each( function() {

		$( this ).selectWoo( $.extend( {
			minimumInputLength: 2,
			ajax: {
				url: wc_edostavka_shipping_settings_params.ajax_url,
				method: 'POST',
				dataType: 'json',
				delay: 250,
				data: function( params ) {
					return {
						action: 'edostavka_get_location_cities',
						country_codes: wc_edostavka_shipping_settings_params.country_code,
						city: params.term,
						size: 10
					}
				},
				processResults: function( data, params ) {
					var terms = [];
					if ( data.success && data.data ) {
						data.data.filter( function( item ) {
							return wc_edostavka_shipping_settings_params.country_code == item.country_code
						} ).map( function( term ) {
							terms.push( {
								id:   term.code,
								text: term.city + ' (' + term.region + ')'
							} );
						} );
					}

					return {
						results: terms,
					}
				},
				cache: true
			}
		}, getEnhancedSelectFormatString() ) ).addClass( 'enhanced' ).on( 'select2:select', function( event ) {

			$.post( wc_edostavka_shipping_settings_params.ajax_url, {
				action: 'edostavka_get_deliverypoints',
				city_code: event.target.value,
				type: 'PVZ',
				lang: wc_edostavka_shipping_settings_params.locale,
				take_only: false,
				beforeSend: function() {
					$( '.wc-edostavka-delivery-point-select + .select2' ).block( {
						message: null,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6
						}
					} )
				}
			} ).done( function( response ) {

				var options = [];

				if( response && response.success && response.data ) {
					response.data.map( function( point ) {
						options.push( sprintf( '<option value="%s">[%s] %s (%s)</option>', point.code, point.code, point.name, point.location.address_full ) );
					} );
				}

				$( ':input.wc-edostavka-delivery-point-select' ).empty().val( null ).append( options.join( '' ) ).trigger( 'change' );
			} ).always( function() {
				$( '.wc-edostavka-delivery-point-select + .select2' ).unblock();
			} );

		} );
	} );

	$( 'select.wc-edostavka-tariff-select' ).on( 'change', function( event ) {

		$.post( wc_edostavka_shipping_settings_params.ajax_url, {
			action: 'edostavka_get_tariff_by_code',
			code: event.target.value
		}, function( response ) {

			if( response.success && response.data ) {
				if( response.data.type.indexOf( 'door_' ) == 0 ) {
					$( '#woocommerce_edostavka_devivery_point' ).closest( 'tr' ).hide();
					$( '#woocommerce_edostavka_dropoff_address' ).closest( 'tr' ).show();
				} else {
					$( '#woocommerce_edostavka_devivery_point' ).closest( 'tr' ).show();
					$( '#woocommerce_edostavka_dropoff_address' ).closest( 'tr' ).hide();
				}
			} else {
				console.warn( response );
			}
		} );

	} ).trigger( 'change' );

	$( '#woocommerce_edostavka_location_limit' ).on( 'change', function( event ) {

		if( event.target.value == 'none' ) {
			$( '#woocommerce_edostavka_states' ).closest( 'tr' ).hide();
			$( '#woocommerce_edostavka_cities' ).closest( 'tr' ).hide();
		} else {
			$( '#woocommerce_edostavka_states' ).closest( 'tr' ).show();
			$( '#woocommerce_edostavka_cities' ).closest( 'tr' ).show();
		}

	} ).trigger( 'change' );

	$( '#woocommerce_edostavka_round_cost' ).on( 'change', function( event ) {

		if( event.target.value == 'none' ) {
			$( '#woocommerce_edostavka_round_cost_range' ).closest( 'tr' ).hide();
		} else {
			$( '#woocommerce_edostavka_round_cost_range' ).closest( 'tr' ).show();
		}

	} ).trigger( 'change' );

	$( '#woocommerce_edostavka_states' ).on( 'change', function( event ) {

		$( '#woocommerce_edostavka_cities' ).each( function() {

			const self = this;

			$( self ).empty().val( null );

			$.map( wc_edostavka_shipping_settings_params.saved_cities, function( data, code ) {
				if( $.inArray( event.target.value.toString(), [ 'any', data.region_code.toString() ] ) !== -1 ) {
					$( self ).append( $( '<option />', {
						value: code,
						text: sprintf( '%s%s%s', data.city, '' == event.target.value && data.region ? sprintf( ' (%s)', data.region ) : '', data.sub_region ? sprintf( ' (%s)', data.sub_region ) : '' ),
						title: [ data.country, data.region, data.sub_region, data.city ].filter( function( element ) {
							return element;
						} ).join( ', ' ),
						selected: true
					} ) );
				}
			} );

			$( self ).selectWoo( $.extend( {
				minimumInputLength: 0,
				allowClear: true,
				multiple: true,
				ajax: {
					url: wc_edostavka_shipping_settings_params.ajax_url,
					method: 'POST',
					dataType: 'json',
					delay: 250,
					data: function( params ) {
						const query = {
							action: 'edostavka_get_location_cities',
							//region_code: event.target.value,
							//city: params.term,
							lang: wc_edostavka_shipping_settings_params.locale,
							size: 50,
							page: params.page && params.page > 1 ? params.page - 1 : 0
						}

						if( 'any' !== event.target.value ) {
							query.region_code = event.target.value;
						}

						if( event.target.dataset?.countries ) {
							query.country_codes = event.target.dataset.countries;
						}

						if( params.term !== '' ) {
							query.city = params.term;
						}

						return query;
					},
					processResults: function( data, params ) {
						var terms = [];

						if ( data.success && data.data ) {
							data.data.filter( function( item ) {
								if( 'any' !== event.target.value ) {
									return event.target.value == item.region_code
								}
								return true;
							} ).map( function( term ) {
								terms.push( {
									id:   term.code,
									text: sprintf( '%s%s%s', term.city, 'any' == event.target.value && term.region ? sprintf( ' (%s)', term.region ) : '', term.sub_region ? sprintf( ' (%s)', term.sub_region ) : '' ),
									title: [ term.country, term.region, term.sub_region, term.city ].filter( function( element ) {
										return element;
									} ).join( ', ' )
								} );
							} );
						}

						return {
							results: terms,
							pagination: {
								more: data.data.length >= 50
							}
						}
					},
					cache: true
				}
			}, getEnhancedSelectFormatString() ) ).trigger( 'change' );

		} );

	} ).change();

	$( 'form#mainform' ).on( 'submit', function( event ) {

		if(
			$( '#woocommerce_edostavka_location_limit').val() !== 'none' &&
			$( '#woocommerce_edostavka_states' ).val() == 'any' &&
			$( '#woocommerce_edostavka_cities' ).val() == ''
		) {
			event.preventDefault();
			event.stopImmediatePropagation();
			$.alert({
				title: false,
				content: 'Вы выбрали значение "Любой регион" в опции "Регион", поэтому вам необходимо выбрать хотя бы один город в опции "Города" или выберите конкретный регион в опции "Регион".',
				closeIcon: true,
				backgroundDismiss: true,
				escapeKey: true,
				animationBounce: 1,
				useBootstrap: false,
				theme: 'modern',
				boxWidth: '450px',
				animateFromElement: false,
				icon   : 'fa fa-exclamation-triangle',
				type   : 'red',
			});
		}
	} );

} )( jQuery );
