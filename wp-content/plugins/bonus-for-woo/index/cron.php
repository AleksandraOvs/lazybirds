<?php
/**
 * Автоматизация действий
 *
 * @version 4.2.0
 */


/*Удаление баллов за бездействие. Находим старых клиентов*/
add_action( 'bfw_clear_old_cashback', 'delete_balls_old_clients', 10, 3 );
 function delete_balls_old_clients(): void
 {

    $val = get_option('bonus_option_name');

    $day_day = $val['day-inactive'];//Количество дней которое дается клиентам на трату баллов


    $exclude_role = $val['exclude-role'] ?? '';

    $args=array(
        'role__not_in' => $exclude_role,/*Исключенные роли*/
        'meta_query' => array(
            array(/*где баллы больше 0*/
                'key' => 'computy_point',
                'value' => 0,
                'compare' => '>'
            )),
    );
    $users = get_users( $args );

    foreach ($users as $user) {

        if (!class_exists('WooCommerce')) {
            require ABSPATH . 'wp-content/plugins/woocommerce/woocommerce.php';
        }



        $customer_orders = get_posts( array(/*находим все заказы клиента*/
            'posts_per_page' => -1,
            'meta_key'    => '_customer_user',
            'order' => 'ASC',
            'meta_value'  => $user->ID ,
            'post_type' => wc_get_order_types('view-orders'),
            'post_status' => array_keys(wc_get_order_statuses())
        ) );

        if(count($customer_orders)==0){/*Если заказов 0*/

            // Сегодняшняя дата
            $today = strtotime(date("d.m.Y"));

            //Дата регистрации клиента
            $registered = $user->user_registered;
            $registered = strtotime(date( "d.m.Y", strtotime( $registered ) ));

            $seconds = abs($today - $registered);
            $days = floor($seconds / 86400);//количество дней регистрации
            if(!empty($val['day-inactive-notice'])) {
                $day_notice_remove_points = $val['day-inactive-notice'];//за сколько дней до удаления придет уведомление
                if ($day_notice_remove_points != '' || $day_notice_remove_points > 0) {
                    $notice = get_user_meta($user->ID, 'mail_remove_points', true) ?? '';
                    if ($notice != 'yes') {
                        $day_notice_remove_point = $day_day - $day_notice_remove_points;
                        if ($days > $day_notice_remove_point) {
                            //Отправляем уведомление
                            /*Шаблонизатор письма*/
                            $title_email = $val['email-when-inactive-notice-title'] ?? __('Your points will be deleted soon.',
                                    'bonus-for-woo');
                            $text_email = $val['email-when-inactive-notice-text'] ?? '';
                            $balluser = (new BfwPoints)->getPoints($user->ID);
                            $text_email_array = array(
                                '[user]' => $user->display_name,
                                '[days]' => $day_notice_remove_points,
                                '[points]' => $balluser
                            );
                            $message_email = (new BfwEmail)->template($text_email, $text_email_array);
                            /*Шаблонизатор письма*/
                            /*email*/
                            if (!empty($val['email-when-inactive-notice'])) {
                              (new BfwEmail)->getMail($user->ID, '', $title_email, $message_email);
                             }
                            /*email*/

                            update_user_meta($user->ID, 'mail_remove_points', 'yes');//говорим что уведомление отправлено
                        }
                    }
                }
            }
            if($days>$day_day){

                //Узнаем баллы клиента
                $computy_point_old = (new BfwPoints)->getPoints($user->ID);

                /*Запись в историю*/
                (new BfwHistory)->add_history($user->ID,'-',$computy_point_old,'0',sprintf( __( 'Inactivity %d days', 'bonus-for-woo' ), $day_day ));

                //Разрешаем вновь отправлять уведомления
                update_user_meta($user->ID, 'mail_remove_points', 'no');

                //Очищаем баллы клиенту
                (new BfwPoints)->updatePoints($user->ID,0);

            }
        }
        else{
            foreach ( $customer_orders as $customer_order ) {
                if (!next($customer_orders)) {
                    // Делаем что-либо с последним элементом.
                    $orderq = wc_get_order($customer_order);
                    $order_id = $orderq->get_id();
                    $data = $orderq->get_data();

                    $today = strtotime(date("d.m.Y"));

                    //Находим дату последнего заказа
                    $lastorderday = strtotime( $data['date_created']->date('d.m.Y'));
                    $seconds = abs($today - $lastorderday);
                    $days = floor($seconds / 86400);//количество дней с последнего заказа


                    if(!empty($val['day-inactive-notice'])) {
                        $day_notice_remove_points = $val['day-inactive-notice'];//за сколько дней до удаления придет уведомление
                        if ($day_notice_remove_points != '' || $day_notice_remove_points > 0) {
                            $notice = get_user_meta($user->ID, 'mail_remove_points', true) ?? '';
                            if ($notice != 'yes') {
                                $day_notice_remove_point = $day_day - $day_notice_remove_points;
                                if ($days > $day_notice_remove_point) {
                                    //Отправляем уведомление
                                    /*Шаблонизатор письма*/
                                    $title_email = $val['email-when-inactive-notice-title'] ?? __('Your points will be deleted soon.',
                                            'bonus-for-woo');
                                    $text_email = $val['email-when-inactive-notice-text'] ?? '';
                                    $balluser = (new BfwPoints)->getPoints($user->ID);
                                    $text_email_array = array(
                                        '[user]' => $user->display_name,
                                        '[days]' => $day_notice_remove_points,
                                        '[points]' => $balluser
                                    );
                                    $message_email = (new BfwEmail)->template($text_email, $text_email_array);
                                    /*Шаблонизатор письма*/
                                    /*email*/
                                    if (!empty($val['email-when-inactive-notice'])) {
                                        (new BfwEmail)->getMail($user->ID, '', $title_email, $message_email);
                                   }
                                    /*email*/

                                    update_user_meta($user->ID, 'mail_remove_points', 'yes');//говорим что уведомление отправлено
                                }
                            }
                        }
                    }
                    if($days>$day_day){

                        //Узнаем баллы клиента
                        $computy_point_old = (new BfwPoints)->getPoints($user->ID);

                        update_user_meta($user->ID, 'mail_remove_points', 'no');//Разрешаем вновь отправлять уведомления
                        /*Запись в историю*/
                        (new BfwHistory)->add_history($user->ID,'-',$computy_point_old,'0',sprintf( __( 'Inactivity %d days', 'bonus-for-woo' ), $day_day ));

                        //Очищаем баллы клиенту
                        (new BfwPoints)->updatePoints($user->ID,0);

                    }
                }

            }
        }
    }

}
/*Находим старых клиентов*/


$val = get_option('bonus_option_name');
if(!empty($val['day-inactive'])){


if($val['day-inactive'] != '' || $val['day-inactive'] > 0){

    if( !wp_next_scheduled('bfw_clear_old_cashback') ){
        /*Запись в крон (Проверка каждый день)*/
         wp_schedule_event( time(), 'daily', 'bfw_clear_old_cashback');

    }
}else{
    /*Убрать крон*/
    wp_clear_scheduled_hook( 'bfw_clear_old_cashback' );
}

}else{
    /*Убрать крон*/
    wp_clear_scheduled_hook( 'bfw_clear_old_cashback' );
}
/*Удаление баллов за бездействие находим старых клиентов*/



/*Начисление баллов в день рождение*/
add_action( 'bfw_search_birthday', 'bfw_add_balls_for_birthday', 10, 3 );
function bfw_add_balls_for_birthday(): void
{
    $val = get_option('bonus_option_name');
    $exclude_role = $val['exclude-role'] ?? '';

    $args=array(
        'role__not_in' => $exclude_role,/*Исключенные роли*/
        'meta_query' => array(
            array(/*Где баллы больше 0*/
                'key' => 'dob',
                'value' => 0,
                'compare' => '>'
            )),
    );
    $users = get_users( $args );

    foreach ($users as $user) {

        /*шаблонизатор письма*/
        $title_email = $val['email-whens-birthday-title'] ?? __('Bonus points on your birthday', 'bonus-for-woo');
        $text_email = $val['email-when-birthday-text'] ?? '';
        $text_email_array = array(
            '[user]' => $user->display_name,
            '[points_for_birthday]' => $val['birthday']
        );
        $message_email = (new BfwEmail)->template($text_email, $text_email_array);
        /*Шаблонизатор письма*/

        //Находим у кого др сегодня
        $pricina = __('Birthday', 'bonus-for-woo');
       if(date("d.m", strtotime($user->dob)) == date('d.m')){


            if(!empty($user->this_year )) {
                //Если есть уже начисление, то проверяем год
                if($user->this_year != date('Y')){
                    //Начисление не равно этому году, то дарим баллы

                    /*Узнаем баллы клиента*/
                    $computy_point_old = (new BfwPoints)->getPoints($user->ID);
                    $count_point =  $computy_point_old + $val['birthday'];
                    (new BfwPoints)->updatePoints($user->ID,$count_point);//обновляем баллы клиенту
                    (new BfwHistory)->add_history($user->ID,'+',$val['birthday'],'0',$pricina);//записываем в историю
                    update_user_meta( $user->ID, 'this_year', date('Y'));//обновляем год зачисления бонусов
                    /*email*/
                    if(!empty($val['email-when-birthday'])){
                            (new BfwEmail)->getMail($user->ID, '', $title_email, $message_email);
                        }
                    /*email*/
                }
            }else{

                //Начислений нет, дарим баллы
                /*Узнаем баллы клиента*/
                $computy_point_old = (new BfwPoints)->getPoints($user->ID);
                $count_point =  $computy_point_old + $val['birthday'];
                (new BfwPoints)->updatePoints($user->ID,$count_point);//обновляем баллы клиенту
                (new BfwHistory)->add_history($user->ID,'+',$val['birthday'],'0',$pricina);//записываем в историю
                update_user_meta( $user->ID, 'this_year', date('Y'));//обновляем год зачисления бонусов
                /*email*/
                if(!empty($val['email-when-birthday'])){
                        (new BfwEmail)->getMail($user->ID, '', $title_email, $message_email);
                    }
                /*email*/
            }
        }


    }


}

if(!empty($val['birthday'])){
    if( !wp_next_scheduled('bfw_search_birthday') ){
        /*Запись в крон (Проверка каждый день)*/
        wp_schedule_event( time(), 'daily', 'bfw_search_birthday');
    }
}else{
    /*Убрать крон*/
    wp_clear_scheduled_hook( 'bfw_search_birthday' );
}
/*Начисление баллов в день рождение*/