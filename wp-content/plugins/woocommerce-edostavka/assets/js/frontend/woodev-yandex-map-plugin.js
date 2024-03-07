;( function ( factory ) {
	if ( typeof define === 'function' && define.amd ) {
		define(['jquery'], factory);
	} else if ( typeof exports === 'object' ) {
		module.exports = factory;
	} else {
		factory( jQuery );
	}
}( function( $ ) {
	'use strict';

	$.fn.WDYandexMap = function( options ) {
		return this.each( function() {
			( new $.WDYandexMap( $( this ), options ) );
		});
	};

	$.WDYandexMap = function( element, options ) {

		var settings = $.extend( {
			namespace: 'WCEdostavkaMaps',
			template: 'wc-modal-edostavka-map-balloon',
			points_url: '',
			set_delivery_point_ajax:'',
			city_id: '',
			delivery_type: 'ALL',
			active_point_icon: '',
			point_icon: '',
			current_pvz:'',
			enable_fill_postcode: false,
			show_search_field: false,
			need_update_checkout:false,
			search_control_placeholder: 'Найти пункт выдачи',
			hide_modal_on_close_confirm: false,
			contents_dimensions_volume: 0,
			contents_dimensions_max_size: 0
		}, options );

		if ( window[settings.namespace] !== undefined ) {

			window[settings.namespace].ready().done( function ( ym ) {
				$( element ).bind( 'WDYandexMap.destroy', ( new $.WDYandexMap.mapLoad( ym, element.get(0).id, settings ) ), function( event, action ) {
					if( action && action.refresh && event.data ) {
						event.data.geoObjects.removeAll();
					} else if( event.data && typeof event.data.destroy === 'function' ) {
						event.data.destroy();
					}
				} );
			} );

		} else {
			throw new Error( 'The yandex map API is NOT loaded yet' );
		}
	}

	$.WDYandexMap.mapLoad = function( ym, elem, settings ) {

		var map,
			currentPVZ,
			$balloon_template = wp.template( settings.template );

		if( ! map ) {
			map = new ym.Map( elem, {
				center: [55.76, 37.64],
				zoom: 12,
				controls: [],
				duration: 300
			}, {
				minZoom: 4,
				maxZoom: 18
			} );
		}

		map.controls.add( new ym.control.ZoomControl(), {
            position: {
                left: 12,
                bottom: 70
            }
        } );

		map.layers.add( new ym.Layer('https://tile%d|4.maps.2gis.com/tiles?%c&v=4.png', {
			projection: ym.projection.sphericalMercator
		} ) );

		map.copyrights.add('© 2Gis');

		const onBalloonButtonClick = function( element ) {

			$( document ).on("click", ".balloon__button", function( button ) {

				if ( element.id != currentPVZ ) {

					Backbone.ajax({
						method: 'POST',
						dataType: 'json',
						url: settings.set_delivery_point_ajax,
						data: element.properties.data,
						beforeSend: function(){
							$( button.target ).addClass( 'balloon__button_disabled' );
							$( button.target ).prop( { disabled: true } );
							$( '.my-balloon' ).block( {
								message: null,
								overlayCSS: {
									background: '#fff',
									opacity: 0.6
								}
							} );
						},
						complete: function(){
							$( '.my-balloon' ).unblock();
						},
						success: function( response ) {

							if( response && response.success ) {

								$( '#billing_address_1' ).val( element.properties.data?.location?.address ).trigger( 'change' );

								if( $( 'select.select-delivery-points' ).length > 0 ) {
									$( 'select.select-delivery-points' ).val( element.properties.data?.code ).trigger( 'change' );
								}

								if( settings.enable_fill_postcode && 'yes' == settings.enable_fill_postcode ) {
									$( '#billing_postcode' ).val( element.properties.data?.location?.postal_code ).trigger( 'change' );
								}

								var newOverlay = ballonObject.objects.overlays.getById( element.id );
								if( newOverlay ) {
									var setActiveIconImage = new $.WDYandexMap.setActiveIconImage( ballonObject, element.id, settings );
									newOverlay.events.remove( 'mapchange', setActiveIconImage );
								}

								if( currentPVZ ) {
									var oldOverlay = ballonObject.objects.overlays.getById( currentPVZ );

									if( oldOverlay ) {
										var setNonActiveIconImage = new $.WDYandexMap.setNonActiveIconImage( ballonObject, currentPVZ, settings );
										oldOverlay.events.remove( 'mapchange', setNonActiveIconImage );
									}
								}

								currentPVZ = element.id;

								if( settings.need_update_checkout ) {
									$( button.target ).addClass( 'hidden' );
									$( button.target ).next().removeClass( 'hidden' );

									$( document.body ).trigger( 'update_checkout' );
								}

							} else {
								$( button.target ).removeClass( 'balloon__button_disabled' );
								$( button.target ).prop( { disabled: false } );
							}
						}
					});
				}
			} );
		}

		var ballonObject = new $.WDYandexMap.objectManager( settings );

		ballonObject.objects.events.add( "balloonopen", function( balloon ) {

			var element = ballonObject.objects.getById( balloon.get("objectId") );

			element.properties.balloonContent = function( data ) {
				return $balloon_template( { data, currentPVZ } );
			} ( element.properties.data );

			ballonObject.objects.balloon.setData( ballonObject.objects.balloon.getData() );

			onBalloonButtonClick( element );

		} );

		ballonObject.objects.events.add( "balloonclose", function() {
			$( document ).off( "click", ".balloon__button" );
		} );

		ballonObject.clusters.events.add( "balloonopen", function( balloon ) {

			const features = ballonObject.clusters.getById( balloon.get("objectId") ).features;

			features && features.length && features.forEach( function( future ) {

				const futureId = ballonObject.objects.getById( future.id );

				futureId.properties.balloonContent = function( data ) {
					return $balloon_template( { data, currentPVZ } );
				} ( futureId.properties.data );

				ballonObject.clusters.balloon.setData( ballonObject.clusters.balloon.getData() );

				onBalloonButtonClick( future );
			} );

		} );

		ballonObject.clusters.events.add("balloonclose", function( balloon ) {
			$( document ).off( "click", ".balloon__button" );
		} );

		Backbone.ajax({
			method:   'GET',
			dataType: 'json',
			url:      settings.points_url,
			data:     {
				city_code: settings.city_id,
				type: settings.delivery_type
			},
			beforeSend: function(){
				$( '#' + elem ).block( {
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				} );
			},
			complete: function(){
				if( ballonObject && typeof ballonObject.getBounds === 'function' && ballonObject.getBounds() ) {
					map.setBounds( ballonObject.getBounds(), {
						zoomMargin: 15,
						checkZoomRange: true,
						duration: 400
					} );
				} else {
					$.alert( {
						closeIcon: true,
						backgroundDismiss: true,
						escapeKey: true,
						animationBounce: 1,
						useBootstrap: false,
						theme: 'material',
						boxWidth: '420px',
						animateFromElement: false,
						type: 'orange',
						title: 'Пункты выдачи не найдены!',
						icon: 'fa fa-exclamation-triangle',
						content: function() {
							return [
								'К сожалению, в выбранном городе не найден ни один пункт для получения заказа, удовлетворяющий условиям доставки.',
								'Пожалуйста, выберите другой доступный метод доставки.'
							].join( '<br />' );
						},
						buttons: {
							close: {
								text: 'Я понял!',
								btnClass: 'btn-warning',
								action: function ( button ) {
									if( settings.hide_modal_on_close_confirm && typeof settings.hide_modal_on_close_confirm === 'function' ) {
										settings.hide_modal_on_close_confirm.call( this, map );
									}
								}
							}
						}
					} );
				}

				$( '#' + elem ).unblock();
			},
			success: function( response ) {
				if( response.success && response.data ) {

					if( response.chosen_delivery_point && response.chosen_delivery_point.code ) {
						currentPVZ = response.chosen_delivery_point.code;
					}

					ballonObject.add( function( result ){
						const setter = new Set
						const mapper = new Map

						return result.filter( function ( item ) {

							//Если пункт выдачи Постамат и у этого потстамата есть ограничения по габаритам, то выполняем условие
							if( 'POSTAMAT' == item.type && item.dimensions ) {

								//получаем из массива dimensions элемент у которого самые большие габариты
								const reduced = item.dimensions.reduce( function ( previousValue, currentValue ) {
									return previousValue > currentValue ? previousValue : currentValue
								} )

								//получаем из массива reduced общий объём ячейки
								const volume = Object.values( reduced ).reduce( function( acc, rec ) {
									return acc * rec
								} )

								//получаем из массива reduced значение наибольшей стороны
								const maxValue = Math.max.apply( null, Object.values( reduced ) )

								if( settings.contents_dimensions_max_size <= maxValue && settings.contents_dimensions_volume <= volume ) {
									//если наибольшая сторона ячейки больше чем наибольшая сторона всех товаров в корзине
									//и общий объём ячейки больше чем объём всех товаров в корзине, то возвращаем объект
									return item
								}
							} else {
								/*
								 * Иначе просто возвращаем объект
								 */
								return item
							}

						} ).map( function ( item ) {

							if( ! setter.has( item.code ) ) {

								setter.add( item.code )

								const coordinates = [ item.location.latitude, item.location.longitude ]
								const hash = coordinates.join( ',' )

								if( mapper.has( hash ) ) {
									mapper.set( hash, mapper.get( elem.hash ) + 1 )
								} else {
									mapper.set( hash, 1 )
								}

								return {
									type: "Feature",
									id: item.code,
									geometry: {
										type: "Point",
										coordinates: [ item.location.latitude, item.location.longitude ]
									},
									options: {
										balloonLayout: new $.WDYandexMap.templateLayout( settings ),
										balloonPanelMaxMapArea: 0,
										balloonAutoPan: true,
										openEmptyBalloon: true,
										hideIconOnBalloonOpen: false,
										iconLayout: "default#image",
										//iconImageHref: elem.code === currentPVZ ? settings.active_point_icon : settings.point_icon,
										iconImageHref: item.type === 'PVZ' ? settings.pvz_icon : settings.postamat_icon,
										iconImageSize: item.code === currentPVZ ? [50, 70] : [45, 45],
										iconImageOffset: item.code === currentPVZ ? [-25, -40] : [-22, -23]
									},
									properties: {
										balloonContent: null,
										placemarkId: item.code,
										data: item,
										type: item.type,
										orderNumber: mapper.get( hash )
									}
								}
							}
						} )

					}( response.data ) );
				}
			}
		});

		map.geoObjects.add( ballonObject );

		if( 'yes' == settings.show_search_field ) {

			map.controls.add( new window[settings.namespace].control.SearchControl( {
				options: {
					provider: {
						geocode: function( request, options ) {

							var deferred 		= new window[settings.namespace].vow.defer(),
								collection 		= new window[settings.namespace].GeoObjectCollection(),
								offset 			= options.skip || 0,
								limit 			= options.results || 10,
								points 			= [],
								request_text 	= request.toLowerCase();

							$.map( ballonObject.objects.getAll(), function( object ) {

								if( ! object.properties.data ) return;

								const { name, location, address_comment, note } = object.properties.data;

								var point_address = location.address_full,
									point_short_address = location.address,
									point_index = location.postal_code,
									point_comment = address_comment || note,
									point_coords = [
										location.latitude,
										location.longitude
									];

								if ( name.toLowerCase().indexOf( request_text ) != -1 ||
									point_address.toLowerCase().indexOf( request_text ) != -1 ||
									point_short_address.toLowerCase().indexOf( request_text ) != -1 ||
									( point_comment && point_comment.toLowerCase().indexOf( request_text ) != -1 ) ||
									point_index == request_text
								) {
									points.push( {
										name,
										description: point_short_address,
										balloonContentBody: point_comment,
										coords: point_coords,
										boundedBy: [ point_coords, point_coords ]
									} );
								}
							} );

							points = points.splice( offset, limit );

							for (var i = 0, l = points.length; i < l; i++) {
								collection.add( new window[settings.namespace].Placemark( points[i].coords, points[i] ) );
							}

							deferred.resolve({
								geoObjects: collection,
								metaData: {
									geocoder: {
										request: request,
										found: collection.getLength(),
										results: limit,
										skip: offset
									}
								}
							});

							return deferred.promise();
						}
					},
					noPlacemark: true,
					noSuggestPanel: false,
					resultsPerPage: 10,
					placeholderContent: settings.search_control_placeholder
				}
			} ) );
		}

		return map;

	};

	$.WDYandexMap.objectManager = function( settings ) {

		/*
		 * @see https://yandex.ru/dev/maps/jsapi/doc/2.1/ref/reference/Clusterer.html
		 */
		return new window[settings.namespace].ObjectManager( {
			clusterize: true,
			clusterIconColor: "#0a8c37",
			//maxZoom: 18, //Максимальный коэффициент масштабирования карты, на котором происходит кластеризация объектов
			clusterBalloonLayout: new $.WDYandexMap.templateLayout( settings ),
			clusterBalloonContentLayout: window[settings.namespace].templateLayoutFactory.createClass( '<div class="my-balloon-tabs"><ul class="my-balloon-tabs__main-tabs">{% for geoObject in properties.geoObjects %}<li class="main-tabs__list-item {% if (geoObject.properties.orderNumber == 1) %}main-tabs__list-item_current{% endif %}"><span class="main-tabs__link">{{ geoObject.properties.data.name }}</span></li>{% endfor %}</ul><div class="tabs__content">{% for geoObject in properties.geoObjects %}<div class="tabs__item">{{ geoObject.properties.balloonContent|raw }}</div>{% endfor %}</div></div>', {
				build: function() {
					this.constructor.superclass.build.call( this );
					this.element = $( '.my-balloon-tabs', this.getParentElement() );
					this.element.find( '.main-tabs__link' ).on( 'click', $.proxy( this.onTabLinkClick, this ) );
				},
				onTabLinkClick: function( event ) {

					const currentTabIndex = $( event.target ).closest( 'li' ).index();

					this.element.find( 'ul.my-balloon-tabs__main-tabs > li' ).removeClass( 'main-tabs__list-item_current' );
					$( event.target ).closest( 'li' ).addClass( 'main-tabs__list-item_current' );

					this.element.find(".tabs__content").find(".tabs__item").not('.tabs__item:eq(' + currentTabIndex + ')').slideUp( {
						duration: 0
					} );

					this.element.find(".tabs__content").find('.tabs__item:eq(' + currentTabIndex + ')').slideDown( {
						duration: 0
					} );

					event.preventDefault();
				}
			} ),
			clusterBalloonItemContentLayout: window[settings.namespace].templateLayoutFactory.createClass('<div class="balloon__body">{{ properties.balloonContent|raw }}</div>'),
			clusterBalloonContentLayoutHeight: 'auto',
			clusterBalloonLeftColumnHeight: 'auto'
			//minClusterSize: 3
			/*clusterIcons: [
				{
                    href: 'https://www.cdek.ru/map/cluster2.svg',
                    size: [40, 40],
                    offset: [-20, -20]
                }
			]*/
		} );
	}

	$.WDYandexMap.templateLayout = function( settings ) {

		return window[settings.namespace].templateLayoutFactory.createClass( '<div class="my-balloon"><a class="my-balloon__close-button" href="#">&times;</a>$[[options.contentLayout]]</div>', {
			build: function() {
				this.constructor.superclass.build.call( this );
				this.element = $( '.my-balloon', this.getParentElement() );
				this.element.find( '.my-balloon__close-button' ).on("click", $.proxy( this.onCloseClick, this ) );
			},
			onCloseClick: function( event ) {
				event.preventDefault(),
				this.events.fire("userclose")
			},
			getShape: function() {

				if ( ! this.isElement( this.element ) ) return this.constructor.superclass.getShape.call( this );

				var position = this.element.position();

				return new window[settings.namespace].shape.Rectangle( new window[settings.namespace].geometry.pixel.Rectangle( [
					[ position.left, position.top ],
					[ position.left + this.element[0].offsetWidth, position.top + this.element[0].offsetHeight ]
				] ) )
			},
			isElement: function( element ) {
				return element && element[0]
			}
		} );
	}

	$.WDYandexMap.setActiveIconImage = function( objectManager, objectId, settings ) {
		objectManager.objects.setObjectOptions( objectId, {
			//iconImageHref: settings.active_point_icon,
			iconImageSize: [50, 70],
			iconImageOffset: [-25, -40]
		} );
	}

	$.WDYandexMap.setNonActiveIconImage = function( objectManager, objectId, settings ) {
		objectManager.objects.setObjectOptions( objectId, {
			//iconImageHref: settings.point_icon,
			iconImageSize: [45, 45],
			iconImageOffset: [-22, -23]
		} );
	}

} ) );
