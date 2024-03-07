/*global jQuery */
( function( $, default_settings ) {
	'use strict';

	/**
	 * Woodev Dadata suggestions plugin
	 *
	 * @param {object} options
	 */
	$.fn.WoodevDadataSuggestions = function (options ) {
		return this.each( function() {
			( new $.WoodevDadataSuggestions( $( this ), options ) );
		} );
	}

	/**
	 * Initialize the Backbone Modal
	 *
	 * @param {object} element [current element to apply suggestions]
	 * @param {object} options [additional options object]
	 */
	$.WoodevDadataSuggestions = function( element, options ) {
		// Set settings
		const settings = $.extend( {}, this.defaultOptions, options );

		if( $( element ).closest( '.form-row' ).hasClass( 'address-field' ) ) {
			$( element ).closest( '.form-row' ).removeClass( 'address-field' )
		}

		const suggestions = $( element ).suggestions( settings )

		const plugin = suggestions.suggestions()

		this.disableOrEnable.call( plugin )
	}

	/**
	 * Set default options
	 *
	 * @type {object}
	 */
	$.WoodevDadataSuggestions.prototype.defaultOptions = $.extend( {}, default_settings, {
		type: 'ADDRESS',
		count: 10,
		minChars: 2,
		triggerSelectOnBlur: false, //Автоматически подставлять подходящую подсказку из списка, когда текстовое поле теряет фокус. (если true)
		noSuggestionsHint: false, //Отключаем текст что ничего не найдено по запросу
		onSearchError: function( query, jqXHR, textStatus, errorThrown ) {
			console.log( 'Сервер dadata вернул ошибку при запросе', errorThrown )
		},
		beforeRender: function () {
			if( $( this ).closest( '.form-row' ).hasClass( 'address-field' ) ) {
				$( this ).closest( '.form-row' ).removeClass( 'address-field' )
			}
		},
		onSearchStart: function () {
			if( $( this ).closest( '.form-row' ).hasClass( 'address-field' ) ) {
				$( this ).closest( '.form-row' ).removeClass( 'address-field' )
			}
		}
	} )

	$.WoodevDadataSuggestions.prototype.disableOrEnable = function() {

		if( this.options ) {
			if( 'RU' !== this.options?.countryISOCode ) {
				this.disable()
			} else if( this.disabled ) {
				this.enable()
			}
		}
	}

} )( jQuery, wc_edostavka_suggestions_plugin_params );
