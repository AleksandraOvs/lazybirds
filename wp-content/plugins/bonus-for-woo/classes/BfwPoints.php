<?php
/**
 * Class Points
 *
 * @version 2.5.1
 */

class BfwPoints
{
    /**
     * Склонение существительных после числительных.
     * @param float $points
     * @param bool $show Включает значение $value в результирующею строку
     * @return string
     * @version 2.10.1
     */
    public static function pointsLabel(float $points, bool $show = true ): string
    {
        $value = get_option( 'bonus_option_name' );

        $label_point = $value['label_point'] ?? __( 'Point', 'bonus-for-woo' );
        $label_point_two = $value['label_point_two'] ?? __( 'Points', 'bonus-for-woo' );
        $label_points = $value['label_points'] ?? __( 'Points', 'bonus-for-woo' );

        $words = array($label_point, $label_point_two, $label_points);

        $num = (int)$points % 100;
        if ($num > 19) {
            $num = $num % 10;
        }

        switch ($num) {
            case 1:  $out = $words[0]; break;
            case 2:
            case 3:
            case 4:  $out = $words[1]; break;
            default: $out = $words[2]; break;
        }

        return $out;
    }



    /**
     * Данное решение для тех параметров, где используется перед числом предлог "до" например "до 101 балла"
     *
     * @param float $points
     * @param bool $show
     * @return string
     */
    public static function pointsLabelUp(float $points, bool $show = true ): string
    {

        $value = get_option( 'bonus_option_name' );

        $label_point_two = $value['label_point_two'] ?? __( 'Points', 'bonus-for-woo' );
        $label_points = $value['label_points'] ?? __( 'Points', 'bonus-for-woo' );

        $words = array($label_point_two, $label_points, $label_points);


        $num = (int)$points % 100;
        if ($num > 19) {
            $num = $num % 10;
        }

        switch ($num) {
            case 1:  $out = $words[0]; break;
            case 2:
            case 3:
            case 4:  $out = $words[1]; break;
            default: $out = $words[2]; break;
        }

        return $out;
            }


            /**
             * Метод решает какой использовать метод с "до" или без "до"
             *
             * @depends pointsLabelUp|pointsLabel
             * @version 5.0.1
             */
    public static function howLabel(string $up, float $points): string
    {
            if (empty($val['bonus-in-price-upto']) AND !empty($up)) {
               return self::pointsLabelUp($points);
            }
        else{
            return self::pointsLabel($points);
        }

    }



    /**
     * Возвращает количество бонусных баллов пользователя
     *
     * @param int $userId
     * @return float
     * @version 2.5.1
     */
    public static function getPoints(int $userId): float
    {
       $points = get_user_meta($userId, 'computy_point', true) ?? 0;
       return  (float)$points;
    }



    /**
     * Округляет баллы до нужного числа
     *
     * @param float $points
     * @return float
     * @since  4.8.0
     */
    public static function roundPoints(float $points): float
    {
        $val = get_option('bonus_option_name');
        if (empty($val['round_points'])){
            return round($points);
        }else{
            return round($points,2);
        }
    }



    /**
     * Возвращает баллы пользователя, которые он хочет списать
     *
     * @param int $userId
     * @return float
     * @version 2.5.1
     */
    public static function getFastPoints(int $userId): float
    {
        $points = get_user_meta($userId, 'computy_fast_point', true);
        return (float)$points;
    }



    /**
     * Обновление бонусных баллов
     *
     * @param int $userId
     * @param float $newBalls
     * @since 2.5.1
     * @version 4.8.0
     */
    public static function updatePoints(int $userId, float $newBalls): void
    {
        if($newBalls<0){
            $newBalls=0;
        }
        $newBalls = apply_filters('bfw-update-points-filter', $newBalls, $userId);
        update_user_meta($userId, 'computy_point', $newBalls);
    }



    /**
     * Обновление баллов, которые пользователь хочет списать
     *
     * @param int $userId
     * @param float $newballs
     * @since 2.5.1
     * @version 4.8.0
     */
    public static function updateFastPoints(int $userId, float $newballs): void
    {
       if($newballs>0){
        update_user_meta($userId, 'computy_fast_point', $newballs);
       }else{
           update_user_meta($userId, 'computy_fast_point', 0);
       }
    }



    /**
     * Находим сумму всех оплаченных заказов клиента
     * так как wc_get_customer_total_spent ($to_user->ID); включает сумму не оплаченных заказов тоже.
     * @param int $userId
     * @return float
     * @version 5.6.2
     *
     */
    public static function getSumUserOrders(int $userId): float
    {
        $val = get_option('bonus_option_name');
        $order_staus = $val['add_points_order_status'] ?? 'completed';
        $data_start='';
        if ((new BfwRoles)->is_pro()) {
          /*С какой даты начинать считать сумму заказов*/
            $data_start = $val['order_start_date'] ?? ''; //2023-06-0
            if(!empty($val['order_start_date'])){
                $datastart =$val['order_start_date'];
                $data_start =  "AND p.post_date >=   '$datastart' ";
            }
        }

        $order_staus = "wc-".$order_staus;
        global $wpdb;
        $total_alls = $wpdb->get_var( "SELECT SUM(pm.meta_value) FROM {$wpdb->prefix}postmeta as pm
INNER JOIN {$wpdb->prefix}posts as p ON pm.post_id = p.ID
INNER JOIN {$wpdb->prefix}postmeta as pm2 ON pm.post_id = pm2.post_id
WHERE p.post_status LIKE '$order_staus' AND p.post_type LIKE 'shop_order'
AND pm.meta_key LIKE '_order_total' AND pm2.meta_key LIKE '_customer_user'
AND pm2.meta_value LIKE $userId $data_start
" );
        if(empty($total_alls)){
            $total_alls=0;
        }
        return $total_alls;
    }



    /**
     * Проведение оффлайн-заказа
     * @param $price
     * @param $user_id
     * @return void
     * @since 5.1.0
     * @version 5.1.0
     */
    public static function add_offline_order($price, $user_id): void
    {

        /*1. Создаем офлайн продукт*/
        $offline_product = get_option('bonus-for-woo-offline-product');
        if ( get_post($offline_product)) {
            set_post_status_bfw( 'publish', $offline_product );
            $post_id =$offline_product;
        }else{
            $val = get_option('bonus_option_name');
            $post_title = $val['title-product-offline-order'] ?? __('Offline product', 'bonus-for-woo');
            $post_id = wp_insert_post( array(
                'post_title' => $post_title,
                'post_type' => 'product',
                'post_status' => 'publish',
                'post_content' => __('Technical product for accrual of bonus points', 'bonus-for-woo')
            ));

            wp_set_object_terms( $post_id, 'simple', 'product_type');
            update_post_meta( $post_id, '_visibility', 'hidden' );/*скрыть с каталога*/
            update_post_meta( $post_id, '_stock_status', 'instock');
            update_post_meta( $post_id, '_virtual', 'yes');
            update_post_meta( $post_id, '_regular_price', "1" );
            update_post_meta( $post_id, '_price', "1" );

            update_option( 'bonus-for-woo-offline-product', $post_id );/*указываем товар для проведения продаж офлайн*/
        }

        /*2. Создаем заказ клиенту на нужную сумму*/
        $order = wc_create_order();
        $order->add_product( wc_get_product( $post_id ), $price );
// Установим платёжный метод, например пусть это будет оплата наличными при получении
        $payment_gateways = WC()->payment_gateways->payment_gateways();
        if( ! empty( $payment_gateways[ 'cod' ] ) ) {
            $order->set_payment_method( $payment_gateways[ 'cod' ] );
        }

        $val = get_option('bonus_option_name');

        if (empty($val['add_points_order_status'])) {
            $val['add_points_order_status'] = 'completed';
        }
        // Пересчитываем заказ
        $order->calculate_totals();
        $current_user = wp_get_current_user();
        $order->add_order_note( __('Order created by administrator: ', 'bonus-for-woo').$current_user->user_login );
        update_post_meta( $order->get_id(), '_customer_user', $user_id );
        if($order->update_status( $val['add_points_order_status'] )){
            /*3. кидаем офлайн продукт в черновики*/
            set_post_status_bfw( 'draft', $post_id );
        }
}




    /**
     * Начисление ежедневных баллов за первый вход
     *
     * @param $user_id
     * @return void
     * @since 5.2.0
     * @version 5.2.0
     */
    public static function add_every_days($user_id): void
    {
        $val = get_option('bonus_option_name');
        $point_every_day = $val['every_days'] ?? 0;
        if($point_every_day>0){

        //Проверяем получал ли сегодня клиент баллы
        $last_day = get_user_meta($user_id, 'points_every_day', true);
        if($last_day!=date('d')){
            //Узнаем количество баллов клиента
                $count_point = static::getPoints($user_id);
                $new_point = $count_point+$point_every_day;

            //Начисляем баллы клиенту
            static::updatePoints($user_id,$new_point);

            //обновляем день
            update_user_meta( $user_id, 'points_every_day', date('d'));
            $pricina =  sprintf(__('Daily %s for the login.', 'bonus-for-woo'), $val['label_points']);

            //Записываем в историю
            (new BfwHistory)->add_history($user_id, '+', $point_every_day, '0', $pricina);
            //отправляем письмо
            if(!empty($val['email-when-everyday-login'])) {
                        /*Шаблонизатор письма*/

                $text_email = $val['email-when-everyday-login-text'] ?? '';

                $title_email = $val['email-when-everyday-login-title'] ?? __('Bonus points have been added to you!', 'bonus-for-woo');
                $user = get_userdata($user_id);
                $text_email_array = array(
                    '[user]' => $user->display_name,
                    '[points]' => $point_every_day,
                    '[total]' => $new_point
                );

                $message_email = (new BfwEmail)->template($text_email,$text_email_array);
                /*Шаблонизатор письма*/
                (new BfwEmail)->getMail($user_id, '', $title_email, $message_email);

            }
        }
        }
    }




}