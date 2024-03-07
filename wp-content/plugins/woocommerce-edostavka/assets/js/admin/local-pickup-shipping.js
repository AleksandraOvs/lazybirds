( function( $ ) {

	$( document.body ).ready( function () {

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

		$( '#woocommerce_local_pickup_allowed_cities' ).selectWoo( $.extend( {
			minimumInputLength: 3,
			allowClear: true,
			multiple: true,
			ajax: {
				url: wc_enhanced_select_params.ajax_url,
				method: 'POST',
				dataType: 'json',
				delay: 250,
				data: function( params ) {
					return {
						action: 'edostavka_get_location_cities',
						size: 50,
						page: params.page && params.page > 1 ? params.page - 1 : 0,
						city: params.term,
						country_codes: $( '#woocommerce_local_pickup_allowed_cities' ).data( 'countries' )
					}
				},
				processResults: function( data, params ) {
					var terms = [];
					if ( data.success && data.data ) {
						data.data.map( function( term ) {
							terms.push( {
								id:   term.code,
								text: sprintf( '%s%s%s', term.city, term.region ? sprintf( ' (%s)', term.region ) : '', term.sub_region ? sprintf( ' (%s)', term.sub_region ) : '' )
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
		}, getEnhancedSelectFormatString() ) );

	} );

} )( jQuery );
