<?php
/**
**** Разработка и продвижение сайтов *****
**** по вопросам доработки/разработки ****
**** MM76.RU *****************************
**** Tel: +7 920 650 76-76 ***************
**** WhatsApp: +7 920 650 76-76 **********
**** Tg: @mm76_ru ************************
**** Site: https://mm76.ru/ ***************
*/

add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * # Перевод
 */
add_filter('gettext', 'translate_str', 10, 3);
add_filter('ngettext', 'translate_str');
function translate_str($translated) {
    $translated = str_ireplace('SIGN UP', 'Зарегистрироваться', $translated);
    $translated = str_ireplace('This will be how your name will be displayed in the account section and in reviews', 'Именно так ваше имя будет отображаться в личном кабинете и в отзывах', $translated);
    $translated = str_ireplace('Your wishlist is currently empty.', 'Ваш список желаний в настоящее время пуст.', $translated);
    $translated = str_ireplace('Return To Shop', 'Вернуться в магазин', $translated);
    $translated = str_ireplace('Search Results for', 'Результаты поиска по сайту', $translated);
    $translated = str_ireplace('Share on', 'Поделиться', $translated);
    $translated = str_ireplace('Subtotal', 'Подытог', $translated);
    $translated = str_ireplace('Total', 'Итог', $translated);
    $translated = str_ireplace('Price', 'Цена', $translated);
    $translated = str_ireplace('Product', 'Продукт', $translated);
    $translated = str_ireplace('Stock status', 'Наличие', $translated);
    $translated = str_ireplace('In stock', 'В наличии', $translated);
    $translated = str_ireplace('Cashback: ', 'Твой кешбэк за эту покупку:', $translated);
    // $translated = str_ireplace('up to', ' до', $translated);
     return $translated;
}

add_action( 'woocommerce_cart_calculate_fees','shipping_method_discount', 20, 1 );
function shipping_method_discount( $cart_object ) {

    if ( is_admin() && ! defined( 'DOING_AJAX' ) || ! is_checkout() ) return;
    $payment_method = 'tinkoff';
    $percent = 10; // 10%
    $cart_total = $cart_object->subtotal_ex_tax;
    $chosen_payment_method = WC()->session->get('chosen_payment_method');

   if ( is_checkout() || defined('WOOCOMMERCE_CHECKOUT') ) {
      if( $payment_method == $chosen_payment_method ){
          $label_text = __( "Cкидка -10% при оплате онлайн" );
          // Считаем скидку  
          $discount = ($cart_total / 100) * $percent;
          // Добавляем скидку 
          $cart_object->add_fee( $label_text, -$discount, false );
      }
    }
  }

add_action( 'woocommerce_review_order_before_payment', 'refresh_payment_methods' );
function refresh_payment_methods(){
    ?>
      <script type="text/javascript">
          (function($){
              $( 'form.checkout' ).on( 'change', 'input[name^="payment_method"]', function() {
                  $('body').trigger('update_checkout');
              });
          })(jQuery);
      </script>
    <?php
}

/***
 * удалить краткое описание из админки 
 */

//  function remove_short_description() {
//     remove_meta_box( 'postexcerpt', 'product', 'normal');
// }
// add_action('add_meta_boxes', 'remove_short_description', 999);


/**
 * # Поменять название у кнопки 
 */
add_filter( 'woocommerce_product_add_to_cart_text', 'my_custom_cart_button_text', 10, 2 );
function my_custom_cart_button_text( $button_text, $product ) {

    if ( $product->is_type( 'variable' ) )
        $button_text = __('Купить', 'dazzling');
    return $button_text;
}

/**
 * # Добавляем для мобилки блок напишите нам в WhatsApp 
 */
add_action( 'woocommerce_share', 'share_action', 5 );
function share_action() {
  global $product;
  $title_product = $product->get_title();  

  if (wp_is_mobile()) {
    ?>
      <div class="share-product">
        <div class="share-product__whatsapp">
          <p>Нужна помощь? Напиши нам в WhatsApp</p>
          <a href="https://api.whatsapp.com/send/?phone=79693472910&text=Привет%2C+у+меня+есть+вопрос+по+товару+<?php echo $title_product ?>">
            <i class="fab fa-whatsapp"></i> +7-969-347-29-10
          </a>
        </div>
      </div>
    <?php 
  }else {
    echo '';
  }
}

/**
 * # Отключаем табы в карточке товара, будем использовать свои  
 */
add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 100 );
function woo_remove_product_tabs( $tabs ) {
   /** # Отключаем закладку Описание Elementor */  
   unset( $tabs['description'] );       
   /** # Отключаем закладку Отзывы */  
   unset( $tabs['reviews'] );
   /** # Отключаем закладку Дополнительная информация */               
   unset( $tabs['additional_information'] );

   return $tabs;
}

/***
 * Отключает похожие товары
 */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

/**
 * # Добавляем WishList к кнопке купить  
 */
add_action( 'woocommerce_before_add_to_cart_button', 'add_laz_wishlist', 10);
function add_laz_wishlist() {
   echo laz_wishlist(); 

}

/**
 * # WishList 
 */
function laz_wishlist(){
    global $product;
    /** # Получаем ID товара */
    $product_id = $product->get_id();  
    /** # Получаем название товара */
    $product_title = $product->get_title();  
    /** # Получаем слаг товара */
    $product_slug = $product->get_slug();  
    $product_atts = $product->get_attributes();
    /** # Иконка */
    $iheart   =  '<svg width="39" height="33" viewBox="0 0 39 33" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M28.4973 24.9746C24.222 28.7188 19.849 31.1518 19.25 31.4754C18.651 31.1518 14.278 28.7188 10.0027 24.9746C5.49194 21.0244 1.50042 15.9705 1.5 10.6571C1.50296 8.2294 2.46866 5.90198 4.18532 4.18532C5.90198 2.46866 8.22941 1.50296 10.6571 1.5C13.7676 1.50024 16.4164 2.83043 18.0505 5.00673L19.25 6.60432L20.4495 5.00673C22.0836 2.83043 24.7324 1.50024 27.8429 1.5C30.2706 1.50296 32.598 2.46866 34.3147 4.18532C36.0315 5.90217 36.9973 8.22994 37 10.6579C36.9992 15.971 33.0078 21.0246 28.4973 24.9746Z" stroke="#FF81C9" stroke-width="3"/>
    </svg>
    ';
    $iheart2   =  '<svg width="40" height="34" viewBox="0 0 40 34" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M39.25 11.1562C39.25 23.1875 21.4111 32.9259 20.6514 33.3281C20.4512 33.4358 20.2274 33.4922 20 33.4922C19.7726 33.4922 19.5488 33.4358 19.3486 33.3281C18.5889 32.9259 0.75 23.1875 0.75 11.1562C0.753184 8.33101 1.87692 5.62241 3.87466 3.62466C5.87241 1.62692 8.58101 0.503184 11.4062 0.5C14.9555 0.5 18.063 2.02625 20 4.60609C21.937 2.02625 25.0445 0.5 28.5938 0.5C31.419 0.503184 34.1276 1.62692 36.1253 3.62466C38.1231 5.62241 39.2468 8.33101 39.25 11.1562Z" fill="#FF81C9"/>
    </svg>
    ';  

    $wl = new Soo_Wishlist_List();

    if( $wl->in_wishlist( $product ) !== false ) {
        $wishlist = sprintf(
            '  
            <div class="wishlist-added">
             <a href="%1$s" 
             data-product_id="%2$s" 
             data-product_type="variable" 
             class="button laz-wishlist add-to-wishlist-button add-to-wishlist-%2$s"
             rel="nofollow">
               %3$s
             </a>
             <p>Добавлено в <a href="' . site_url('wishlist') . '">Избранное</a></p>
             </div>
            ',
            '/wishlist/',
            $product_id,
            $icon = $iheart2,
            // $wcwl_count_products =  soow_count_products() == 0 ? " ":  "<span class='show_count wishlist-count'>" .soow_count_products(). "</span>"  
        );
    } else {
        $wishlist = sprintf(
            '  
            <div class="wishlist-notadded">
             <a href="%1$s" 
             data-product_id="%2$s" 
             data-product_type="variable" 
             class="button laz-wishlist add-to-wishlist-button add-to-wishlist-%2$s"
             rel="nofollow">
               %3$s
             </a>
             <p>Добавить в <a href="' . site_url('wishlist') . '">Избранное</a></p>
             </div>
            ',
            $add_wishlist_url = '/shop/' . $product_slug . '/?add_to_wishlist=' . $product_id,
            $product_id,
            $icon = $iheart,
            // $wcwl_count_products =  soow_count_products() == 0 ? " ":  "<span class='show_count wishlist-count'>" .soow_count_products(). "</span>"  
        );
    }

    return $wishlist;
}

/**
 * # Карусель с фтографиями товара 
 */
/*remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
add_action( 'after_setup_theme', 'remove_wc_gallery_lightbox', 25 );
function remove_wc_gallery_lightbox() { 
    remove_theme_support( 'wc-product-gallery-lightbox' );
}

add_filter( 'woocommerce_single_product_image_thumbnail_html', 'remove_single_product_image', 10, 2 );
function remove_single_product_image( $html, $thumbnail_id ) {
    return '';
}*/

// add_action( 'woocommerce_product_thumbnails' , 'product_gallery', 20);
/*add_action( 'woocommerce_before_single_product_summary', 'product_gallery', 20 );
function product_gallery() {

    global $product;

    /**
     * # Получаем ID товара 
     */
    /*$product_id = $product->get_id();

    $woocommerce_placeholder_300x300 = get_stylesheet_directory_uri() . '/assets/img/woocommerce-placeholder-300x300.png';

    $attachment_ids = $product->get_gallery_image_ids();
    $allPhotoGallery = count($attachment_ids);
    $remainderPhotoGallery =   $allPhotoGallery - 4;
    $remainderPhotoGallery = $remainderPhotoGallery == 0 ? " " : '+' .$remainderPhotoGallery; 

    /**
     * # Вывод в галлерею главной фотографии 
     */
    /*$attachment_ids_url = wp_get_attachment_image_src( $attachment_ids[0], 'full' )[0];

    $thumbProduct =  $product->get_image_id();
    $attachment_id_url = wp_get_attachment_image_src( $thumbProduct, 'full' )[0];
    /** # Итого количество фотографи в галереи */

    /*?>
    <div class="product-gallery">
            <!-- Переход между фотографиями большой картинки  -->
            <div class="product-gallery__navs">
                <span class="arrow-prev  js-arrow-prev" data-nav="product-main-img-nav">
                    <i class="fas fa-chevron-left"></i>
                </span>                        
                <span class="arrow-next  js-arrow-next" data-nav="product-main-img-nav">
                    <i class="fas fa-chevron-right"></i>
                </span>
            </div>
            <div class="product-gallery__main-img js-show-lightbox-gallery" data-thumb-src="<?php echo $attachment_id_url ?>" data-id="<?php echo $product_id ?>" style="background-image: url(<?php echo $attachment_id_url ?>);"> 
                      <?php 
                          /** # Скидка в процентах */
                          /*get_percentage_sale( $product);
                       ?>
            </div>
            <div class="product-gallery__thumbnails-wrapper">
                <ul class="product-gallery__thumbnails-list">
                    <?php 
                        $arr_attachment_thumbnail_url = [];
                        $count = 0;
                        foreach( array_slice( $attachment_ids, 0, 20 ) as $attachment_id ) { 
                            $count++;
                            $thumbnail_url = wp_get_attachment_image_src( $attachment_id, 'full' )[0];

                            $get_thumbnail_url = "";
                            $get_thumbnail_url .= "<input class='attachment-thumbnail-url' type='hidden' value='$thumbnail_url' >";    
                            array_push($arr_attachment_thumbnail_url, $get_thumbnail_url );
                            if ($count<4) {
                                echo "<li class='thumbnail-item js-thumbnail-item'> <img  src='".$thumbnail_url."'></li>";
                            }
                            elseif($count==4) {
                                echo "<li class='thumbnail-item js-thumbnail-item'> <img  src='$thumbnail_url' > <span class='counter-all-item js-counter-all-item'>$remainderPhotoGallery</span> </li>";
                            }else {
                                echo "<li class='thumbnail-item js-thumbnail-item hidden'> <img  src='$thumbnail_url' ></li>";
                            }
                        }
                    ?>
                </ul>
            </div>
            <?php 
                /** # Получаем все src для галереи картинок товара */    
                /*foreach ($arr_attachment_thumbnail_url as $value) {
                    echo $value;
                }
            ?>
        </div>
    <?php 
}*/

/**
 * # Добавляем процент скидку в карточку товара на странице каталога 
 */
//add_filter('woocommerce_before_shop_loop_item_title', 'lw_custom_sales_badge');
//function lw_custom_sales_badge() {
//    global $product;
//    get_percentage_sale( $product);
//}

/**
* # Ставим свой значек скидка в процентах 
*/
/*function get_percentage_sale( $product) {
 global $product;
 $product_id =  $product->get_id();
  // && $product->product_type == 'simple'
  // && $product->product_type == 'variable'
  if ($product->is_on_sale()  ) {
         echo  is_product() ? '<div class="percentage percentage_is_product">' : '<div class="percentage">';
                 $available_variations = $product->get_available_variations();
                 $maximumper = 0;
                 for ($i = 0; $i < count($available_variations); ++$i) {
                         $variation_id=$available_variations[$i]['variation_id'];
                         $variable_product1= new WC_Product_Variation( $variation_id );
                         $regular_price = $variable_product1 ->get_regular_price();
                         $sales_price = $variable_product1 ->get_sale_price();
                         $percentage= round((( ( $regular_price - $sales_price ) / $regular_price ) * 100),1) ;
                     if ($percentage > $maximumper) {
                        $maximumper = round($percentage);
                     }
                 }
              $price = "";   
              echo  $price . sprintf( __('%s', 'woocommerce' ), '-'. $maximumper . '%' ); ?>
          </div>     
     <?php  
    }
}*/


/**
 * # Удалить купоны на странице чекаут 
 */
add_filter( 'woocommerce_coupons_enabled', 'hide_coupon_field_on_cart' );
function hide_coupon_field_on_cart( $enabled ) {
if ( is_checkout() ) {
    $enabled = false;
}
return $enabled;
}

/**
 * Remove Woocommerce Select2 - Woocommerce 3.2.1+
 */
function woo_dequeue_select2() {
    if ( class_exists( 'woocommerce' ) ) {
        wp_dequeue_style( 'select2' );
        wp_deregister_style( 'select2' );

        wp_dequeue_script( 'selectWoo');
        wp_deregister_script('selectWoo');
    } 
}
//add_action( 'wp_enqueue_scripts', 'woo_dequeue_select2', 100 );

/**
 * переименуем анкету WC
 */
add_filter( 'woocommerce_account_menu_items', 'custom_my_account_menu_items', 22, 1 );
function custom_my_account_menu_items( $items ) {
    $items['dashboard'] = 'Личный кабинет';
    $items['edit-account'] = 'Личная информация';
    return $items;
}
/**
 * уберем загрущки
 */
function my_accountmenu_removes( $items ) {
    unset($items['downloads']);
    //edit-address
    unset($items['edit-address']);
    return $items;
}
add_filter( 'woocommerce_account_menu_items', 'my_accountmenu_removes' );

/**
 * добавим в профиль список желаний
 */
add_filter ( 'woocommerce_account_menu_items', 'add_whish_in_profile', 25 );
function add_whish_in_profile( $menu_links ){
 
    $menu_links = array_slice( $menu_links, 0, 4, true ) + array( 'wishlist' => 'Список желаний' ) + array_slice( $menu_links, 4, NULL, true );
	return $menu_links;
 
}

/**
 * сообщение пустой корзины
 */
// remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );
// add_action( 'woocommerce_cart_is_empty', 'custom_empty_cart_message', 10 );
// function custom_empty_cart_message() {
//     $html  = '<div class="col-12 offset-md-1 col-md-10"><p class="cart-empty">';
//    echo '<img src="https://lazybirds.ru/wp-content/themes/konte/images/empty-bag.svg" width="150" alt="Корзина пуста">Твоя корзина пока пуста.';
//     echo $html . '</p></div>';
// }

remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );
add_action( 'woocommerce_cart_is_empty', 'custom_empty_cart_message', 10 );
function custom_empty_cart_message() {
    $html  = '<div class="empty-cart"><p>Твоя корзина пока пуста.</p></div>';
    echo $html;
}

add_filter( 'gettext', 'woocommerce_rename_coupon_field_on_cart', 10, 3 );
add_filter( 'gettext', 'woocommerce_rename_coupon_field_on_cart', 10, 3 );
// Меняем слово Купон на Промокод на странице корзины
function woocommerce_rename_coupon_field_on_cart( $translated_text, $text, $text_domain ) {
    // не меняет текст в админке
    if ( is_admin() || 'woocommerce' !== $text_domain ) {
        return $translated_text;
    }
    if ( 'Coupon:' === $text ) {
        $translated_text = 'Промокод / сертификат:';
    }
 
    if ('Coupon has been removed.' === $text){
        $translated_text = 'Промокод был удалён.';
    }
 
    if ( 'Apply coupon' === $text ) {
        $translated_text = 'ОК';
    }
 
    if ( 'Coupon code' === $text ) {
        $translated_text = 'Промокод';    
    } 
 
    return $translated_text;
}
 
// Меняем слово Купон на Промокод на странице заказа
add_filter( 'woocommerce_checkout_coupon_message', 'woocommerce_rename_coupon_message_on_checkout' );
function woocommerce_rename_coupon_message_on_checkout() {
    return 'Есть промокод?' . ' <a href="#" class="showcoupon">' . __( 'Нажмите, чтобы ввести промокод', 'woocommerce' ) . '</a>';
}
 
add_filter('woocommerce_coupon_error', 'rename_coupon_label', 10, 3);
add_filter('woocommerce_coupon_message', 'rename_coupon_label', 10, 3);
function rename_coupon_label($err, $err_code=null, $something=null){
 
    $err = str_ireplace("Coupon","Промокод ",$err);
 
    return $err;
}
 
// В деталях заказа изменяем слово купон на Промокод применён
add_filter( 'woocommerce_cart_totals_coupon_label', 'woocommerce_change_coupon_label',10, 3 );
function woocommerce_change_coupon_label() {
    echo 'Промокод применён';
}

add_filter('gettext', 'translate_text');
add_filter('ngettext', 'translate_text');
 
function translate_text($translated) {
$translated = str_ireplace('Подытог', 'Сумма', $translated);
$translated = str_ireplace('Возможно Вас также заинтересует&hellip;', 'Вместе с этим товаром покупают', $translated);
return $translated;
}