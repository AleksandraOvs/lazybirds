( function( $ ) {
    'use strict'

	$( document ).ready( function () {

		$.fn.selectWoo.amd.define( 'edostavka/resultsAdapter', [ 'select2/utils', 'select2/results' ], function ( Utils, ResultsList ) {

			const ResultAdapter = function( decorated, $element, options, dataAdapter ) {
				return decorated.call( this, $element, options, dataAdapter );
			}

			ResultAdapter.prototype.setClasses = function () {
				var self = this;

				this.data.current( function (selected) {

					const customerLocation = self.options.get('customerLocation');

					const selectedIds = customerLocation && customerLocation.city_code ? [customerLocation.city_code.toString()] : $.map(selected, function (s) {
						return s.id.toString();
					});

					var $options = self.$results.find('.select2-results__option[data-selected]');

					$options.each(function () {
						var $option = $(this);

						var item = $.data(this, 'data');

						var id = customerLocation && customerLocation.city_code && item?.code ? item.code.toString() : item?.id.toString();

						if ( ( item?.element != null && item?.element.selected ) ||
							( item?.element == null && $.inArray(id, selectedIds) > -1)) {
							$option.attr('data-selected', 'true');
						} else {
							$option.attr('data-selected', 'false');
						}
					});

				} );
			}

			return Utils.Decorate( ResultsList, ResultAdapter );

		} );

		$.fn.WCEdostavkaSelectCity = function( options ) {
			return this.each( function() {
				( new $.WCEdostavkaSelectCity( $( this ), options ) );
			} );
		}

		$.WCEdostavkaSelectCity = function( $element, options ) {
			const settings = $.extend( {
				placeholder: '',
				defaultValue: '',
				countryCode: '',
				minimumInputLength: 2,
				placeholderOption: 'first',
				allowTags: false,
				ajaxUrl: false,
				customerLocation: {}
			}, options || {} );

			var xhr           = false,
				input_name    = $element.attr( 'name' ),
				input_id      = $element.attr('id'),
				input_classes = $element.attr('data-input-classes'),
				value         = $element.val() || settings.defaultValue,
				placeholder   = $element.attr( 'placeholder' ) || $element.attr( 'data-placeholder' ) || settings.placeholder,
				$newelement;

			if ( $element.is( 'input' ) ) {
				$newelement = $( '<select />' )
					.prop( 'id', input_id )
					.prop( 'name', input_name )
					.data( 'placeholder', placeholder )
					.attr( 'data-input-classes', input_classes )
					.addClass( input_classes );
				$element.replaceWith( $newelement );
				$element = $newelement;
			}

			if( value && settings.customerLocation && settings.customerLocation.country_code && settings.countryCode !== settings.customerLocation.country_code ) {
				value = '';
			}

			$element.empty();

			if( value ) {
				$element.append(
					$( '<option />' )
						.prop( 'value', value )
						.text( value )
				);
			}

			$element.selectWoo( {
				placeholder: settings.placeholder,
				minimumInputLength: settings.minimumInputLength,
				placeholderOption: settings.placeholderOption,
				width: '100%',
				customerLocation: settings.customerLocation,
				ajax: {
					url: settings.ajaxUrl,
					method: 'POST',
					dataType: 'json',
					delay: 250,
					data: function( params ) {
						return {
							action: 'edostavka_get_location_cities',
							country_codes: settings.countryCode,
							city: params.term || value,
							size: 10
						}
					},
					transport: function( xhr_params, success, failure ) {

						if ( xhr ) {
							xhr.abort();
						}

						xhr = $.ajax( xhr_params );
						xhr.then( success );
						xhr.fail( failure );

						return xhr;
					},
					processResults: function ( data, params ) {
						var terms = [];

						if ( data.success && data.data ) {
							data.data.filter( function( item ) {
								return settings.countryCode == item.country_code
							} ).map( function( term ) {
								terms.push( {
									_resultId: term.code,
									id:   term.city,
									text: term.city,
									title: [ term.country, term.region, term.city ].join( ', ' ),
									code: term.code,
									region: term.region,
									region_code: term.region_code,
									sub_region: term.sub_region || null,
									country: term.country,
									country_code: term.country_code,
									longitude: term.longitude,
									latitude: term.latitude
								} );
							} );
						}

						return { results: terms };
					},
					cache: true
				},
				resultsAdapter: $.fn.selectWoo.amd.require( 'edostavka/resultsAdapter' ),
				templateSelection: function( item, container ) {
					return item.id || item.text;
				},
				templateResult: function( item, element ) {

					if ( item.loading || ! item.region ) {
						return item.text;
					}

					const region = [ item.region ];

					if( item.sub_region ) {
						region.push( item.sub_region );
					}

					const $container = $( '<div />', {
						class: 'select2-result-item',
						html: item.text + ' (' + region.join( ', ' ) + ')'
					} );

					if( item.country ) {
						$container.append( $( '<small />', {
							style: 'color:#cbcbcb; display: block;',
							text: [ item.text, item.region, item.country ].join( ', ' )
						} ) );
					}

					return $container;
				},
				tags: settings.allowTags,
				insertTag: function( data, tag ) {
					if( ! data.filter( function( item ) {
						return item !== tag
					} ).length ) {
						tag.isCustom = true;
						data.push( tag )
					}
				},
				language: {
					errorLoading: function() {
						return wc_country_select_params.i18n_searching;
					},
					inputTooLong: function( args ) {
						var overChars = args.input.length - args.maximum;

						if ( 1 === overChars ) {
							return wc_country_select_params.i18n_input_too_long_1;
						}

						return wc_country_select_params.i18n_input_too_long_n.replace( '%qty%', overChars );
					},
					inputTooShort: function( args ) {
						var remainingChars = args.minimum - args.input.length;

						if ( 1 === remainingChars ) {
							return wc_country_select_params.i18n_input_too_short_1;
						}

						return wc_country_select_params.i18n_input_too_short_n.replace( '%qty%', remainingChars );
					},
					loadingMore: function() {
						return wc_country_select_params.i18n_load_more;
					},
					maximumSelected: function( args ) {
						if ( args.maximum === 1 ) {
							return wc_country_select_params.i18n_selection_too_long_1;
						}

						return wc_country_select_params.i18n_selection_too_long_n.replace( '%qty%', args.maximum );
					},
					noResults: function() {
						return wc_country_select_params.i18n_no_matches;
					},
					searching: function() {
						return wc_country_select_params.i18n_searching;
					}
				}
			} ).addClass( 'enhanced' );

			//FIXME: при вызове события change происхоидт бесконечная перезагрузка чекаута
			//$element.val( value ).trigger( 'change' );

			return $element;
		}

	} );

} )( jQuery );
