(function($){

/**
 * # Подгружаем Наши коллекции на главную страницу   
 *   @link https://lazybirds.ru      
 */
function lazy_collections_block(){          
var loader = "<img  class='loader' src='https://lazybirds.ru/wp-content/uploads/2023/06/loader.gif'>"; 		
   $.ajax({
         url: '/wp-admin/admin-ajax.php ', 
         type: "POST", 
         data: { 
             action: 'lazy_collections_block',  
         },
        beforeSend: function() {
           $('.collections').addClass('loader');
           $("#collections").append(loader);   
        },
        success:function(data){
            $('.collections').removeClass('loader');
		    $("#collections").html(data);
          }
      });
  }

$(document).ready(function(){
lazy_collections_block();

/**
 * # Обновляем сумму в корзине при обновлении количесвта товара  
 */
	$(document).on('click', '.qty-button', function() {
		var upd_cart_btn = $(".button").attr('name', 'update_cart');
		upd_cart_btn.trigger('click');
	});

/**
 * # Обновляем whishlit 
 */
	window.updateCountWhishlist =  function updateCountWhishlist() {
		if($('.wishlist-count').length <= 0) {
			var wishlistCountSpan = "<span class='show_count wishlist-count'>1</span>";	
			$('.header-wishlist').prepend(wishlistCountSpan);
		}else {
			var wishlistCount =  parseInt($('.wishlist-count').text())+1;
			$('.wishlist-count').text(wishlistCount);
		}
	}

/**
 * # Открываем вкладку с отзывами  
 */
	$('.review-link').click(function(){
		$('#tab-review').trigger('click');
	});	

/**
 * # Стрелки для мобильной версии категорий 
 */
	function addNavConsole() {
		let outNavConsole = "";
		outNavConsole += '<span class="elem-nav-console nav-console-left"><i class="fas fa-chevron-left"></i></span>';
		outNavConsole += '<span class="elem-nav-console nav-console-right"><i class="fas fa-chevron-right"></i></span>';
		$('.intro_cat').append(outNavConsole);
	}


	$(document).on('click', '.elem-nav-console', function(){
		$('.cat-item').toggle();
		$('.intro-cat-thumb').each(function(){
			$(this).attr('src', $(this).attr('data-src') ); 
		});
	});

		setTimeout(function (){
			$('.intro-cat-thumb').each(function(){
				$(this).attr('src', $(this).attr('data-src') ); 
			});
		},2000);

/**
 * # Контролируем именение экрамна
 */
	var handleMatchMedia = function(mediaQuery) {
		if (mediaQuery.matches) {
			addNavConsole();
		} else {
		}
	},
	mql = window.matchMedia('all and (max-width: 480px)');
	handleMatchMedia(mql);
	mql.addListener(handleMatchMedia); 

/**
 * # Проходим по товарам и переносим иконку WhishList вверх
 */
	$('.product').each(function(){
		var  addWishListIcon = $(this).find('.add-to-wishlist-button').clone();
		$(this).find('.product-thumbnail').before(addWishListIcon);
	});

/**
 * # Mobile Menu 
 */
	$('.js-hamburger').click(function(){
		$(this).toggleClass('is-active');
		$('.secondary-menu').toggleClass('is-active');
		$('body').toggleClass('fixed');
	});

	$('.js-close').click(function(){
		$('.secondary-menu').removeClass('is-active');
	})

/**
 * # Показываем поиск header 
 */
	$('.header-search').click(function(){
		$('.search-site_header').toggleClass('is-active');
		$('.search-field').focus();
	});

/**
 * # Добавляем количество к корзине 
 */
	$(document).on("added_to_cart", function(e) {
		/** # удаляем временный инпут */
		$('#templ-data-count').remove();
		$.ajax({
			url: '/wp-admin/admin-ajax.php ',
			type: "POST",
			data: {
				action : 'update_templ_data_count',
			},
			beforeSend: function() {
				$('.mini-cart-count').addClass('update').text('');
			},
			success:function(data){    	
				$('.mini-cart-count').removeClass('update');
				$('body').append(data);
				var templDataCount = $('#templ-data-count').val();
				$('.mini-cart-count').text(templDataCount);
			}
		});
	});

/**
 * # WishList 
 */
	$('.add-laz-wishlist-button').click(function(e){
		e.preventDefault();
	});

/**
 * # Показываем таблицу размеров 
 */
	$('.js-open_clothing-size-chart').click(function(e){
		e.preventDefault();
		$('.clothing-size-chart_overlay').toggleClass('is-active');	
		$('.clothing-size-chart').toggleClass('is-active');	
	});

	$('.close_clothing-size-chart').click(function(){
		$('.clothing-size-chart_overlay').removeClass('is-active');	
		$('.clothing-size-chart').removeClass('is-active');			   		
	});

/**
 * # Закрываем таблицу размеров  esc на клавиатуре  
 */
	$(document).keydown(function(e) {
		if (e.keyCode === 27) {
			$('.lightbox-gallery').remove();
			$('.clothing-size-chart_overlay').removeClass('is-active');	
			$('.clothing-size-chart').removeClass('is-active');			   		
		}
	});

/**
 * # Табы на странице товара
 */
	$('.product-tab').click(function(){
		$('.product-tab').removeClass('is-active');
		$(this).addClass('is-active');

		var ariaControlsProductTab = $(this).attr('aria-controls');
		$('.product-tab-entry-content').removeClass('is-active');

		$('.product-tab-entry-content').each(function(){
			var ariaControlsEntryContent = $(this).attr('aria-controls');
			if(ariaControlsEntryContent  ===  ariaControlsProductTab  ) {
				$(this).addClass('is-active');
			}
		});

		if ($(this).attr('aria-controls') === "tab-3") {
			$('.comment-respond').addClass('is-active');
		}else {
			$('.comment-respond').removeClass('is-active');
		}
	});
})

	var handleMatchMediaScreen = function(mediaQuery) {
		if (mediaQuery.matches) {
			scrollPageProduct();
		} 
	},
	mql = window.matchMedia('all and (min-width: 993px)');
	handleMatchMediaScreen(mql);
	mql.addListener(handleMatchMediaScreen); 

/**
 * # Фиксируем блок при скроле    
 */

/** # Добавляем обертку */
$('.summary.entry-summary').wrap('<div class="entry-summary_wrapper">');
function scrollPageProduct(){
	$(window).scroll(function(){
		var counter = 0;
		var $elementBlock = $('.product-tabs');
		var scroll = $(window).scrollTop() + $(window).height();
		if($($elementBlock).length) {
			var offsetPrd = $elementBlock.offset().top + $elementBlock.height();
		}else {
			return;
		}

		if($(this).scrollTop()>280){
			$('.entry-summary').addClass('is-fixed');
			$('.entry-summary').removeClass('is-sticky-bottom');
		}

		if (scroll > offsetPrd && counter == 0) {
			$('.entry-summary').removeClass('is-fixed');
			$('.entry-summary').addClass('is-sticky-bottom');
			counter = 1;
		}

		else if($(this).scrollTop()<280) {
			$('.entry-summary').removeClass('is-fixed');
		}
	});
}	 
	
}(jQuery));


/**
 * # Маска для ввыода номера телефона  
 */
 window.addEventListener("DOMContentLoaded", function() {
   function setCursorPosition(pos, elem) {
       elem.focus();
       if (elem.setSelectionRange) elem.setSelectionRange(pos, pos);
       else if (elem.createTextRange) {
           var range = elem.createTextRange();
           range.collapse(true);
           range.moveEnd("character", pos);
           range.moveStart("character", pos);
           range.select()
   }
 }
   function mask(event) {
       var matrix = "+7 (___) ___-____",
           i = 0,
           def = matrix.replace(/\D/g, ""),
           val = this.value.replace(/\D/g, "");
       if (def.length >= val.length) val = def;
       this.value = matrix.replace(/./g, function(a) {
           return /[_\d]/.test(a) && i < val.length ? val.charAt(i++) : i >= val.length ? "" : a
       });
       if (event.type == "blur") {
           if (this.value.length == 2) this.value = ""
       } else setCursorPosition(this.value.length, this)
   };
     var input = document.querySelectorAll(".tel input");
     for(var i = 0; i <input.length; i++ ) {
       input[i].addEventListener("input", mask, false);
       input[i].addEventListener("focus", mask, false);
       input[i].addEventListener("blur", mask, false); 
     }     
 });