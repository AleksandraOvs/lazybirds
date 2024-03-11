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
    // $translated = str_ireplace('Cashback: ', 'Кэшбэк:', $translated);
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
    /** # Иконка */
    $iheart   =  get_stylesheet_directory_uri() . '/assets/img/svg/heart.svg';  
    $iheart2   =  get_stylesheet_directory_uri() . '/assets/img/svg/heart_fill.svg';  

    $wl = new Soo_Wishlist_List();

    if( $wl->in_wishlist( $product ) !== false ) {
        $wishlist = sprintf(
            '  
             <a href="%1$s" 
             data-product_id="%2$s" 
             data-product_type="variable" 
             class="button laz-wishlist add-to-wishlist-button add-to-wishlist-%2$s"
             rel="nofollow">
               %3$s
             </a>
            ',
            '/wishlist/',
            $product_id,
            $icon ="<img width=30 src='$iheart2'>",
            // $wcwl_count_products =  soow_count_products() == 0 ? " ":  "<span class='show_count wishlist-count'>" .soow_count_products(). "</span>"  
        );
    } else {
        $wishlist = sprintf(
            '  
             <a href="%1$s" 
             data-product_id="%2$s" 
             data-product_type="variable" 
             class="button laz-wishlist add-to-wishlist-button add-to-wishlist-%2$s"
             rel="nofollow">
               %3$s
             </a>
            ',
            $add_wishlist_url = '/shop/' . $product_slug . '/?add_to_wishlist=' . $product_id,
            $product_id,
            $icon ="<img width=30 src='$iheart'>",
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

/**
 * # Новые вкладки табы 
 */
add_action( 'woocommerce_after_single_product_summary', 'woo_product_tabs_content', 10  );
function woo_product_tabs_content() {
    global $product;
    global $post;   

    /** # Получаем ID товара */
    $product_id = $product->get_id();  
    $product_tabs = [
        'tab-description' => 'Описание',
        'tab-product-care'=>'Уход',
        // 'tab-product-reviews'=>'Отзывы',
    ];

    $comments_number =  get_comments_number('0', '1', '%'); 

    $out_product_tabs = "";
    $out_product_tabs .= "<ul class='product-tabs' role='tablist'>";
    $count = 0;
    foreach ($product_tabs as $tab ) {
         $count++;

         if ($count == 1) {
            $out_product_tabs .= "<li class='product-tab is-active' aria-controls='tab-$count'>$tab</li>";
         }
         elseif($count == 2) {
            $out_product_tabs .= "<li class='product-tab' aria-controls='tab-$count'>$tab</li>";
         }
         // elseif($count == 3) {
         //    $out_product_tabs .= "<li class='product-tab' id='tab-review' aria-controls='tab-$count'>$tab ($comments_number)</li>";
         // }
    }
    $out_product_tabs .= "</ul>";

    /** # Вкладка  — Описание товара (Элементор) */
    $description_content  = apply_filters( 'the_content', get_the_content() );

    /** # Вкладка  — Уход */
    //$product_care   =  wpautop(carbon_get_the_post_meta('cbf_product_care'));
    $product_care = get_field('uhod', $product_id);

    /** # Вкладка отзывы */
    //$reviews =  add_product_reviews($product_id);

    $product_tabs_content = [
        'tab-content-description' => $description_content,
        'tab-content-product-care' => $product_care,
        // 'tab-content-product-reviews' => $reviews,
    ];            
    $out_product_tabs_content = "";
    $out_product_tabs_content .= "<div class='product-tabs-content '>";

    $count=0;
    if ($product_tabs_content) {
        
        foreach ($product_tabs_content as $key => $tab_content) {
            $count++;
            if ($count == 1){
                $out_product_tabs_content .= "<div class='product-tab-entry-content is-active $key' id='$key' aria-controls='tab-$count'> $tab_content </div>";
            }elseif($count == 2){
                $out_product_tabs_content .= "<div class='product-tab-entry-content $key' id='$key' aria-controls='tab-$count'> $tab_content </div>";
            }
            // elseif($count == 3){
            //     $out_product_tabs_content .= "<div class='product-tab-entry-content $key' id='$key' aria-controls='tab-$count'> $tab_content </div>";
            // }                        
        }
    }

    $out_product_tabs_content .= "</div>";
    echo '<div class="product-content wrapper">';
    echo $out_product_tabs;
    echo $out_product_tabs_content;
    comment_form();
    echo '</div>';
};

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
    $html  = '<div class="col-12 offset-md-1 col-md-10"><p class="cart-empty">Твоя корзина пока пуста.</p></div>';
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