<?php
/*Списание баллов в корзине и оформлении заказа
 *
 * @param $redirect
 * @version 5.3.3
 */
function bfw_return_spisanie($redirect): void
{
    $val = get_option('bonus_option_name');






      if (!empty($val['exclude-fees-coupons'])){
        /*Сумма товаров в корзине без учета купонов и скидок*/
          $items =  WC()->cart->get_cart();
          $total=0;
          foreach($items as $item => $values) {
              $price = get_post_meta($values['product_id'] , '_price', true)*$values['quantity'];
              $total += $price;
          }

    }else{
        $total =  WC()->cart->total;//сумма в корзине

        /*Убираем доставку из общей суммы*/
        if (WC()->cart->shipping_total>0){
            $total = $total - WC()->cart->shipping_total;
        }

          /*Убираем налог на доставку из общей суммы*/
          if(WC()->cart->shipping_tax_total>0){
              $total = $total- WC()->cart->shipping_tax_total;
          }

          /*Убираем левые комиссии из общей суммы*/
          $fees = WC()->cart->get_fees();
          foreach ($fees as $fee) {
              $name = $fee->name;
              $amount = $fee->amount;
              if( $name != $val['bonus-points-on-cart']){
                  $total = $total-$amount;
              }
          }

    }





    $alternative ='';

    $computy_point = (new BfwPoints)->getPoints(get_current_user_id()); //всего баллов у покупателя


    /*Процент списание баллов для про*/
      if ((new BfwRoles)->is_pro()) {
        $max_percent = $val['max-percent-bonuses'] ?? 100;
        $max_percent = apply_filters( 'max-percent-bonuses-filter', $max_percent,$total );
    } else {
        $max_percent = 100;
    }
    /*Процент списание баллов для про*/


    $displaynone = '';

    /*Исключение категорий */
    if ((new BfwRoles)->is_pro()) {
        $categoriexs = $val['exclude-category-cashback'] ??  'not';

        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
            if (has_term($categoriexs, 'product_cat', $cart_item['product_id'])) {
                $sum_exclude_cat = $_product->get_price() * $cart_item['quantity'];
                $total = $total - $sum_exclude_cat;
            }
        }


    /*Исключение категорий*/


    /*Исключение товаров*/

        $exclude_tovar = $val['exclude-tovar-cashback'] ?? '';
        $tovars = apply_filters('bfw-excluded-products-filter', explode(",", $exclude_tovar), $exclude_tovar);
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
            if (in_array($cart_item['product_id'], $tovars)) {
                if (!has_term($categoriexs, 'product_cat', $cart_item['product_id'])) {
                    $sum_exclude_tov = $_product->get_price() * $cart_item['quantity'];
                    $total = $total - $sum_exclude_tov;
                }
            }
        }
    }
    /*Исключение товаров*/



    $i = 0;
    $s = 0;
    foreach (WC()->cart->get_cart() as $item):
        $id_tovara_vkorzine = $item['product_id'];
        $s++;
        $product = wc_get_product($id_tovara_vkorzine);
        if ($product->is_on_sale()) {
            $i++;
            if (!empty($val['spisanie-onsale'])){
                /*Исключаем возможность тратить кешбэк на товары со скидкой */
                $sum_exclud_sale = $item['data']->get_price() * $item['quantity'];
                $total = $total - $sum_exclud_sale;
            }

        }
    endforeach;
    if ($i == $s) { /*Если все товары в корзине со скидкой*/

        if (!empty($val['spisanie-onsale'])) {/*Если не разрешено списывать баллы у распродажи*/
            $displaynone = 'style="display:none"';

        }

    }



    $user_fast_points = (new BfwPoints)->getFastPoints(get_current_user_id());

    $total_plus_fast = $total+$user_fast_points;


    $total_max_percent = $total_plus_fast * $max_percent / 100;
    $total_max_percent =  (new BfwPoints())->roundPoints($total_max_percent);/*округляем если надо*/

    $computy_point = (new BfwPoints())->roundPoints($computy_point);

    if($total_max_percent>$computy_point){
        $total_max_percent=$computy_point;
    }

     $vozmojniy_ball_true = $total_max_percent-$user_fast_points;

    if($vozmojniy_ball_true<$user_fast_points){
        $vozmojniy_ball_true=$user_fast_points;
    }
if($vozmojniy_ball_true<0){
    $vozmojniy_ball_true = $total_max_percent;
}


/*Высчитывание минимальной суммы заказа*/
    $minimal_amount = 100000;
    if(!empty($val['minimal-amount'])){
        if($val['minimal-amount']>0){
            $minimal_amount = $total-$val['minimal-amount'];
            if($minimal_amount<0){
                $minimal_amount=0;
            }
        }
    }


        if ($computy_point > 0) {





            /*если есть другие комиссии, то вычесть их из возможных баллов*/


            if($user_fast_points>0){
                $vozmojniy_ball_true = $vozmojniy_ball_true+$user_fast_points;
                $vozmojniy_ball_true = (new BfwPoints())->roundPoints($vozmojniy_ball_true);
              if($total<$vozmojniy_ball_true){$total=$vozmojniy_ball_true;}
            }


          $vozmojniy_ball = min($computy_point, $vozmojniy_ball_true,$total,$minimal_amount,$total_max_percent);
            $vozmojniy_ball =  (new BfwPoints())->roundPoints($vozmojniy_ball);/*округляем если надо*/




if($vozmojniy_ball==0){ $displaynone = 'style="display:none"';}



            echo '<div '.$displaynone.' class="woocommerce-cart-notice woocommerce-cart-notice-minimum-amount woocommerce-info bfw-how-match-cashback">'.
sprintf( __('For this order, you can spend %s of %s %s', 'bonus-for-woo'), $vozmojniy_ball,$computy_point, (new BfwPoints())->pointsLabel(5)).'</div>';



            if(!empty($val['balls-and-coupon'])){
                    /*Если применяется купон*/

                    if(WC()->cart->applied_coupons){

                        $cart_discount =  mb_strtolower($val['bonus-points-on-cart']);

                        if (!empty($val['fee-or-coupon'])){
                            //если система с помощью купонов
                            if(in_array($cart_discount, WC()->cart->get_applied_coupons()) AND count(WC()->cart->get_applied_coupons())>1) {

                                $displaynone = 'style="display:none"';
                                $alternative = '<div class="woocommerce-cart-notice woocommerce-cart-notice-minimum-amount woocommerce-error">'.
                                    sprintf( __('To use %s, you must remove the coupon.', 'bonus-for-woo'), (new BfwPoints())->pointsLabel(5)).'</div> ';
                                /*Тут  очистить fastballs*/

                                foreach ( WC()->cart->get_applied_coupons() as $code ) {
                                    $coupon = new WC_Coupon( $code );
                                    if ( strtolower( $code ) === $cart_discount ) {
                                        WC()->cart->remove_coupon( $code );
                                    }
                                }


                                (new BfwPoints)->updateFastPoints(get_current_user_id(),0);
                                WC()->cart->calculate_totals();//Пересчет общей суммы заказа
                            }
                        }else{
                            $displaynone = 'style="display:none"';
                            $alternative = '<div class="woocommerce-cart-notice woocommerce-cart-notice-minimum-amount woocommerce-error">'.
                                sprintf( __('To use %s, you must remove the coupon.', 'bonus-for-woo'), (new BfwPoints())->pointsLabel(5)).'</div> ';
                            //если система с помощью fee
                            (new BfwPoints)->updateFastPoints(get_current_user_id(),0);
                            WC()->cart->calculate_totals();//Пересчет общей суммы заказа
                        }

                    }
            }



            $userid = get_current_user_id();
            if(isset($val['minimal-amount'])){
                if($total<$val['minimal-amount']){
                    $displaynone = 'style="display:none"';
                    $alternative ='';
                    if($vozmojniy_ball>0){
                        $alternative = '<div class="woocommerce-cart-notice woocommerce-cart-notice-minimum-amount woocommerce-error">'.
                            sprintf(__('To use %s, the order amount must be more than', 'bonus-for-woo'), (new BfwPoints())->pointsLabel(5)).' '.$val['minimal-amount'].' '.get_woocommerce_currency_symbol();
                        if ((new BfwPoints)->getFastPoints(get_current_user_id())  > 0) {
                            $alternative .=  '<form class="remove_points_form" action="' . admin_url("admin-post.php") . '" method="post">
                      <input type="hidden" name="action" value="clear_bonus" />';
                            $alternative .=  '<input type="hidden" name="redirect" value="' . $redirect . '">';
                            $alternative .=  '<input type="submit" class="remove_points"  value="' . $val['remove-on-cart'] . '"> </form>';
                        }
                        $alternative .=  '</div>';
                    }



                    $cart_discount =  mb_strtolower($val['bonus-points-on-cart']);
                    foreach ( WC()->cart->get_applied_coupons() as $code ) {
                        if ( strtolower( $code ) === mb_strtolower( $cart_discount ) ) {
                            WC()->cart->remove_coupon( $code );
                        }
                    }


                    (new BfwPoints)->updateFastPoints(get_current_user_id(),0);
                    WC()->cart->calculate_totals();//Пересчет общей суммы заказа
                }}

            echo $alternative;
            $bonustext_in_cart1 = $val['bonustext-in-cart1'] ?? __( 'Use', 'bonus-for-woo' );
            $bonustext_in_cart2 =  sprintf(__('%s to get a discount', 'bonus-for-woo'), (new BfwPoints())->pointsLabel($vozmojniy_ball));
            $bonustext_in_cart3 = $val['bonustext-in-cart3'] ?? __( ' for this order!', 'bonus-for-woo' );
            $bonustext_in_cart4 = $val['bonustext-in-cart4'] ?? __( 'Use points', 'bonus-for-woo' );

if($vozmojniy_ball<=0){  $displaynone = 'style="display:none"';}

            echo '<div ' . $displaynone . ' id="computy-bonus-message-cart" class="woocommerce-cart-notice woocommerce-cart-notice-minimum-amount woocommerce-info">
' . $bonustext_in_cart1 . ' <b>' . $vozmojniy_ball . '</b> ' . $bonustext_in_cart2 . ' <b>' . $vozmojniy_ball . ' ' . get_woocommerce_currency_symbol() . '</b> ' . $bonustext_in_cart3 . '
 <span class="computy_skidka_link">' . $bonustext_in_cart4 . '</span> <br> ';


            echo '
        <div class="computy_skidka_container" style="display: none;">
        <br>
        <form class="computy_skidka_form" action="' . admin_url("admin-post.php") . '" method="post">
         <input type="hidden" name="action" value="computy_trata_points" />';
            echo '<input type="hidden" name="maxpoints" value="' . $vozmojniy_ball . '"> ';
            echo '<input type="hidden" name="redirect" value="' . $redirect . '">';

            echo ' <p class="form-row form-row-first">';

            $computy_point_old = (new BfwPoints)->getFastPoints($userid);
            if ($computy_point_old > 0) {
                $usepointsoncart = $computy_point_old;
            } else {
                $usepointsoncart = $vozmojniy_ball;
            }
            echo '
         <input type="text" name="computy_input_points" class="input-text"   value="' . $usepointsoncart . '">
          
            </p>
          <p class="form-row form-row-last">';
            echo ' <input type="submit" class="button"  value="' . $val['use-points-on-cart'] . '">
            </p></form></div>';
            if ((new BfwPoints)->getFastPoints(get_current_user_id())  > 0) {
                echo '<form class="remove_points_form" action="' . admin_url("admin-post.php") . '" method="post">
                      <input type="hidden" name="action" value="clear_bonus" />';
                echo '<input type="hidden" name="redirect" value="' . $redirect . '">';
                echo '<input type="submit" class="remove_points"  value="' . $val['remove-on-cart'] . '">
 
</form>';
            }
            echo '</div> ';

        }


}



add_shortcode('bfw-write-off-bonuses', 'bfwoo_spisaniebonusov_in_cart');

add_action('woocommerce_before_cart', 'bfwoo_spisaniebonusov_in_cart', 9);
function bfwoo_spisaniebonusov_in_cart(): void
{
    if ( !current_user_can( 'manage_options' ) )  {
        $redirect=wc_get_cart_url();
        bfw_return_spisanie($redirect);
    }

}

add_action('woocommerce_before_checkout_form', 'bfwoo_spisaniebonusov_in_checkout', 9);
function bfwoo_spisaniebonusov_in_checkout(): void
{
    $val = get_option('bonus_option_name');
    if (!empty($val['spisanie-in-checkout'])) {
        $redirect=wc_get_checkout_url();
        bfw_return_spisanie($redirect);
    }
}