( function( $ ) {

    $( ':input.wc-edostavka-default-city' ).filter( ':not(.enhanced)' ).each( function() {

        var code_value;

        if( edostavka_integration_params.customer_city && ! $.isEmptyObject( edostavka_integration_params.customer_city ) ) {
            var $element = $( this );
            $.each( edostavka_integration_params.customer_city, function ( code, name ) {
                $element.empty().append(
                    $( '<option />' )
                        .prop( 'value', code )
                        .text( name )
                );
                code_value = code;
            } );
        }

        $( this ).selectWoo( {
            allowClear:  true,
            placeholder: $( this ).data( 'placeholder' ),
            minimumInputLength: 2,
            placeholderOption: 'first',
            ajax: {
                url: edostavka_integration_params.ajax_url,
                method: 'POST',
                dataType: 'json',
                delay: 250,
                data: function( params ) {
                    return {
                        action: 'edostavka_get_location_cities',
                        city: params.term,
                        size: 20,
                        lang: edostavka_integration_params.lang
                    }
                },
                processResults: function ( data, params ) {
                    var terms = [];

                    if ( data.success && data.data ) {
                        data.data.map( function( term ) {
                            terms.push( {
                                id:   term.code,
                                text: term.city + ' (' + term.region + ')',
                                title: [ term.country, term.region, term.city ].join( ', ' )
                            } );
                        } );
                    }

                    return { results: terms };
                },
                cache: true
            },
            language: {
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
        } ).addClass( 'enhanced' );

        if( code_value ) {
            $( this ).val( code_value ).trigger( 'change' );
        }

    } );

    $( 'html' ).on( 'click', function( event ) {
        if ( this === event.target ) {
            $( ':input.wc-edostavka-default-city' ).filter( '.select2-hidden-accessible' ).selectWoo( 'close' );
        }
    } );

	$( '#woocommerce_edostavka_enable_dropdown_city_field' ).on( 'change', function( event ) {

		if( event.target.value == 'none' ) {
			$( '#woocommerce_edostavka_enable_custom_city' ).closest( 'tr' ).hide();
			$( '#woocommerce_edostavka_clean_address_field' ).prop( 'disabled', true );
			$( '#woocommerce_edostavka_enable_suggestions_city' ).prop( 'disabled', false );
			$( '#woocommerce_edostavka_enable_suggestions_state' ).prop( 'disabled', false );
		} else {
			$( '#woocommerce_edostavka_enable_custom_city' ).closest( 'tr' ).show();
			$( '#woocommerce_edostavka_clean_address_field' ).prop( 'disabled', false );
			$( '#woocommerce_edostavka_enable_suggestions_city' ).prop( 'disabled', true );
			$( '#woocommerce_edostavka_enable_suggestions_state' ).prop( 'disabled', true );
		}

	} ).trigger( 'change' );

	$( '#woocommerce_edostavka_disable_state_field' ).on( 'change', function( event ) {

		if( event.target.value == 'always' ) {
			$( '#woocommerce_edostavka_enable_suggestions_state' ).prop( 'disabled', true );
		} else {
			$( '#woocommerce_edostavka_enable_suggestions_state' ).prop( 'disabled', false );
		}

	} ).trigger( 'change' );

	$( '#woocommerce_edostavka_disable_postcode_field' ).on( 'change', function( event ) {

		if( event.target.value == 'always' ) {
			$( '#woocommerce_edostavka_fill_postcode_field' ).prop( 'disabled', true );
		} else {
			$( '#woocommerce_edostavka_fill_postcode_field' ).prop( 'disabled', false );
		}

	} ).trigger( 'change' );

	$( '#woocommerce_edostavka_cron_auto_update_orders' ).on( 'change', function ( event ) {

		if( ! $( event.target ).is( ':checked' ) ) {
			$( '#woocommerce_edostavka_cron_auto_update_orders_interval' ).closest( 'tr' ).hide();
			$( '#woocommerce_edostavka_cron_update' ).hide();
		} else {
			$( '#woocommerce_edostavka_cron_auto_update_orders_interval' ).closest( 'tr' ).show();
			$( '#woocommerce_edostavka_cron_update' ).show();
		}

	} ).trigger( 'change' );

	$( '#woocommerce_edostavka_packing_method' ).on( 'change', function( event ) {

		if( event.target.value == 'box_packing' ) {
			$( '#packing_options' ).show();
			$( '#woocommerce_edostavka_unpacking_item_method' ).closest( 'tr' ).show();
			$( '#woocommerce_edostavka_name_single_box' ).closest( 'tr' ).hide();
			$( '#woocommerce_edostavka_name_item_box' ).closest( 'tr' ).hide();
		} else if( event.target.value == 'single_box' ) {
			$( '#packing_options' ).hide();
			$( '#woocommerce_edostavka_unpacking_item_method' ).closest( 'tr' ).hide();
			$( '#woocommerce_edostavka_name_single_box' ).closest( 'tr' ).show();
			$( '#woocommerce_edostavka_name_item_box' ).closest( 'tr' ).hide();
		} else {
			$( '#packing_options' ).hide();
			$( '#woocommerce_edostavka_unpacking_item_method' ).closest( 'tr' ).hide();
			$( '#woocommerce_edostavka_name_single_box' ).closest( 'tr' ).hide();
			$( '#woocommerce_edostavka_name_item_box' ).closest( 'tr' ).show();
		}

	} ).trigger( 'change' );

	$( '.wc_edostavka_boxes' ).on( 'click', '.checkbox-toggle-enabled', function() {
		var $link   = $( this ),
			$row    = $link.closest( 'td' ),
			$toggle = $link.find( '.woocommerce-input-toggle' ),
			$checkbox = $row.find( 'input[type="checkbox"]' );

		if( $toggle.hasClass( 'woocommerce-input-toggle--enabled' ) ) {
			$checkbox.prop( 'checked', false );
			$toggle.removeClass( 'woocommerce-input-toggle--enabled' );
			$toggle.addClass( 'woocommerce-input-toggle--disabled' );
		} else if( $toggle.hasClass( 'woocommerce-input-toggle--disabled' ) ) {
			$checkbox.prop( 'checked', true );
			$toggle.removeClass( 'woocommerce-input-toggle--disabled' );
			$toggle.addClass( 'woocommerce-input-toggle--enabled' );
		}

		return false;
	} );

	$( '.wc_edostavka_boxes .insert' ).click( function() {
		var $tbody = $('.wc_edostavka_boxes').find('tbody');
		var size = $tbody.find('tr').length;
		var code = '<tr class="new">\
							<td class="check-column"><input type="checkbox" /></td>\
							<td><input type="text" size="30" name="boxes_name[' + size + ']" /></td>\
							<td><input type="text" size="5" name="boxes_length[' + size + ']" /><span>см</span></td>\
							<td><input type="text" size="5" name="boxes_width[' + size + ']" /><span>см</span></td>\
							<td><input type="text" size="5" name="boxes_height[' + size + ']" /><span>см</span></td>\
							<td><input type="text" size="5" name="boxes_box_weight[' + size + ']" /><span>кг</span></td>\
							<td><input type="text" size="5" name="boxes_cost[' + size + ']" /></td>\
							<td><input type="checkbox" class="toggle-checkbox" name="boxes_enabled[' + size + ']" checked="checked" /><a href="#" class="checkbox-toggle-enabled"><span class="woocommerce-input-toggle woocommerce-input-toggle--enabled"></span></a></td>\
						</tr>';

		$tbody.append( code );

		return false;
	} );

	$('.wc_edostavka_boxes .remove').click(function() {
		var $tbody = $('.wc_edostavka_boxes').find('tbody');

		$tbody.find('.check-column input:checked').each(function() {
			$(this).closest('tr').hide().find('input').val('');
		});

		return false;
	});

} )( jQuery );
