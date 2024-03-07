<?php
/**
 * Действия при изменении статуса заказа
 *
 * @version 5.5.0
 *
 */

$val = get_option('bonus_option_name');
/*------------Действие когда клиент подтверждает заказ - списание баллов------------*/

$order_status_write =  $val['write_points_order_status'] ?? 'processed';
if($order_status_write=='processed'){
    $order_status_write_action =  'woocommerce_checkout_order_processed';
}else{
    $order_status_write_action =  'woocommerce_order_status_'.$order_status_write;
}
add_action($order_status_write_action, 'new_order_bfw', 20, 1);
function new_order_bfw($order_id): void
{
    $order = wc_get_order($order_id);//Вызов информации о заказе
    $customer_user = $order->get_customer_id();//узнаем id покупателя

    //Узнаем баллы клиента
    $computy_point_old = (new BfwPoints)->getPoints($customer_user);
    //Узнаем баллы которые он решил списать
    $computy_point_fast = (new BfwPoints)->getFastPoints($customer_user);
    if ($computy_point_fast > 0) {
        $count_point = $computy_point_old - $computy_point_fast;
        (new BfwPoints)->updatePoints($customer_user,$count_point);//Удаляем баллы со счета клиента
    }
    (new BfwPoints)->updateFastPoints($customer_user,0);//Очищаем добавленную скидку

    if ($computy_point_fast > 0) {
        /*Запись в историю*/
        $prichina = sprintf(__('Use of %s', 'bonus-for-woo'), (new BfwPoints())->pointsLabel(5));
        (new BfwHistory)->add_history($customer_user,'-',$computy_point_fast,$order_id,$prichina);
        /*Запись в историю*/
        /*email*/
        $val = get_option( 'bonus_option_name' );
        $title_email = $val['email-when-order-confirm-title'] ?? __('Writing off bonus points', 'bonus-for-woo');

        /*Шаблонизатор письма*/
        $text_email = $val['email-when-order-confirm-text'] ?? '';
        $user = get_userdata($customer_user);
        $get_referral        = get_user_meta($customer_user, 'bfw_points_referral', true );
        $text_email_array = array(
            '[user]'=>$user->display_name,
            '[order]'=>$order_id,
            '[points]'=>$computy_point_fast,
            '[total]'=>$count_point,
            '[cause]'=>$prichina,
            '[referral-link]'=> esc_url(site_url().'?bfwkey='. $get_referral));
        $message_email = (new BfwEmail)->template($text_email,$text_email_array);
        /*Шаблонизатор письма*/

        if(!empty($val['email-when-order-confirm'])){
            (new BfwEmail)->getMail($customer_user, '', $title_email, $message_email);
        }
        /*email*/
    }
    //списание временных баллов
}


/*------------Действие когда статус заказа выполнен - начисление баллов------------*/
$order_status = $val['add_points_order_status'] ?? 'completed';

add_action('woocommerce_order_status_'.$order_status, 'add_computy_points', 10, 1);
function add_computy_points($order_id)
{

/*вызов информации о заказе*/
$order = wc_get_order($order_id);

/*узнаем id покупателя*/
$customer_user = $order->get_customer_id();

 if($customer_user!=0){ /*Проверяем зарегистрирован ли клиент*/

/*сумма покупок данного заказа*/
$total = (float)$order->get_total();

/*---------Если есть частичный возврат, то вычесть его с общей суммы---------*/
     if($order->get_refunds()){
         if($order->get_refunds()[0]->get_amount()){
             $total = $order->get_total() - $order->get_refunds()[0]->get_amount();
         }
     }
/*---------Если есть частичный возврат, то вычесть его с общей суммы---------*/


/*---------Находим товары и категории, которые надо исключить---------*/
if ((new BfwRoles)->is_pro()) {
    $val = get_option('bonus_option_name');
    /*находим метод оплаты, который надо исключить из системы*/
 $pm = $order->get_payment_method(); //выводит id метода оплаты!!!
    if (!empty($val['exclude-payment-method'])){
        if (in_array($pm, $val['exclude-payment-method'])){
            return false;
        }
    }
    /*находим метод оплаты, который надо исключить из системы*/
$order_items = $order->get_items();

    if (!empty($val['cashback-for-shipping'])){
        /*убираем сумму доставки с кешбэка*/
        /*TODO исправить работу плагина доставки СДЭК, Почта РФ, Озон логистик*/

        $shipping_total = $order->get_shipping_total();
        $total = $total - $shipping_total;
    }



$addcashbckexclude = $val['addkeshback-exclude'] ?? 0;

if ($addcashbckexclude != '1') {

$categoriexs = $val['exclude-category-cashback'] ?? 'not';

$exclude_tovar = $val['exclude-tovar-cashback'];

 $tovars = apply_filters('bfw-excluded-products-filter', explode(",", $exclude_tovar), $exclude_tovar);


foreach ($order_items as $item_id => $item) {

$product_id = $item->get_product_id();
$item_data = $item->get_data();



if (in_array($product_id, $tovars)) {
if (!has_term($categoriexs, 'product_cat', $product_id)) {
$sum_exclud_tov = $item_data['subtotal'];
$total = $total - $sum_exclud_tov;
}
}

if (has_term($categoriexs, 'product_cat', $product_id)) {
$sum_exclud_cat = $item_data['subtotal'];
$total = $total - $sum_exclud_cat;
}
}
}


}/*pro*/
/*---------Находим товары и категории, которые надо исключить---------*/


    /*узнаем баллы клиента*/
$computy_point_old = (new BfwPoints)->getPoints($customer_user);

   if((new BfwRoles)->is_invalve($customer_user)){/*Если участвует в бонусной системе*/

        /*минимальная сумма заказа*/
        if ((new BfwRoles)->is_pro()){
            if(isset($val['minimal-amount'])){
                if($total<$val['minimal-amount']){
                    if(!empty($val['minimal-amount-cashback'])){
                       return false;
                    }
                }}
        }
        /*минимальная сумма заказа*/


        (new BfwRoles)->updateRole($customer_user); //обновляем роль
        $percent = (new BfwRoles)->getRole($customer_user)['percent'];//находим процент


        /*-----добавляем бонусные баллы на счет клиента-----*/

        /*узнаем баллы, которые решил списать клиент*/
        $computy_point_fast = (new BfwPoints)->getFastPoints($customer_user);

        $computy_point_new = ($total - $computy_point_fast) * ($percent / 100);
        $computy_point_new = (new BfwPoints())->roundPoints($computy_point_new);

       $computy_point_new = apply_filters( 'bfw-completed-points', $computy_point_new, $order_id, $order );

       $count_point = $computy_point_old +  $computy_point_new;
       $count_point = (new BfwPoints())->roundPoints($count_point); //округляем если надо


       if ($computy_point_fast > 0) {
           $count_point = $count_point - $computy_point_fast;
        }


       /*Находим используемые баллы в заказе*/
       $fee_total = (new BfwFunctions)->feeOrCoupon($order);




       if(isset($val['yous_balls_no_cashback']) AND $fee_total > 0){
           //если используются баллы - кешбэк не дается.
              }else{
           /*запись в историю*/
                  $reason = __('Points accrual', 'bonus-for-woo');
                  (new BfwHistory)->add_history($customer_user,'+',$computy_point_new,$order_id,$reason);

                  /*email*/
                  $val = get_option( 'bonus_option_name' );
                  $text_email = $val['email-when-order-change-text'] ?? '';
                  /*шаблонизатор письма*/
                  $title_email = $val['email-when-order-change-title'] ?? __('Points accrual', 'bonus-for-woo');
                  $user = get_userdata($customer_user);
                  $get_referral = get_user_meta($customer_user, 'bfw_points_referral', true );
                  $text_email_array = array('[user]'=>$user->display_name,'[order]'=>$order_id,'[points]'=>$computy_point_new,'[total]'=>$count_point, '[referral-link]'=> esc_url(site_url().'?bfwkey='. $get_referral));
                  $message_email = (new BfwEmail)->template($text_email,$text_email_array);
                  /*шаблонизатор письма*/

                  if(!empty($val['email-when-order-change'])){
                      (new BfwEmail)->getMail($customer_user, '', $title_email, $message_email);
                  }
                  /*email*/

                  (new BfwPoints)->updatePoints($customer_user,$count_point);//добавляем баллы клиенту
                   }

        (new BfwPoints)->updateFastPoints($customer_user,0);//очищаем добавленную скидку


        $referalwork = isset( $val['referal-system'] ) ? intval( $val['referal-system'] ) : 0;
        /*если включена реферальная система*/
        if($referalwork) {

            //узнать если у пользователя инвайт
            //bog<-invaite<-client
            $get_referral_invite = get_user_meta($customer_user, 'bfw_points_referral_invite', true);
            $get_referral_invite = (int)$get_referral_invite;
            if ($get_referral_invite != 0) {

                $sumordersforreferral = $val['sum-orders-for-referral'] ?? 0.0;
                $totalref = (new BfwPoints)->getSumUserOrders($get_referral_invite);
                if ($totalref >= $sumordersforreferral) {

                /*процент от приглашенного первого уровня*/
                $percent_for_referal = $val['referal-cashback'];

            /*Добавляем баллы рефереру (пригласителю, спонсору) от реферала первого уровня*/
           (new BfwFunctions)->addReferralPoints($customer_user,$percent_for_referal,$total,$computy_point_fast, $get_referral_invite);

                }


                /*Начисляем баллы от реферала второго уровня*/
                if (!empty($val['level-two-referral'])) {
                    $get_referral_invite_two_level = get_user_meta($get_referral_invite, 'bfw_points_referral_invite', true);
                    $get_referral_invite_two_level = (int)$get_referral_invite_two_level;
                    if($get_referral_invite_two_level != 0){

                        $sumordersforreferral2 = $val['sum-orders-for-referral'] ?? 0.0;
                        $totalref2 = (new BfwPoints)->getSumUserOrders($get_referral_invite_two_level);
                        if ($totalref2 >= $sumordersforreferral2) {

                        $percent_for_referal_two_level = $val['referal-cashback-two-level'];

  /*Добавляем баллы рефереру (пригласителю, спонсору) от реферала второго уровня*/
    (new BfwFunctions)->addReferralPoints($customer_user,$percent_for_referal_two_level,$total,$computy_point_fast, $get_referral_invite_two_level);
                            }
                        }
                    }
                }
                }

        }

       }



}

/*------------Действие когда оформлен возврат баллов-----------*/
$order_status_refunded = $val['refunded_points_order_status'] ?? 'refunded';
add_action('woocommerce_order_status_'.$order_status_refunded, 'refunded_computy_points', 10, 1);
function refunded_computy_points($order_id): void
{
    $order = wc_get_order($order_id);

    /*узнаем id покупателя*/
    $customer_user = $order->get_customer_id();
    /*узнаем баллы клиента*/
    $computy_point_old = (new BfwPoints)->getPoints($customer_user);


    /*Находим используемые баллы в заказе*/
    $fee_total = (new BfwFunctions)->feeOrCoupon($order);

    $count_point = $computy_point_old+$fee_total;

    /*запись в историю*/
    $pricina = __('Refund of bonus points', 'bonus-for-woo');

    global $wpdb;
    $getplusball = $wpdb->get_var('SELECT points FROM ' . $wpdb->prefix . 'bfw_history_computy WHERE user = "' . $customer_user . '" AND symbol="+" AND orderz = "' .$order_id . '" ');
    $info_email = '';
    if(!empty($getplusball)){
        $getplusball = (new BfwPoints())->roundPoints($getplusball);
    }else{
        $getplusball=0;
    }

    if($getplusball>0){
       /*убираем заработанные баллы*/
        (new BfwHistory)->add_history($customer_user,'-',$getplusball,$order_id,$pricina);
        $count_point = $count_point-$getplusball;
        $info_email .= sprintf(__(' The %s bonus points you earned for order no. %s have been canceled.', 'bonus-for-woo'), $getplusball,$order_id);
   }

   if($fee_total>0){
       /*Добавляем списанные баллы*/
       (new BfwHistory)->add_history($customer_user,'+',$fee_total,$order_id,$pricina);
       $info_email .= sprintf(__(' You have returned %s bonus points for order number %s.', 'bonus-for-woo'), $fee_total,$order_id);
   }
    (new BfwPoints)->updatePoints($customer_user,$count_point);//Обновляем баллы клиенту
    (new BfwRoles)->updateRole($customer_user); //Обновляем роль клиенту
    /*email*/
    $val = get_option( 'bonus_option_name' );



    /*Шаблонизатор письма*/
    $title_email = $val['email-when-order-change-title-vozvrat'] ?? __('Refund of bonus points', 'bonus-for-woo');
    $text_email = $val['email-when-order-change-text-vozvrat'] ?? '';

    $user = get_userdata($customer_user);
    $get_referral = get_user_meta($customer_user, 'bfw_points_referral', true );
    $text_email_array = array('[referral-link]'=> esc_url(site_url().'?bfwkey='. $get_referral),'[user]'=>$user->display_name,'[cashback]'=>$getplusball,'[order]'=>$order_id,'[points]'=>$fee_total,'[total]'=>$count_point);
    $message_email = (new BfwEmail)->template($text_email,$text_email_array);
    /*Шаблонизатор письма*/

    if(!empty($val['email-when-order-change'])){
          if($getplusball>0 OR $fee_total>0){
                (new BfwEmail)->getMail($customer_user, '', $title_email, $message_email);
            }
        }
    /*email*/

}