const $ = jQuery;
function doc_images() {

    ///ширина 3 высота 4
    $(".card-content .swiperCardImages").each(function() {
        let th = $(this),
        height = th.height(),
        width = th.width();

        th.css({ height: width / 3 * 4 + 'px' });
    });

    ///ширина 4 высота 3
    $("").each(function() {
        let th = $(this),
        height = th.height(),
        width = th.width();

        th.css({ height: width / 4 * 3 + 'px' });
    });

    ///квадрат
    $("").each(function() {
        let th = $(this),
        height = th.height(),
        width = th.width();

        th.css({ height: width + 'px' });
    });

}

let width = $(window).width();
    let body = $('body');
    let menu = $('.header-center');

    $(document).on('click', '.brg-menu-btn', function () {
      $(this).toggleClass('_open');
      $('html, body').toggleClass('o-hidden'); 
      menu.toggleClass('_open');
      body.toggleClass('_fixed');
      
        
    });

function updateCountWhishlist() {
    toastr["success"]("Товар успешно добавлен в избранное", "Успешно");
    $(".soo_wl_header .show_count").show();
    $(".soo_wl_header .show_count").html( parseFloat( parseFloat( $(".soo_wl_header .show_count").text() ) + 1 ) );
}


$(function() {
    ////

    var swiperCardImages = new Swiper(".swiperCardImages", {
        slidesPerView: 1,
        lazy: true,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });

    $(".search-icon-form .icon").click(function() {

        $(this).toggleClass('is_active');
        $('.header__actions__search').toggleClass('is_active');

       // $(this).parent().find(".input__hidden").toggle( 200 );

        return false;
    });

    /**
     * адаптив
     */
    doc_images();

    $(window).resize(function() {
        doc_images();
    });
    $(window).scroll(function() {
        doc_images();
    });
    $(".lmp_button").click(function() {
        setTimeout(function() {
            doc_images();
        }, 2000);
    });

    /**
     * Галерея в товаре
     */
    $data_col = 0;
    $(".gallery-thumbs_mm76 .woocommerce-product-gallery__image").each(function() {
        let th = $(this);
        let img_four = $(".gallery-thumbs_mm76 .woocommerce-product-gallery__image").eq(3);

        $data_col++;

        if( $data_col > 4 ) {
            th.hide();
            if( !img_four.hasClass('clicked') ) {
                img_four.addClass('clicked');
            }
        }
        
    });

    let nums_photos = ($data_col - 4);
    $(".gallery-thumbs_mm76 .woocommerce-product-gallery__image").eq(3).attr('data-cols', nums_photos);

    if( nums_photos >= 1 ) {
        $(".gallery-thumbs_mm76").append('<div class="thumb-closes" style="display:none;"><i class="fas fa-chevron-left"></i></div>');
    }

    ///clicked
    $(".gallery-thumbs_mm76 .woocommerce-product-gallery__image.clicked").click(function() {

        let th = $(this);

        $(".gallery-thumbs_mm76 .woocommerce-product-gallery__image").fadeIn( 200 );
        $(".thumb-closes").fadeIn( 200 );
        th.removeClass('clicked');

    });
    $('body').on('click', '.thumb-closes', function() {
        let th = $(this);

        th.hide();
        $(".gallery-thumbs_mm76 .woocommerce-product-gallery__image").eq(3).addClass('clicked');

        i = 0;
        $(".gallery-thumbs_mm76 .woocommerce-product-gallery__image").each(function() {
            let th = $(this);
    
            i++;
            
            if( i > 4 ) {
                th.hide();
            }
            
        });

        return false;
    });

    /**
     * tabs
     */
    $('.product-tab').click(function() {

        let th = $(this);
        let tabs_content = $(".product-tabs-content");
        let aria = th.attr('aria-controls');
        let tab = $(".product-tab-entry-content");

        tab.hide();
        tabs_content.find('[aria-controls="'+aria+'"]').fadeIn( 200 );

        $(".product-tab").removeClass('is-active');
        th.addClass('is-active');

        return false;
    });

    /**
     * box for right
     */
    $('a[href^="#"]').click(function(){
        let anchor = $(this).attr('href');
        if( $(anchor).length > 0 ) {
            $(".overlay").fadeIn( 300 );

            if( $(anchor).hasClass('left__box_hidden') ) {
                $(anchor).animate({ 'left': 0 }, 200);
            } else {
                $(anchor).animate({ 'right': 0 }, 200);
            }
        }
    });
    $(".rb-close").click(function(){
        $(".overlay").fadeOut( 200 );

        if( $(window).width() <= 650 ) {

            if( $(this).hasClass('left__box_hidden') ) {
                px = '-290px';
            } else {
                px = '-100vw';
            }
            
        } else {

            if( $(this).hasClass('left__box_hidden') ) {
                px = '-290px';
            } else {
                px = '-700px';
            }

        }

        if( $(this).parent().hasClass('left__box_hidden') ) {
            $(this).parent().animate({ 'left': px }, 300);
        } else {
            $(this).parent().animate({ 'right': px }, 300);
        }
    });
    $(".overlay").click(function() {
        $(".overlay").fadeOut( 200 );

        $(".moda-right").each(function() {

            if( $(window).width() <= 650 ) {

                if( $(this).hasClass('left__box_hidden') ) {
                    px = '-290px';
                } else {
                    px = '-100vw';
                }
                
            } else {

                if( $(this).hasClass('left__box_hidden') ) {
                    px = '-290px';
                } else {
                    px = '-700px';
                }

            }

            if( $(this).hasClass('left__box_hidden') ) {
                $(this).animate({ 'left': px }, 300);
            } else {
                $(this).animate({ 'right': px }, 300);
            }

        });

    });

    /**
     * FancyBox
     */
    Fancybox.bind("[data-fancybox]", {});
    Fancybox.bind(".woocommerce-product-gallery__image > a", {
        groupAll: true,
    });
    $('.moda-right').unbind('click.fb');


    let form_login = $("#loginform");
    if( form_login.length >0 ) {

        form_login.find("[for='user_login']").css({ display: 'none' });
        form_login.find("#user_login").attr( 'required', 'true' );
        form_login.find("[for='user_login']").parent().append('<span>'+ form_login.find("[for='user_login']").text() +'</span>');
        ////<svg width="32" height="24" viewBox="0 0 32 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20.3005 11.9817C20.3005 13.303 19.8106 14.5701 18.9385 15.5044C18.0664 16.4386 16.8836 16.9635 15.6502 16.9635C14.4169 16.9635 13.2341 16.4386 12.362 15.5044C11.4899 14.5701 11 13.303 11 11.9817C11 10.6605 11.4899 9.39337 12.362 8.45912C13.2341 7.52486 14.4169 7 15.6502 7C16.8836 7 18.0664 7.52486 18.9385 8.45912C19.8106 9.39337 20.3005 10.6605 20.3005 11.9817Z" fill="#8EBCEE"/><path d="M31.8312 11.2337C28.3783 3.83314 22.2467 0 16 0C9.75487 0 3.62174 3.83314 0.168754 11.2337C0.0577749 11.4717 0 11.734 0 12C0 12.266 0.0577749 12.5283 0.168754 12.7663C3.62174 20.1669 9.75327 24 16 24C22.2451 24 28.3783 20.1669 31.8312 12.7663C31.9422 12.5283 32 12.266 32 12C32 11.734 31.9422 11.4717 31.8312 11.2337ZM16 20.5714C11.2493 20.5714 6.39629 17.7943 3.40733 12C6.39629 6.20571 11.2477 3.42857 16 3.42857C20.7507 3.42857 25.6037 6.20571 28.5927 12C25.6037 17.7943 20.7507 20.5714 16 20.5714Z" fill="#8EBCEE"/></svg>

        form_login.find("[for='user_pass']").css({ display: 'none' });
        form_login.find("#user_pass").attr( 'required', 'true' );
        form_login.find("[for='user_pass']").parent().append('<span>'+ form_login.find("[for='user_pass']").text() +'</span>');

        //form_login.find("[for='user_pass']").parent().append('<div class="eye_pass"><svg width="32" height="24" viewBox="0 0 32 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20.3005 11.9817C20.3005 13.303 19.8106 14.5701 18.9385 15.5044C18.0664 16.4386 16.8836 16.9635 15.6502 16.9635C14.4169 16.9635 13.2341 16.4386 12.362 15.5044C11.4899 14.5701 11 13.303 11 11.9817C11 10.6605 11.4899 9.39337 12.362 8.45912C13.2341 7.52486 14.4169 7 15.6502 7C16.8836 7 18.0664 7.52486 18.9385 8.45912C19.8106 9.39337 20.3005 10.6605 20.3005 11.9817Z" fill="#8EBCEE"/><path d="M31.8312 11.2337C28.3783 3.83314 22.2467 0 16 0C9.75487 0 3.62174 3.83314 0.168754 11.2337C0.0577749 11.4717 0 11.734 0 12C0 12.266 0.0577749 12.5283 0.168754 12.7663C3.62174 20.1669 9.75327 24 16 24C22.2451 24 28.3783 20.1669 31.8312 12.7663C31.9422 12.5283 32 12.266 32 12C32 11.734 31.9422 11.4717 31.8312 11.2337ZM16 20.5714C11.2493 20.5714 6.39629 17.7943 3.40733 12C6.39629 6.20571 11.2477 3.42857 16 3.42857C20.7507 3.42857 25.6037 6.20571 28.5927 12C25.6037 17.7943 20.7507 20.5714 16 20.5714Z" fill="#8EBCEE"/></svg></div>');

    }

    // $(".eye_pass").click(function() {

    //     let th = $(this);
    //     let input = th.parent().find('input');

    //     if( input.attr('type') == 'password' ) {
    //         input.attr('type', 'text');
    //     } else {
    //         input.attr('type', 'password');
    //     }

    //     return false;
    // });

    $("input.invalid").blur(function() {
        $(this).removeClass('invalid');
    });

    $("p > span").click(function() {

        let th = $(this);

        if( th.parent().find('input').length > 0 ) {
            th.parent().find('input').focus();
        }

    });

    /**
     * провыекра пустых полей
     */
    $("input").each(function() {
        if( $(this).val() == '' ) {
            $(this).addClass('input_empty');
        }
    });
    $("input").blur(function() {
        $(this).removeClass('input_empty');
    });
    $(".woocommerce-MyAccount-content input").blur(function() {
        $(this).removeClass('invalid');
    });

    /**
     * LoginForm
     */
    $("#loginform #user_login").blur(function() {
        let th = $(this);

        $.ajax({
			url: $ajax,
			type: 'POST',
			data: 'action=get_login&login='+th.val(),
			success: function( data ) {
                if( th.val() != '' ) {
                    if( data == 'no' ) {
                        th.addClass('invalid');
                        $(".login-requests").html('<span class="tt-info" style="margin-top: 30px;">Такой логине не зарегистрирован, <a href="/register?email='+th.val()+'">зарегистрироваться?</a></span>');
                        $("#wp-submit").attr('disabled', 'true');
                    } else {
                        th.removeClass('invalid');
                        $(".login-requests").html('');
                        $("#wp-submit").attr('disabled', false);
                    }
                }
			}
		});

        return false;
    });
    $("#loginform #user_pass").blur(function() {
        let th = $(this);

        $.ajax({
			url: $ajax,
			type: 'POST',
			data: 'action=get_auth&pass='+th.val()+'&login='+$("#loginform #user_login").val(),
			success: function( data ) {
                if( th.val() != '' ) {
                    if( data == 'no' ) {
                        th.addClass('invalid');
                        $(".login-requests").html('<span class="tt-info" style="margin-top: 30px;">Пароль к данному профилю не подходит</span>');
                        $("#wp-submit").attr('disabled', 'true');
                    } else {
                        th.removeClass('invalid');
                        $(".login-requests").html('');
                        $("#wp-submit").attr('disabled', false);
                    }
                }
			}
		});

        return false;
    });
    $("#wp_signup_form #email").blur(function() {
        let th = $(this);

        $.ajax({
			url: $ajax,
			type: 'POST',
			data: 'action=get_login&login='+th.val(),
			success: function( data ) {
                if( th.val() != '' ) {
                    if( data != 'no' ) {
                        th.addClass('invalid');
                        $(".email_requests").html('<span class="tt-info" style="margin-top: 30px;">Пташка, кажется у тебя уже есть личный кабинет!<br><a href="/recovery?email='+th.val()+'">Восстановить доступ</a></span>');
                        $("#submitbtn").attr('disabled', 'true');
                    } else {
                        th.removeClass('invalid');
                        $(".email_requests").html('');
                        $("#submitbtn").attr('disabled', false);
                    }
                }
			}
		});

        return false;
    });

    $( 'body' ).on( 'change', '.qty', function() { // поле с количеством имеет класс .qty
		$( '[name="update_cart"]' ).trigger( 'click' );
	} );

    $("body").on('click', '.close-message', function() {
        $(this).parent().parent().fadeOut( 200 );
    });

    /**
     * маски ввода
     */
    $.mask.definitions['h'] = "[0|1|3|4|5|6|7|9]"
    $("#account_phone, input[type=tel]").mask('+7 (h99) 999-99-99');

    $("#account_bdate").mask('99/99/9999');

    /**
     * ajax формы в настройках аккаунта
     */
    $(".ajax-address-user").submit(function() {

        let th = $(this);

        $.ajax({
			url: $ajax,
			type: 'POST',
            dataType: 'JSON',
			data: th.serialize(),
			success: function( data ) {
                if( data.status == 'ok' ) {
                    alert('Данные об адресе доставки успешно сохранены!');
                } else {
                    $.each(data.errors, function(i, v) {
                        if( v == 'yes' ) {
                            $("#" + i).addClass('invalid');
                        }
                    });
                }
			}
		});

        return false;

    });
    /////сменаа пароля
    //ajax-pass-user
    $(".ajax-pass-user").submit(function() {

        let th = $(this);

        $.ajax({
			url: $ajax,
			type: 'POST',
            dataType: 'JSON',
			data: th.serialize(),
			success: function( data ) {
                if( data.status == 'ok' ) {
                    alert('Пароль успешно сохранён!');
                } else {
                    alert('Пароли не совпадают');
                    $.each(data.errors, function(i, v) {
                        if( v == 'yes' ) {
                            $("#" + i).addClass('invalid');
                        }
                    });
                }
			}, error: function(dara) {
                console.log( dara );
            }
		});

        return false;

    });

    /**
     * е... навигация на главной
     */
    $(".nav-console-right, .nav-console-left").click(function(){

        $(".two__blocks_main").find('.card-cat').each(function() {
            if( $(this).is(":visible") ) {
                $(this).css({display: 'none'});
            } else {
                $(this).css({display: 'block'});
            }
        });

        return false;
    });

    /**
     * images_lists
     */
    var swiperGallery_1 = new Swiper(".swiperGallery_1", {
        spaceBetween: 10,
        slidesPerView: 4,
        freeMode: true,
        watchSlidesProgress: true,
    });
    var swiperGallery_2 = new Swiper(".swiperGallery_2", {
        spaceBetween: 10,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        thumbs: {
            swiper: swiperGallery_1,
        },
    });

    var swiperHero = new Swiper(".swiperHero", {
        spaceBetween: 20,
        slidesPerView: 2,
        freeMode: true,
        watchSlidesProgress: true,
    });

    var swiperBestsellers = new Swiper(".swiperBestsellers", {
        spaceBetween: 24,
        slidesPerView: 5,
        grabCursor: true,
        loop: true,
        freeMode: true,
        watchSlidesProgress: true,
        navigation: {
            nextEl: ".swiperBestsellers-control-prev",
            prevEl: ".swiperBestsellers-control-next",
        },
        breakpoints:{
            1024: {
                slidesPerView: 5
            },
            768: {
                slidesPerView: 3.2
            }
        }
    });

    var swiperFeedback = new Swiper(".swiperFeedback", {
        spaceBetween: 16,
        slidesPerView: 1.2,
        grabCursor: true,
        
        //loop: true,
        freeMode: true,
        watchSlidesProgress: true,
        navigation: {
            nextEl: ".swiperFeedback-control-prev",
            prevEl: ".swiperFeedback-control-next",
        },
        breakpoints:{
            1200: {
                slidesPerView: 4.5,
                spaceBetween: 24,
            },
            1024: {
                slidesPerView: 3.5,
                spaceBetween: 50,
                centeredSlides: false,
                loop: false,
            },
            768: {
                slidesPerView: 2.7,
                spaceBetween: 50,
            },
            480: {
                slidesPerView: 1.7,
                spaceBetween: 16,
            },
            320: {
                slidesPerView: 1.7,
                spaceBetween: 16,
                centeredSlides: true,
                loop: true,
              //  loop: true,
            }


        }
    });

    var swiperFeatured = new Swiper(".swiperFeatured", {
        spaceBetween: 16,
        slidesPerView: 2.5,
        grabCursor: true,
        
        //loop: true,
        freeMode: true,
        watchSlidesProgress: true,
        // navigation: {
        //     nextEl: ".swiperFeedback-control-prev",
        //     prevEl: ".swiperFeedback-control-next",
        // },
        breakpoints:{
            1200: {
                slidesPerView: 4.5,
                spaceBetween: 16,
            },
            1024: {
                slidesPerView: 3.5,
                spaceBetween: 16,
                centeredSlides: false,
                loop: false,
            },
            768: {
                slidesPerView: 4,
                spaceBetween: 16,
            },
            480: {
                slidesPerView: 2.5,
                spaceBetween: 16,
            },
            320: {
                slidesPerView: 2.5,
                spaceBetween: 16,
                centeredSlides: true,
                loop: true,
              //  loop: true,
            }


        }
    });

    if( $(".variations_form").length > 0 ) {
        setTimeout(function() {
            let variation_data = $(".variations_form").data('product_variations');
            for (var key in variation_data) {

                if( variation_data[key].max_qty.length == 0 ) {
                    //console.log( variation_data[key] );
                    for ( key2 in variation_data[key].attributes ) {
                        //console.log( variation_data[key].attributes[key2] );
                        $("[data-attribute_name='"+key2+"'] [data-value='"+variation_data[key].attributes[key2]+"']").addClass('not-aviable');
                    }
                }
            };
        }, 500);
    }

    /**
     * toastes
     */
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "3000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    /**
     * аккаунт
     */
    if( $('body').hasClass('woocommerce-edit-account') && $('body').hasClass('logged-in') ) {
        $(".button").each(function() {
            $(this).css({ 'min-width': $(".woocommerce-Input").eq(0).outerWidth() + 'px' });
        });
    }


    // $(document).on('click', '#loadmore button', function () {
    //     $(this).toggleClass('_open');
    //     $('html, body').toggleClass('o-hidden'); 
    //     menu.toggleClass('_open');
    //     body.toggleClass('_fixed');
        
          
    //   });

    var button = $('#loadmore button');

    button.click( function(){
        $('.row-collections').toggleClass('_open');
        $(this).remove();
    });

});


$(document).ready(function(){
    let timeout = undefined
  
    function update_cart() {
      if ( timeout !== undefined ) {
          clearTimeout( timeout )
        }
  
        timeout = setTimeout(function() {
          $("[name='update_cart']").removeAttr('disabled')
          $("[name='update_cart']").trigger("click")
        }, 500)
    }

    $('.woocommerce').on('change', 'input.qty', update_cart)
  
    setInterval(function(){
      $(".cart__counter").prop("onclick", null).off("click")
  
      $('.cart__counter').on( 'click', 'button.plus, button.minus', function(){
        var qty = $( this ).siblings('.quantity').find( '.qty' )
        var val = parseFloat(qty.val()) ? parseFloat(qty.val()) : '0'
        var max = parseFloat(qty.attr( 'max' ))
                  
        var min = parseFloat(qty.attr( 'min' ))
        var step = parseFloat(qty.attr( 'step' ))
                  
  
        if ( $(this).is( '.plus' ) ) {
          if ( max && ( max <= val ) ) {
            qty.val( max );
          } else {
            qty.val( val + step );
          }
        } else {
          if ( min && ( min >= val ) ) {
            qty.val( min )
          } else if ( val > 1 ) {
            qty.val( val - step )
          }
        }
      })
  
      $('.cart__counter-next').prop('onclick', null).off('click')
      $('.cart__counter-next').click(update_cart) 
  
      $('.cart__counter-prev').prop('onclick', null).off('click')
      $('.cart__counter-prev').click(update_cart)
    }, 1000)
  
  });
  
  