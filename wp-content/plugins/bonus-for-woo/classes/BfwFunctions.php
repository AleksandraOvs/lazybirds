<?php
/**
* Class functions
 * Класс с различными функциями, которые не подходят к другим классам
 * @version 5.5.0
 *
*/

class BfwFunctions
{



    /** Находим используемые баллы в заказе
     * @param $order
     * @return float
     */
    public static function feeOrCoupon($order): float
    {
        $val = get_option('bonus_option_name');
        $fee_total=0;
        if (!empty($val['fee-or-coupon'])) {
            /* если бонусы с помощью купонов  */
            foreach( $order->get_coupon_codes() as $coupon_code  ) {
                $coupon = new WC_Coupon($coupon_code);
                $get_code = $coupon->get_code();
                $cart_discount =  mb_strtolower($val['bonus-points-on-cart']);
                if(strtolower($get_code)=== strtolower($cart_discount)){
                    $fee_total = $order->get_discount_total();
                }
            }

        }else{
            /*если бонусы с помощью комиссий*/
            foreach( $order->get_items('fee') as $item_id => $item_fee ){
                // The fee name
                $val = get_option( 'bonus_option_name' );
                if( $item_fee->get_name()== $val['bonus-points-on-cart']){
                    $fee_total = $item_fee->get_total();
                }
            }
        }

        return absint($fee_total); /*модуль*/
    }



    /** Добавляем баллы рефереру (пригласителю, спонсору)
     * @param $customer_user int id клиента
     * @param $percent_for_referral int Процент, который получит реферал
     * @param $total float Общая сумма заказа
     * @param $computy_point_fast float Баллы, которые использует покупатель
     * @param $get_referral_invite int Реферер клиента
     * @return void
     */
    public static function addReferralPoints(int $customer_user, int $percent_for_referral, float $total, float $computy_point_fast, int $get_referral_invite){

        $referral_point_new = ($total - $computy_point_fast) * ($percent_for_referral / 100);
        $referral_point_new = (new BfwPoints())->roundPoints($referral_point_new);
        /*узнаем баллы реферера*/
        $old_point_referral = (new BfwPoints)->getPoints($get_referral_invite);

        $referral_points = $old_point_referral + $referral_point_new;
        if((int)$referral_points!=0){
            $pricinaref = __('Points for referral', 'bonus-for-woo');

            /*шаблонизатор письма*/
            $text_email = $val['email-when-order-change-referal-text'] ?? '';

            $title_email = $val['email-when-order-change-referal-title'] ?? __('Points accrual', 'bonus-for-woo');
            $user = get_userdata($get_referral_invite);
            $get_referral = get_user_meta($get_referral_invite, 'bfw_points_referral', true);
            $text_email_array = array(
                '[referral-link]' => esc_url(site_url() . '?bfwkey=' . $get_referral),
                '[user]' => $user->display_name,
                '[cause]' => $pricinaref,
                '[points]' => $referral_point_new,
                '[total]' => $referral_points
            );
            $message_email = (new BfwEmail)->template($text_email, $text_email_array);
            /*шаблонизатор письма*/

            //если галочка стоит только первый заказ, то проверка является ли заказ первым
            if (!empty($val['first-order-referal'])) {
                //проверка является ли заказ первым
                $numorders = wc_get_customer_order_count($customer_user);
                if ($numorders == 1) {
                    (new BfwHistory)->add_history($get_referral_invite, '+', $referral_point_new, '0', $pricinaref);
                    $val = get_option('bonus_option_name');
                    if (!empty($val['email-when-order-change'])) {
                        (new BfwEmail)->getMail($get_referral_invite, '', $title_email, $message_email);
                    }
                    /*email*/
                    (new BfwPoints)->updatePoints($get_referral_invite, $referral_points); //добавляем баллы инвайту
                }
            } else {

                (new BfwHistory)->add_history($get_referral_invite, '+', $referral_point_new, '0', $pricinaref, $customer_user );
                /*email*/
                $val = get_option('bonus_option_name');
                if (!empty($val['email-when-order-change'])) {
                    (new BfwEmail)->getMail($get_referral_invite, '', $title_email, $message_email);
                }
                /*Начислить баллы рефереру*/
                (new BfwPoints)->updatePoints($get_referral_invite, $referral_points);

            }
        }
    }


    /**
     * Вывод списанных баллов в редактировании заказа админом
     * @param $order_id
     * @return void
     */
    public static  function bfw_inadminzakaz($order_id ): void
    {
        $order = wc_get_order($order_id);
        foreach( $order->get_items('fee') as $item_id => $item_fee ){
            $val = get_option( 'bonus_option_name' );

            if( $item_fee->get_name()== $val['bonus-points-on-cart']){
                $fee_total = $item_fee->get_total();
            }
        }
        if(isset($fee_total)){
            $val = get_option( 'bonus_option_name' );
            echo ' <tr><td class="label" >'.$val['bonus-points-on-cart'].':</td><td width="1%"></td>
		<td class="total">
		 <span class="woocommerce-Price-amount amount"><bdi>'.$fee_total.' <span class="woocommerce-Price-currencySymbol">'.  get_woocommerce_currency_symbol().'</span></bdi></span>				</td>
			</tr>';
        }
    }



    /**
     * Переводы
     * @return void
     */
    public static function lang_load_bonus_for_woo(): void
    {
        load_plugin_textdomain( 'bonus-for-woo', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
    }


    /**
     * Копирайт computy.ru
     * @return void
     */
    public static function computy_copyright(): void
    {if ( !(new BfwRoles)->is_pro()) {
        ?>
        <div class="computy_copyright"><?php echo __('With the support of', 'bonus-for-woo'); ?> <a href="https://computy.ru" target="_blank" title="Разработка на WordPress"> computy </a> </div>
        <?php
    }
    }






}