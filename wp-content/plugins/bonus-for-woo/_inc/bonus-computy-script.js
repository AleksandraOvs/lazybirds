jQuery(document).ready(function($){

let body = $('body');
/*Открытие закрытие использование бонусов в корзине*/
    body.on('click', '.computy_skidka_link', function(e){
        e.stopPropagation();
        $('.computy_skidka_container').show('slow');
        $(this).addClass('show_skidka');
    });
    body.on('click', '.show_skidka', function(e){
         e.stopPropagation();
        $('.computy_skidka_container').hide('slow');
        $(this).removeClass('show_skidka');
    });
    /*Открытие закрытие использование бонусов в корзине*/

    /*Копирование реферальной ссылки*/
    body.on('click', '#copy_referal', function(){
        let $tmp = $("<input>");
        $("body").append($tmp);
        $tmp.val($('#code_referal').text()).select();
        document.execCommand("copy");
        $('#copy_good').text("Скопировано в буфер обмена") ;
        setTimeout(function() {
            $("#copy_good").text(" ").empty();
        }, 2000);
        $tmp.remove();
    });
    /*Копирование реферальной ссылки*/

    /*Копирование реферальной на странице товара*/
    body.on('click', '.copy_referal_single', function(){
     let text =  $(this).attr('data-link');

                let copytext2 = document.createElement('input');
                copytext2.value = text;
                document.body.appendChild(copytext2);
                copytext2.select();
                document.execCommand("copy");
                document.body.removeChild(copytext2);


        $(this).addClass('goodcopy');

        setTimeout (function(){
            $(this).removeClass('goodcopy');
        }.bind(this), 2000);

    });
    /*Копирование реферальной на странице товара*/

    $(document).on('submit','.computy_skidka_form', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: "/wp-admin/admin-ajax.php",
            data: {
                action: 'computy_trata_points',
                maxpoints: $(this).find('[name="maxpoints"]').val(),
                redirect: $(this).find('[name="redirect"]').val(),
                computy_input_points: $(this).find('[name="computy_input_points"]').val(),
            },
            success: function (data) {
                              document.location.href = data.data;

            },
            error: function (error) {
                console.log(error);

            }
        });
        return false;
    });

    /*Введение купона */
    $(document).on('submit','.take_coupon_form', function (e) {
        let form = $(this);
        $(this).addClass('loading_coupon');
        $(".message_coupon").text('');
        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: "/wp-admin/admin-ajax.php",
            data: {
                action: 'bfw_take_coupon_action',
                redirect: $(this).find('[name="redirect"]').val(),
                code_coupon: $(this).find('[name="code_coupon"]').val(),
            },
            success: function (data) {
                form.removeClass('loading_coupon');
                if(data.data.cod==='200'){
                    $(".message_coupon").text(data.data.message);
                    setTimeout(function () {
                        document.location.href = data.data.redirect;
                    }, 2000);

                }else{
                    $(".message_coupon").text(data.data.message);
                }

              //  message_coupon
            },
            error: function (error) {
                console.log(error);

            }
        });
        return false;
    });

    $(document).on('submit','.remove_points_form', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: "/wp-admin/admin-ajax.php",
            data: {
                action: 'clear_fast_bonus',
                redirect: $(this).find('[name="redirect"]').val(),
                computy_input_points: $(this).find('[name="computy_input_points"]').val(),
            },
            success: function (data) {
                window.location.href = data.data;
            },
            error: function (error) {
                console.log(error);

            }
        });
        return false;
    });


    $('.checkout_coupon').on('submit', function() {
        $('.remove_points').trigger('click');
    });
    body.on('click', '#bfw_remove_cart_point', function(){
        $('.remove_points').trigger('click');
    });

    body.on('click', '.woocommerce-remove-coupon', function(){
           $('#computy-bonus-message-cart').show();
    });

});

