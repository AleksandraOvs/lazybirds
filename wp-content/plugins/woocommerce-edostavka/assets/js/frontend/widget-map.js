( function( $, d, params ){

	const wc_edostavka_widget_map = {
		initialize: function( container ) {

			this.container 		= container
			this._target		= null
			this.onModalLoaded 	= this.onModalLoaded.bind( this )
			this.onModalInit	= this.onModalInit.bind( this )
			this.onModalRemoved	= this.onModalRemoved.bind( this )
			this.destroyMap		= this.destroyMap.bind( this )
			this.mapLoad		= this.mapLoad.bind( this )

			$( d.body )
				.on( 'wc_backbone_modal_loaded', this.onModalLoaded )
				.on( 'click', '.wc-edostavka-choose-delivery-point', this.onModalInit )
				.on( 'wc_backbone_modal_removed', this.onModalRemoved )

		},
		destroyMap: function() {
			$( this.container ).trigger( 'WDYandexMap.destroy' )
		},
		onModalLoaded: function() {
			this.destroyMap()
			this.render()
		},
		render: function() {
			$( '.modal-map-container' ).empty()
			this.mapLoad()
		},
		onModalInit: function( event ) {
			event.preventDefault()
			this._target = event.target
			$( this ).WCBackboneModal( { template : 'wc-modal-edostavka-map' } )
		},
		onModalRemoved:function() {
			this.destroyMap()
		},
		mapLoad: function() {

			const delivery_type = $( this._target ).data( 'delivery_type' )

			$( this.container ).WDYandexMap( {
				points_url: params.points_url,
				set_delivery_point_ajax: params.set_delivery_point_ajax,
				city_id: $( this._target ).data( 'city_id' ),
				delivery_type: delivery_type == 'stock' ? 'PVZ' : 'POSTAMAT',
				postamat_icon: params.postamat_icon,
				pvz_icon: params.pvz_icon,
				enable_fill_postcode: params.enable_fill_postcode,
				show_search_field: params.show_search_field,
				need_update_checkout: true,
				search_control_placeholder: params.i18n_strings.search_control_placeholder,
				contents_dimensions_volume: params.contents_dimensions.volume,
				contents_dimensions_max_size: params.contents_dimensions.max_size,
				hide_modal_on_close_confirm: function() {
					$( '.modal-close' ).click()
				}
			} )
		}
	}

	wc_edostavka_widget_map.initialize( '#wc-edostavka-map-container' )

} )( jQuery, document, edostavka_widget_map_params );
