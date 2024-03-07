<?php
/**
 * Страница бонусов в аккаунте клиента
 *
 * @version      5.0.2
 */


/*  * Заголовок страницы бонусов в аккаунте * */
add_action( 'bfw_account_title', 'bfw_account_title' );
if ( ! function_exists( 'bfw_account_title' ) ) {
  function bfw_account_title():void {
 $val = get_option('bonus_option_name');

$titleBonusPage = $val['title-on-account'] ?? __('Bonus page', 'bonus-for-woo');
echo $titleBonusPage;
	}
}

 /* Вывод основной информации: статус, процент кешбэка, количество бонусных баллов  */
add_action( 'bfw_account_basic_info', 'bfw_account_basic_info' );
if ( ! function_exists( 'bfw_account_basic_info' ) ) {
    /**
     * @throws Exception
     */
    function bfw_account_basic_info(): void
    {
        $userid = get_current_user_id();

        $val = get_option('bonus_option_name');

        $title_my_status_on_account = $val['title-my-status-on-account'] ?? __('My status', 'bonus-for-woo');
        $title_my_percent = $val['my-procent-on-account'] ?? __('My cashback percentage', 'bonus-for-woo');
        $title_my_bonus_points = $val['my-bonus-points-on-account'] ?? __('My bonus points', 'bonus-for-woo');

        (new BfwRoles)->updateRole($userid);

        echo '<div class="bonuses__container">';

        echo '<div class="bonus_computy_account bfw-account_status_name"><span class="title_bca">Мой статус</span> <span class="value_bca">' . (new BfwRoles)->getRole($userid)['name'] . '</span></div>';
        echo '<div class="bonus_computy_account bfw-account_percent"><span class="title_bca">Кешбек</span> <span class="value_bca"> ' . (new BfwRoles)->getRole($userid)['percent'] . '% </span></div>';
        
        $computy_point = (new BfwPoints)->getPoints($userid);
        $computy_point = $computy_point ?? 0;
        echo '<div class="bonus_computy_account bfw-account_count_points"><span class="title_bca">Мои баллы</span> <span class="value_bca">' . (new BfwPoints())->roundPoints($computy_point) . '</span></div>';

        echo '</div>';

        /*Отображать ли срок когда сгорят баллы*/
        if (empty($val['burn_point_in_account'])) {

            if (isset($val['day-inactive'])) {
                if ($val['day-inactive'] > 0 and $computy_point > 0) {

                    $customer = new WC_Customer($userid);
                    $last_order = $customer->get_last_order();

                    if ($last_order) {
                        $order_data = $last_order->get_data();
                        $order_date_created = '';
                        foreach ($last_order->get_items() as $ignored) {
                            $order_date_created = $order_data['date_created']->date('Y-m-d h:i:s');
                        }
                        $date_order = new DateTime($order_date_created);
                        $today = new DateTime(date('Y-m-d h:i:s'));
                        $abs_diff = $today->diff($date_order)->format("%a");
                        $ostalos = $val['day-inactive'] - $abs_diff;
                    } else {
                        /*Если нет заказов*/
                        // Сегодняшняя дата
                        $today = strtotime(date("d.m.Y"));

                        //Дата регистрации клиента
                        $udata = get_userdata($userid);

                        $registered = $udata->user_registered;
                        $registered = strtotime(date("d.m.Y", strtotime($registered)));
                        $seconds = abs($today - $registered);
                        $days = floor($seconds / 86400);//количество дней регистрации
                        $ostalos = $val['day-inactive'] - $days;
                    }
                    if ($ostalos > -1) {
                        echo '<div class="bonus_computy_account bfw-account_expire_points"><span class="title_bca">Остаток дней</span> <span class="value_bca"> ' . $ostalos . ' ' . __('days', 'bonus-for-woo') . '</span></div>';
                    }
                }
            }
            /*Отображать ли срок когда сгорят баллы*/


        }
    }
}


/*  * Ввод купонов(только для PRO-версии)* */
add_action( 'bfw_account_coupon', 'bfw_account_coupon' );
if ( ! function_exists( 'bfw_account_coupon' ) ) {
    function bfw_account_coupon():void {

      if( (new BfwRoles)->is_pro() ){
           $val = get_option('bonus_option_name');
    if(!empty($val['coupon-system'])){
        echo ' <div class="bonus_computy_account add_coupon">
<div class="title computy_skidka_link">'. sprintf(__('Enter coupon code to take %s.','bonus-for-woo'), (new BfwPoints())->pointsLabel(25)). '</div>
<div class="computy_skidka_container coupon_form" style="display: none;">
<form class="take_coupon_form" action="'.admin_url("admin-post.php") . '" method="post">
 <input type="hidden" name="action" value="bfw_take_coupon_action" /> 
 <input type="hidden" name="redirect" value="' .  get_permalink( get_option('woocommerce_myaccount_page_id') )  .'/bonuses"> 
<input type="text" name="code_coupon" placeholder="'.__('Coupon code','bonus-for-woo').'" required>
<input type="submit" class="code_coupon_submit" value="'.__('To take','bonus-for-woo').'"><div class="message_coupon"></div>
</form>
</div>
</div>  ';
        ?>

        <?php
    }
    }
	}
}


/*  * Прогресс бар * */
add_action( 'bfw_account_progress', 'bfw_account_progress' );
if ( ! function_exists( 'bfw_account_progress' ) ) {
    function bfw_account_progress():void {
   $userid = get_current_user_id();
  $val = get_option('bonus_option_name');
  $total = (new BfwPoints)->getSumUserOrders($userid); /*сумма всех покупок округленная до целого*/

    //узнать сколько рублей осталось до следующего статуса
    $nextrole = (new BfwRoles)->getNextRole($userid);

    if ($nextrole['status'] == 'next' or $nextrole['status'] == 'max') {

        if (get_option('bonus-for-woo-pro') == 'active') {
            ?>
            <ol class="bfw-progress-bar">
            <?php
            global $wpdb;
            $tab_bfw = $wpdb->get_results("SELECT *,summa_start FROM " . $wpdb->prefix . "bfw_computy  ORDER BY summa_start + 0 asc");
            $you_role = (new BfwRoles)->getRole($userid)['name'];
            foreach ($tab_bfw as $bfw) {
                echo '<li class="';
                if ($bfw->summa_start < $total) {
                    echo ' is-complete ';
                }
                if ($you_role === $bfw->name) {
                    echo ' is-active ';
                }
                echo '"><span>' . $bfw->name . '</span></li>';
            }
            echo '</ol>';

            ?>
            <div class="bfw-progressbar-block">
                <style> #bfw-progressbar > div {
                        width: <?php if($nextrole['status']!='max'){ echo $nextrole['percent-zarabotannogo'];}else{echo '100'; $nextrole['percent-zarabotannogo']='100';} ?>%;
                    }

                    #bfw-progressbar > div span {
                    <?php if($nextrole['percent-zarabotannogo']<10){echo 'left:6px;';}
                    else{echo 'right: 6px;';} ?>
                    }
                </style>
                <div class="bfw-progressbar-title">
                    <div class="bfw-progressbar-title-one"><?php echo (new BfwRoles)->getRole($userid)['name']; ?></div>
                    <div class="bfw-progressbar-title-two"><?php if ($nextrole['status'] != 'max') {
                            echo $nextrole['name'];
                        } ?></div>
                </div>
                <div id="bfw-progressbar">
                    <div><span><?php echo (new BfwPoints())->roundPoints($total)  . ' ' . get_woocommerce_currency_symbol(); ?></span></div>
                </div>
            </div>

        <?php } ?>

        <?php
    }
    if ($nextrole['status'] == 'next') {
        $remaining_amount1 = $val['remaining-amount1'];
        $remaining_amount2 = $val['remaining-amount2'];
        $remaining_amount3 = $val['remaining-amount3'];

        echo '<small class="remaining-amount">' . $remaining_amount1 . ' ' . $nextrole['percent'] . '% ' . $remaining_amount2 . ' «' . $nextrole['name'] . '», тебе необходимо приобрести товары на ' . $nextrole['sum'] . ' ' . get_woocommerce_currency_symbol() . '</small>';
    } elseif ($nextrole['status'] == 'max') {
        echo '<small class="remaining-amount">У тебя максимальный кешбек!</small>';
    } elseif ($nextrole['status'] == 'no') {
        echo '<small class="remaining-amount">' . __('At the moment, the bonus system is not available.',
                'bonus-for-woo') . '</small>';
    } else {
        echo '<small class="remaining-amount">' . __('Unfortunately, you cannot participate in the bonus system.',
                'bonus-for-woo') . '</small>';
    }
	}
}


/*  * Реферальная система (только для PRO-версии) * */
add_filter( 'bfw_account_referal', 'bfw_account_referal' );
if ( ! function_exists( 'bfw_account_referal' ) ) {
function bfw_account_referal(): string
{
    $referral ='';
    if ((new BfwRoles)->is_invalve()  AND (new BfwRoles)->is_pro() ) {

    $val = get_option('bonus_option_name');
    if (!empty($val['referal-system'])) {
        $userid = get_current_user_id();
        /*Реферальный код пользователя*/
        $get_referral = get_user_meta($userid, 'bfw_points_referral', true);
        /*Количество приглашенных пользователем клиентов*/
        $get_referral_invite = get_user_meta($userid, 'bfw_points_referral_invite', true);
        if (empty($get_referral)) {
            $referral_key = bfw_create_referal_code();
            update_user_meta($userid, 'bfw_points_referral', $referral_key);
        }
        if (empty($get_referral_invite)) {
            $referral_invite = 0;
            update_user_meta($userid, 'bfw_points_referral_invite', $referral_invite);
        }


        /*Сколько людей пригласил*/
        $argsa['meta_query'] = array(
            array(
                'key' => 'bfw_points_referral_invite',
                'value' => trim($userid),
                'compare' => '==',
            ),
        );
        $refere_data = get_users($argsa);
        $refere_data_two_two = 0;
         if (!empty($val['level-two-referral'])) {
        /*Считаем второй уровень*/

        foreach ($refere_data as $refere_data_two){
            $argsatwo['meta_query'] = array(
            array(
                'key' => 'bfw_points_referral_invite',
                'value' => trim($refere_data_two->ID),
                'compare' => '==',
            ),
        );
           $refere_data_two_two += count(get_users($argsatwo));
        }/*Считаем второй уровень*/
}
        $sumordersforreferral = $val['sum-orders-for-referral'] ?? 0.0;

$total = (new BfwPoints)->getSumUserOrders($userid);
 /*Сумма всех покупок округленная до целого*/


        if ($total > $sumordersforreferral or $total == $sumordersforreferral) {
            if(empty($get_referral)){
                $referral .= '<div class="bonus_computy_account bfw-account_referral"><span class="title_bca">' . __('Referral link generated. Please refresh the page.',
                    'bonus-for-woo') . '</span></div>';
            }else{
                $referral .= '<div class="bonus_computy_account bfw-account_referral"><span class="title_bca">' . __('My referral link',
                    'bonus-for-woo') . ':</span> <code id="code_referal" class="value_bca">' . esc_url(site_url() . '?bfwkey=' . $get_referral) . '</code> <span  title="'.__('Copy link', 'bonus-for-woo').'"  id="copy_referal"></span><span id="copy_good"></span> </div>';
            $url = esc_url(site_url() . '?bfwkey=' . $get_referral);
            $title = get_bloginfo('name');
            $description = get_bloginfo('description');
                $referral .= '<div class="bfw_social_links">';

            if (!empty($val['ref-social-vk'])) {
                $referral .= '<a target="_blank" href="https://vk.com/share.php?url=' . $url . '&title=' . $title . '&description=' . $description . '&noparse=true" class="bfw_social_link_item bfw_ref_icon_vk"></a>';
            }
            if (!empty($val['ref-social-fb'])) {
                $referral .= '<a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' . $url . '" class="bfw_social_link_item bfw_ref_icon_fb"></a>';
            }
            if (!empty($val['ref-social-tw'])) {
                $referral .= '<a target="_blank" href="https://twitter.com/share?url=' . $url . '" class="bfw_social_link_item bfw_ref_icon_tw"></a>';
            }
            if (!empty($val['ref-social-tg'])) {
                $referral .= '<a class="bfw_social_link_item bfw_ref_icon_tg" target="_blank" href="https://telegram.me/share/url?url=' . $url . '&text=' . $title . ' ' . $description . '"></a>';
            }
            if (!empty($val['ref-social-whatsapp'])) {
                $referral .= '<a class="bfw_social_link_item bfw_ref_icon_whatsapp" target="_blank" href="https://api.whatsapp.com/send?text=' . urlencode($url) . '" data-action="share/whatsapp/share"></a>';
            }
            if (!empty($val['ref-social-viber'])) {
                $referral .= '<a class="bfw_social_link_item bfw_ref_icon_viber" target="_blank" href="viber://forward?text=' . $url . '"></a>';
            }

                $referral .= '</div>';
                $referral .= '<div class="bonus_computy_account"><span class="title_bca">' . __('I invited',
                    'bonus-for-woo') . ':</span> <span class="value_bca">' . count($refere_data) . ' ' . __('people',
                    'bonus-for-woo') . '</span></div>';

if (!empty($val['level-two-referral'])) {
    $referral .= '<div class="bonus_computy_account"><span class="title_bca">' . __('My friends invited',
                    'bonus-for-woo') . ':</span> <span class="value_bca">' . $refere_data_two_two . ' ' . __('people',
                    'bonus-for-woo') . '</span></div>'; /*Выводим количество приглашенных второго уровня*/
                    }

            }


        } else {
            $ostalos = $sumordersforreferral - $total;
            $referral .= '<small class="remaining-amount">' . __('To start using the referral system, you need to buy goods for ',
                    'bonus-for-woo') . ' ' . $ostalos . ' ' . get_woocommerce_currency_symbol() . '</small>';

        }

    }
    }
    return $referral;
}
}


/*-------Создаем ссылку в меню woocommerce account бонусная система-------*/
add_filter('woocommerce_account_menu_items', 'bfwoo_bonuses_link', 25);
function bfwoo_bonuses_link($menu_links):array
{
    $val = get_option('bonus_option_name');
         $poryadok = $val['poryadok-in-account'] ?? 2;
        $title_page = $val['title-on-account'] ?? __('Bonus page', 'bonus-for-woo');
        $menu_links = array_slice($menu_links, 0, $poryadok,
                true) + array('bonuses' => $title_page) + array_slice($menu_links, $poryadok,
                null, true);
        $menu_links['bonuses'] = $title_page;

    return $menu_links;
}
/*-------Создаем ссылку в меню woocommerce account бонусная система-------*/


/*  * История начисления баллов * */
add_action( 'bfw_account_hystory', 'bfw_account_hystory' );
if ( ! function_exists( 'bfw_account_hystory' ) ) {
    function bfw_account_hystory():void {
         $val = get_option('bonus_option_name');
 if (empty($val['hystory-hide'])) {
     require_once ('datatable.php');
         /*История начислений баллов клиента*/
        (new BfwHistory)->getHistory(get_current_user_id());
    }
	}
}

add_action( 'bfw_account_rulles', 'bfw_account_rulles' );
add_shortcode('link_on_rulles','bfw_account_rulles');
      function bfw_account_rulles():void {
      $val = get_option('bonus_option_name');
     if(!empty($val['rulles_url'])){
        echo '<a class="bfw_link_rulles" href="'.$val['rulles_url'].'">'.$val['rulles_value'].'</a>';

 }}


/*-------Добавляем конечную точку bonuses-------*/
add_action('init', 'bfwoo_bonuses_add_endpoint', 25);
function bfwoo_bonuses_add_endpoint():void
{
    add_rewrite_endpoint('bonuses', EP_PAGES);
}
/*-------Добавляем конечную точку bonuses-------*/


/*-------Добавляем контент на страницу бонусов-------*/
add_action('woocommerce_account_bonuses_endpoint', 'bfwoo_computy_content', 25);
function bfwoo_computy_content():void
{
/*Если есть шаблон в теме, то используем его*/
 if (file_exists(get_stylesheet_directory() . '/bonus-for-woo/account.php')) {
get_template_part( 'bonus-for-woo/account' );
} else {
require_once(BONUS_COMPUTY_PLUGIN_DIR . '/templates/account.php');
}

}
/*-------Добавляем контент на страницу бонусов-------*/