<?php
/**
 * Страница статистики
 *
 * @version 5.1.2
 */


/*todo сделать через ajax. то есть обработка в несколько проходов. тогда будет виден процесс и не будет белого экрана*/
?>
<style>
    .bfw-stat-wrap{
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }
    .bfw-stat-block{
        background: #fff;
        padding: 0 20px 20px 20px;
        margin: 10px 0;
        border-radius: 5px;
        border: 1px solid #dcdcde;
    }
</style>
<div class="wrap bonus-for-woo-admin">
    <?php  echo '<h1>'. __('Bonus system statistic', 'bonus-for-woo').'</h1>'; ?>
   <p style="color: red"><?php echo __(' At the moment, the statistics are in testing mode.', 'bonus-for-woo'); ?></p>
    <p><?php echo __('Statistics will be updated. For suggestions on statistics, please email info@computy.ru.', 'bonus-for-woo'); ?></p>
    <p><a href="/wp-admin/admin.php?page=bonus-for-woo%2Findex%2Fstatistic.php&userupdate=true"><?php echo __('Update user statuses','bonus-for-woo'); ?></a></p>
    <hr>
<div class="bfw-stat-wrap">
    <?php



    wp_register_style( 'chart.min.css', BONUS_COMPUTY_PLUGIN_URL . '_inc/chart/Chart.min.css', array(), BONUS_COMPUTY_VERSION );
    wp_register_script( 'chart.min.js', BONUS_COMPUTY_PLUGIN_URL . '_inc/chart/Chart.min.js', array(), BONUS_COMPUTY_VERSION );
    wp_register_script( 'knob.min.js', BONUS_COMPUTY_PLUGIN_URL . '_inc/chart/jquery.knob.min.js', array(), BONUS_COMPUTY_VERSION );

    wp_enqueue_style( 'chart.min.css' );
    wp_enqueue_script( 'chart.min.js' );
    wp_enqueue_script( 'knob.min.js' );

$val = get_option('bonus_option_name');
$exclude_roles = $val['exclude-role'] ?? array();


$args1=array(
    'role__not_in' => $exclude_roles ,/*Исключенные роли*/
);
$users_bs = get_users( $args1 );


    if(isset($_GET['userupdate'])){
        /*Обновление статусов пользователей*/
        if($_GET['userupdate']=='true'){

            foreach ($users_bs as $user) {
                (new BfwRoles)->updateRole($user->ID);
                           }
        }
    }




global $wpdb;
$table_bfw = $wpdb->get_results("SELECT  * FROM " . $wpdb->prefix . "bfw_computy");
    $total_in_bfw_names='';
    $total_in_bfw_count_users='';


    $args_role = array(
        'role__not_in' => $exclude_roles,
    );



    $users = get_users($args_role);
    $status_id=array();
    foreach ($users as $user) {

        // Обработка метаданных пользователя
        if(get_user_meta($user->ID, 'bfw_status', true)){
            $status_id[]= get_user_meta($user->ID, 'bfw_status', true);
        }


    }

    echo '<div class="bfw-stat-block" style="width: 300px">
<h3>'. sprintf(__('Total in the bonus system: %s of users', 'bonus-for-woo'),count($status_id)).'</h3>';

    $first_names = $status_id;

    $list_uniq_status = array_count_values($first_names);/*Находим количество уникальных значений массива*/

    foreach ($table_bfw as $bfw) {
        if(isset($list_uniq_status[$bfw->id])){
            $total_in_bfw_names .= "'".$bfw->name."',";
            $total_in_bfw_count_users .= "'".$list_uniq_status[$bfw->id]."',";
        }

    }


?>
<canvas id="pieChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 250px;"></canvas>
</div>
 <script>
        jQuery(function () {
        let donutData = {
            labels: [  <?php  echo $total_in_bfw_names; ?> ],
            datasets: [
                {
                    data: [ <?php  echo $total_in_bfw_count_users; ?> ],
                    backgroundColor : ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de', '#333', '#5c17b8', '#e9ec23'],
                }
            ]
        }
        let pieChartCanvas = jQuery('#pieChart').get(0).getContext('2d')
            let pieData        = donutData;
            let pieOptions     = {
            maintainAspectRatio : true,
            responsive : true,
            legend: {
                display: false
            }
        }
        new Chart(pieChartCanvas, {
            type: 'pie',
            data: pieData,
            options: pieOptions
        })

        })
    </script>


    <div class="bfw-stat-block" style="width: 300px">
        <h3><?php echo  __('Order statistics', 'bonus-for-woo'); ?></h3>
        <?php
        $order_status = $val['add_points_order_status'] ?? 'completed';
        $args3 = array(
            'status' => array('wc-processing', 'wc-'.$order_status),
            'type' => 'shop_order',
            /* todo Тут по хорошему исключить неавторизованных пользователей*/
        );

        $orders = wc_get_orders( $args3 );
        $count_all_orders = count($orders);
        if($count_all_orders==0){
            echo 'Заказов нет.';
        }
else{



        $count_fee=0;
        foreach($orders as $one_order){
                /* Бонусы с помощью купонов  */
                foreach( $one_order->get_coupon_codes() as $coupon_code ) {
                    $coupon = new WC_Coupon($coupon_code);
                   $get_code = $coupon->get_code();
                    $cart_discount =  mb_strtolower($val['bonus-points-on-cart']);
                    if(strtolower($get_code)=== strtolower($cart_discount)){ $count_fee++;}
                }
               /* Бонусы с помощью комиссий  */
                foreach( $one_order->get_items('fee') as $item_id => $item_fee ){
                    $val = get_option( 'bonus_option_name' );
                    $count_fee++;
                }


        }


        echo '<p>'.sprintf(__('Out of %s of orders in %s points applied', 'bonus-for-woo'),$count_all_orders,$count_fee).'</p>';
        //Найти сколько потрачено баллов
        $percent_with_fee = 100*$count_fee/$count_all_orders;
        $percent_with_fee =round($percent_with_fee);
        echo ' <input type="text" class="knob" value="'.$percent_with_fee.'" data-width="90" data-height="90" data-fgColor="#3c8dbc"
                           data-readonly="true">';
        ?>
        <script>
            jQuery(function () {
                jQuery('.knob').knob({
                    'format' : function (value) {
                        return value + '%';
                    }}
                );

            })
        </script>

        <?php
        $total=0;

        foreach($users_bs as $to_user){

            //Находим сумму всех оплаченных заказов
            $total_one = (new BfwPoints)->getSumUserOrders($to_user->ID);
            $total=$total+$total_one;
        }





        if(wc_get_orders( $args3 )){
            $orders_c = wc_get_orders( $args3 );
            $total_fee=0;
            $fee_total=0;
            foreach($orders_c as $one_order){

                    /* Если бонусы с помощью купонов  */
                    foreach( $one_order->get_coupon_codes() as $coupon_code  ) {

                        $coupon = new WC_Coupon($coupon_code);
                        $get_code = $coupon->get_code();
                        $cart_discount =  mb_strtolower($val['bonus-points-on-cart']);
                        if(strtolower($get_code)=== strtolower($cart_discount)){
                        $fee_total = $one_order->get_discount_total();

                        }$total_fee=$total_fee+$fee_total;
                    }


                    /* Если бонусы с помощью комиссий  */
                    foreach( $one_order->get_items('fee') as $item_id => $item_fee ){
                        $bpic = $val['bonus-points-on-cart'] ?? __('Bonus points', 'bonus-for-woo');
                        if( $item_fee->get_name() == $bpic){
                            $fee_total = $item_fee->get_total();
                        }
                        $total_fee=$total_fee+$fee_total;
                    }


            }$total_fee = absint($total_fee);

            if((new BfwPoints())->roundPoints($total)==0){
                echo '<p>'.__('Orders not found.', 'bonus-for-woo').'</p>';
            }else{
                echo '<p>Из '. (new BfwPoints())->roundPoints($total).' '.get_woocommerce_currency_symbol().'  оплачено баллами: '.  (new BfwPoints())->roundPoints($total_fee) .' '.get_woocommerce_currency_symbol().'</p>';

                $percent_with_fee_sum = 100*$total_fee/$total;
                $percent_with_fee_sum =round($percent_with_fee_sum,2);
                echo ' <input type="text" class="knob" value="'.$percent_with_fee_sum.'" data-width="90" data-height="90" data-fgColor="#FF5E4E"
                           data-readonly="true">';
            }

        }else{
            echo '<p>'.__('Orders not found.', 'bonus-for-woo').'</p>';
        }
}  ?>
    </div>






    <div class="bfw-stat-block" style="width: 300px">
        <h3><?php echo  __('User statistics', 'bonus-for-woo'); ?></h3>

        <?php
        $max=0;
        $argsp=array(
            'meta_key' => 'computy_point' ,/*Исключенные роли*/
        );
        $users2 = get_users($argsp);
        foreach ($users2 as $user){
            $points = get_user_meta($user->ID, 'computy_point', true) ?? 0;

            if($points > $max or $max === null){
                $uid = $user->ID;
                $max = $points;
                if(!empty($user->first_name)){
                    $name_user = $user->first_name.' '.$user->last_name;
                }else{
                    $name_user = $user->user_login;
                }
            }
             }
        if(!empty($uid)){?>
            <p><?php echo  __('Most points on account of', 'bonus-for-woo'); ?> <?php echo '<a href="/wp-admin/user-edit.php?user_id='.$uid.'" target="_blank">'.$name_user.'</a>';?>: <?php echo (new BfwPoints())->roundPoints($max) .' '.(new BfwPoints())->pointsLabel($max);  ?></p>

        <?php
        }
        $max=0;

        foreach ($users2 as $user){
            $points = get_user_meta($user->ID, 'computy_point', true) ?? 0;
            $uid = $user->ID;

            $args = array(
                'customer_id' => $uid,
                'limit' => -1,
                'type' => 'shop_order',
            );
            $orders_u = wc_get_orders($args);
            $total_fee=0;$fee_total=0;
            foreach($orders_u as $one_order){
                if (!empty($val['fee-or-coupon'])) {
                    /* Если бонусы с помощью купонов  */
                    foreach( $one_order->get_coupon_codes() as $coupon_code  ) {

                        $coupon = new WC_Coupon($coupon_code);
                        $get_code = $coupon->get_code();
                        $cart_discount =  mb_strtolower($val['bonus-points-on-cart']);
                        if(strtolower($get_code)=== strtolower($cart_discount)){
                            $fee_total = $one_order->get_discount_total();

                        }$total_fee=$total_fee+$fee_total;
                    }

                }else {
                    $fee_total=0;
                    foreach ($one_order->get_items('fee') as $item_id => $item_fee) {
                        if ($item_fee->get_name() == $val['bonus-points-on-cart']) {
                            $fee_total = $item_fee->get_total();
                        }
                        $total_fee = $total_fee + $fee_total;
                    }
                }
            }
            $total_fee = absint($total_fee);
            if($total_fee > $max or $max === null){

                $max = $total_fee;
                if(!empty($user->first_name)){
                    $name_user = $user->first_name.' '.$user->last_name;
                }else{
                    $name_user = $user->user_login;
                }
                echo __('Spent the most points', 'bonus-for-woo');
 echo ' <a href="/wp-admin/user-edit.php?user_id='.$uid.'" target="_blank">'.$name_user.'</a>'; ?> : <?php echo (new BfwPoints())->roundPoints($max) .' '.(new BfwPoints())->pointsLabel($max).' <br>';

            }
        }
        ?>



    </div>




    <?php  if ((new BfwRoles)->is_pro() AND !empty($val['referal-system'])){ ?>
        <div class="bfw-stat-block" style="width: 300px">
            <h3><?php echo  __('Referral statistics', 'bonus-for-woo'); ?></h3>
            <?php


            $args_ref =  array(
                'role__not_in' => $exclude_roles,
                'meta_query' => array(
                    'key' => 'bfw_points_referral',
                    'value' => '0',
                    'compare' => '!=',
                ),
            );
            $args_invite = array(
                'role__not_in' => $exclude_roles,
                'meta_key'=> 'bfw_points_referral_invite',
                'meta_value'=> '0',
                'meta_compare' => '!=',
            );
            $users = get_users( $args_ref );
            //Количество пользователей в реферальной системе
            echo '<p>'. __('Referral system members:', 'bonus-for-woo').' '.count($users);
            $users = get_users( $args_invite );
            //Количество приглашенных
            echo '<p>'. __('Total invitees:', 'bonus-for-woo').' '.count($users);

            ?>

        </div>

    <?php } ?>

    <?php  /*if (is_user_logged_in()) { ?>
        <?php    echo get_num_queries(); ?> запросов за <?php timer_stop(1); ?> секунд.
    <?php }   */?>



</div>