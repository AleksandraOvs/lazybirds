( function( $, params ) {

	const wc_edostavka_address_autocomplete = {
		init: function () {

			this.loadAutoComplete	= this.loadAutoComplete.bind( this )
			this.updateCityField	= this.updateCityField.bind( this )
			this.fillCityField		= this.fillCityField.bind( this )
			this.fillStateField		= this.fillStateField.bind( this )

			$( document.body ).on( 'updated_checkout updated_checkout_city', this.loadAutoComplete )
			$( document.body ).on( 'updated_delivery_data', this.updateCityField )
		},
		loadAutoComplete: function( event, data ) {

			let cityName,
				countryCode

			const self 		= this
			const elements 	= self.addressFields.join( ', ' )
			const $elements = $( elements )

			if( ! $elements.length ) return

			const $parents 	= $elements.closest( '.form-row' )

			if( event.type == 'updated_checkout' ) {
				cityName = $( '#billing_city' ).val() || $( '#shipping_city' ).val() || ( edostavka_checkout_params && edostavka_checkout_params.default_city ? edostavka_checkout_params.default_city : null )
				countryCode = $( '#billing_country' ).val() || $( '#shipping_country' ).val()
			} else if( event.type == 'updated_checkout_city' && data ) {
				cityName = data.text
				countryCode = data.country_code
			}

			if( $parents.hasClass( 'address-field' ) ) {
				$parents.removeClass( 'address-field' )
			}

			let options = {
				token: params.token,
				partner: '6846',
				type: "ADDRESS",
				count: 10,
				constraints: {
					locations: {
						city: cityName
					}
				},
				restrict_value: cityName ? true : false,
				geoLocation: false,
				language: params.language,
				hint: params.i18n_strings.hint,
				noSuggestionsHint: params.i18n_strings.no_hint,
				bounds: 'street-flat', //искать в переделах город-квартира
				onSelect: function ( suggestions ) {
					if( ! $parents.hasClass( 'address-field' ) ) {
						$parents.addClass( 'address-field' )
					}

					$( this ).trigger( 'change', [ suggestions.data ] )

					if( ! cityName ) {

						const kladr_id = suggestions.data.kladr_id.length > 13 ? suggestions.data.kladr_id.substr( 0, 11 ) + '00' : suggestions.data.kladr_id

						self.fetchDadataAPI( 'findById/delivery', { query: kladr_id } ).done( function( response ) {
							if( response.suggestions && response.suggestions.length > 0 ) {
								const deliveryData = response.suggestions[0].data
								$( document.body ).trigger( 'updated_delivery_data', [ deliveryData, suggestions.data ] )
							}
						} ).fail( function( xhr ) {
							if( xhr.responseJSON ) {
								console.error( xhr.responseJSON.message ? xhr.responseJSON.message : xhr.responseJSON )
							} else {
								console.error( 'Во время получения информации о службах доставки произошла неивестная ошибка' )
							}
						} ).always( function () {
							$( document.body ).trigger( 'update_checkout' )
						} )
					}

					if( !!params.fill_postcode_field ) {
						self.fillInputFeld( $( self.postCodeField.join( ', ' ) ), suggestions.data?.postal_code )
					}

					if( !!params.reload_checkout_fields && cityName ) {
						$( document.body ).trigger( 'update_checkout' )
					}
				},
				formatSelected: function (suggestion) {
					return suggestion.value.toString().split( ', ' ).filter( function( item ) {
						return item !== suggestion.data.country &&
							item !== suggestion.data.postal_code &&
							item !== suggestion.data.city_with_type &&
							item !== suggestion.data.city_district_with_type &&
							item !== suggestion.data.area_with_type &&
							item !== suggestion.data.region_with_type
					} ).join( ', ' )
				},
				onSearchError: function( query, jqXHR, textStatus, errorThrown ) {
					console.log( 'Сервер DADATA вернул ошибку при запросе', errorThrown )
				}
			}

			/*
			 * Инициализируем плагин Suggestions и записываем экземпляр в переменную для дальнейшего использования.
			 */
			const suggestionsPlugin = $elements.suggestions( options ).suggestions()

			/*
			 * Если текущая страна не Россия, то отключаем плагин. Иначе, если страна Россия и плагин не активен, то включаем его.
			 */
			if( 'RU' !== countryCode ) {
				suggestionsPlugin.disable()
			} else {
				if( suggestionsPlugin.disabled ) {
					suggestionsPlugin.enable()
				}
			}

			/*
			 * Если плагин не отключён, включена опция "определять местоположение" и город покупателя не указан, то заполняем поля по гео данным.
			 */
			if( ! suggestionsPlugin.disabled && !!params.detect_location && ! cityName ) {

				self.fetchDadataAPI( 'iplocate/address', null, 'GET' ).done( function( { location } ) {

					if( location && location.data ) {

						suggestionsPlugin.setSuggestion( location )

						self.fetchDadataAPI( 'findById/delivery', { query: location.data.kladr_id } ).done( function( { suggestions } ) {

							if( suggestions && suggestions.length > 0 ) {
								$( document.body ).trigger( 'updated_delivery_data', [ suggestions[0].data, location.data ] )
							}

						} )

						if( !!params.fill_postcode_field && location.data.postal_code ) {
							self.fillInputFeld( $( self.postCodeField.join( ', ' ) ), location.data.postal_code )
						}
					}
				} )
			}
		},
		addressFields: [
			'#billing_address_1',
			'#shipping_address_1'
		],
		stateFields: [
			'#billing_state',
			'#shipping_state'
		],
		cityFields: [
			'#billing_city',
			'#shipping_city'
		],
		postCodeField: [
			'#billing_postcode',
			'#shipping_postcode'
		],
		fetchDadataAPI: function( endpoint = '', body = {}, type = 'POST' ) {
			const ajaxParams = {
				type: type,
				contentType: 'application/json',
				headers: {
					'Authorization': 'Token ' + params.token
				}
			}

			if( body ) {
				ajaxParams.data = JSON.stringify( body )
			}

			return $.ajax( 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/' + endpoint, ajaxParams )
		},
		updateCityField: function( event, deliveryData, suggestionsData ) {

			if( deliveryData && deliveryData.cdek_id ) {
				this.setCustomerLocation( deliveryData.cdek_id ).done( function( response ) {
					if( response.success && response.data && edostavka_checkout_params && edostavka_checkout_params.customer_location ) {
						edostavka_checkout_params.customer_location = response.data
					}
				} ).fail( function( xhr ) {
					console.warn( 'An error occurred during setting customer location data.' )
				} )
			}

			if( suggestionsData ) {
				if( suggestionsData.city ) {
					this.fillCityField( suggestionsData.city )
				}
				if( suggestionsData.region_with_type ) {
					this.fillStateField( suggestionsData.region_with_type )
				} else if( suggestionsData.region ) {
					this.fillStateField( suggestionsData.region )
				}
			}
		},
		setCustomerLocation: function( code ) {
			return $.post( params.ajax_url, {
				action: 'edostavka_set_customer_location_by_id',
				lang: params.language == 'ru' ? 'rus' : 'eng',
				code: code
			} )
		},
		fillCityField: function( city ) {

			const $cityFields = $( this.cityFields.join( ', ' ) )

			if( $cityFields.is( 'select' ) ) {
				const $option = $( '<option />', {
					text: city,
					value: city,
					selected: true
				} )
				$cityFields.append( $option )
			}

			$cityFields.val( city ).change()
		},
		fillStateField: function( region ) {
			this.fillInputFeld( $( this.stateFields.join( ', ' ) ), region )
		},
		fillInputFeld: function( $node, value ) {
			if( $node.is( 'input' ) ) {
				$node.val( value ).change()
			}
		},
		joinUtil: function( array, separator = ', ' ) {
			return array.filter( function( elem ) {
				return elem
			} ).join( separator )
		},
		makeAddressString: function( data ) {
			const city = data.city !== data.region ? data.city : ''
			const flat_type = data.flat ? 'flat' : ''
			return this.joinUtil( [ data.country, data.region, data.area, city,
				this.joinUtil( [ data.settlement_type, data.settlement ], ' ' ),
				this.joinUtil( [ data.street_type, data.street ], ' ' ),
				this.joinUtil( [ data.house, data.block_type, data.block ], ' ' ),
				this.joinUtil( [ flat_type, data.flat], ' ' )
			] )
		}
	}

	wc_edostavka_address_autocomplete.init()

} )( jQuery, edostavka_address_autocomplete_params )
