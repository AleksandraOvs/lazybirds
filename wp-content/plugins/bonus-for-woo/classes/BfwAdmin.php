<?php

/**
 * Class BfwAdmin
 *
 * @version 3.4.2
 */
class BfwAdmin
{
   public static function init(): void
   {
        /* Инициализируем меню в админке*/
        add_action('admin_menu', array('BfwAdmin', 'add_admin_menu'));

        /*Загружаем скрипты и стили*/
        add_action('admin_enqueue_scripts', array('BfwAdmin', 'load_scripts'));
        /*Вывод настроек в меню*/
        add_action('admin_init', array('BfwAdmin', 'plugin_settings'));

        /*------------Добавление поля в профиле клиента для администратора------------*/
        add_action('show_user_profile', array('BfwAdmin', 'bfwoo_add_bonus_in_user_profile'));
        add_action('edit_user_profile', array('BfwAdmin', 'bfwoo_add_bonus_in_user_profile'));

        /*----Добавление поля на странице wp-admin/users.php----*/
        add_filter('manage_users_sortable_columns', array('BfwAdmin', 'bfwoo_sortable_cake_column'), 10, 1);
       add_filter('manage_users_sortable_columns', array('BfwAdmin', 'bfwoo_sortable_cake_column_status'), 10, 1);
        /* order by*/
        add_action('pre_get_users', array('BfwAdmin', 'bfwoo_action_pre_get_users'), 10, 1);
        add_filter('manage_users_columns', array('BfwAdmin', 'bfwoo_add_new_user_column_bonus'));
       add_filter('manage_users_columns', array('BfwAdmin', 'bfwoo_add_new_user_column_bonus_status'));

        add_filter('manage_users_custom_column', array('BfwAdmin', 'bfwoo_add_new_user_column_content'), 10, 3);
       add_filter('manage_users_custom_column', array('BfwAdmin', 'bfwoo_add_new_user_column_content_status'), 10, 3);

        /* Сохранение изменений в профиле клиента*/
        add_action('personal_options_update', array('BfwAdmin', 'bfwoo_computy_input_points_add'));
        add_action('edit_user_profile_update', array('BfwAdmin', 'bfwoo_computy_input_points_add'));

        add_action('woocommerce_screen_ids', array('BfwAdmin', 'set_screen_id'));


    }


    public static function set_screen_id($screen)
    {
        $screen[] = 'woocommerce_page_bonus_for_woo-plugin-options';
        return $screen;
    }


    /*------------Добавление поля в профиле клиента для администратора------------*/
    public static function bfwoo_add_bonus_in_user_profile($user): void
    {

        ?>
        <hr>
        <div class="user_profile_bfw">
            <h2><?php echo __('User bonus points', 'bonus-for-woo'); ?></h2>

            <?php
            $roles = (new BfwRoles)->getRole($user->ID);
            echo '<h2>'.__('Status', 'bonus-for-woo').': '.$roles['name'].'</h2>';

            /*Обработчик удаления записи истории начисления баллов*/
            if (isset($_POST['bfw_delete_post_history_points'])) {
                (new BfwHistory)->deleteHistoryId(sanitize_text_field($_POST['bfw_delete_post_history_points']));
                echo '<div id="message" class="notice notice-warning is-dismissible">
	<p>' . __('deleted', 'bonus-for-woo') . '.</p></div>';
            }
            /*Обработчик удаления записи истории начисления баллов*/

            /*Обработчик удаления всей истории начисления баллов*/
            if (isset($_GET['bfw_delete_all_post_history_points'])) {
                $delete_history_points = sanitize_text_field($_GET['bfw_delete_all_post_history_points']);
                (new BfwHistory)->clearAllHistoryUser($delete_history_points);


                echo '<div id="message" class="notice notice-warning is-dismissible">
	<p>' . __('Cleared', 'bonus-for-woo') . '.</p>
</div>';
            }
            /*Обработчик удаления всей истории начисления баллов*/






            if ((new BfwRoles)->is_pro()) { ?>
                    <div class="bfw-offline-block"><span class="bfw-help-tip faq" data-tip="<?php echo __('The client will have a new order and earn bonus points', 'bonus-for-woo'); ?>"></span>
                <label for="bfw_offline_order_price"><b><?php esc_html_e('Place an order offline', 'bonus-for-woo'); ?></b> </label>
                <input type="text" id="bfw_offline_order_price" name="bfw_offline_order_price"  placeholder="<?php echo __('Enter amount', 'bonus-for-woo'); ?>">
                <input type="submit" name="submit" id="submit3" class="button button-primary" value="<?php echo __('Add order', 'bonus-for-woo'); ?>">
                    </div>
                <p>
                    <label for="dob"><b><?php esc_html_e('Date of birth', 'bonus-for-woo'); ?></b> </label>
                    <input type="date" class="woocommerce-Input woocommerce-Input--text input-text" name="dob" id="dob"
                           value="<?php echo esc_attr($user->dob); ?>"/>

                    <?php if (isset($user->this_year) and $user->this_year == date('Y')) {
                        echo __('The client received points this year', 'bonus-for-woo');
                    } else {
                        echo __('The client did not receive points this year', 'bonus-for-woo');
                    } ?>
                </p>

                <i style="color: #005ac9"><?php echo __('You can change the number of bonus points.',
                        'bonus-for-woo'); ?>
                </i>
                <p><b><?php echo __('Total bonus points', 'bonus-for-woo'); ?>:</b> <?php
                    $balluser = (new BfwPoints)->getPoints($user->ID);
                    echo $balluser;
                    ?></p>

                <p><label> <?php echo __('change bonus points', 'bonus-for-woo'); ?>
                    <input type="number" name="computy_input_points" value="<?php echo $balluser; ?>"
                           class="regular-text"/></label></p>
                <p><label><textarea style="width: 100%;height: 100px;" name="prichinaizmeneniya"
                             placeholder="<?php echo __('The reason for the change in points. It will be displayed in the client\'s accrual history.',
                                 'bonus-for-woo'); ?>"></textarea></label></p>
                <p><input type="submit" name="submit" id="submit1" class="button button-primary"
                          value="<?php echo __('change', 'bonus-for-woo'); ?>"></p>

            <?php } else { ?>
                <i style="color: #005ac9"><?php echo __('You cannot remove bonus points, but you can add them. Just enter the number of points you want to add.',
                        'bonus-for-woo'); ?>
                </i>
                <p><b><?php echo __('Total bonus points', 'bonus-for-woo'); ?>:</b> <?php
                    $balluser = (new BfwPoints)->getPoints($user->ID);
                    echo esc_attr($balluser);
                    ?></p>

                <p><label><?php echo __('Add bonus points', 'bonus-for-woo'); ?>
                    <input type="number" name="computy_input_points" value="0" class="regular-text"/></label>
                <p><label><textarea style="width: 100%;height: 100px;" name="prichinaizmeneniya"
                             placeholder="<?php echo __('The reason for the change in points. It will be displayed in the client\'s accrual history.',
                                 'bonus-for-woo'); ?>"></textarea></label></p>
                <p><input type="submit" name="submit" id="submit2" class="button button-primary"
                       value="<?php echo __('Add', 'bonus-for-woo'); ?>"></p>
            <?php } ?>



            <?php
            /*история начислений баллов клиента*/
            (new BfwHistory)->getHistory($user->ID);


            $val = get_option('bonus_option_name');
            $referalwork = isset($val['referal-system']) ? intval($val['referal-system']) : 0;

            /*если включена реферальная система*/
            if ((new BfwRoles)->is_pro() AND $referalwork == 1) { ?>
                <h3><?php echo __('Referral system', 'bonus-for-woo'); ?></h3>
                <?php
                $get_referral = get_user_meta($user->ID, 'bfw_points_referral', true);
                $get_referral_invite = get_user_meta($user->ID, 'bfw_points_referral_invite', true);
                /*Сколько людей пригласил*/
                $argsa['meta_query'] = array(
                    array(
                        'key' => 'bfw_points_referral_invite',
                        'value' => trim($user->ID),
                        'compare' => '==',
                    ),
                );
                $refere_data = get_users($argsa);
                foreach ($refere_data as $ref_data_one) {
                    $referral_one_user_name[] = $ref_data_one->user_nicename;
                    $referral_one_id[] = $ref_data_one->ID;
                   // $referral_one_first_name[]->first_name;
                }


                echo __('Referral link',
                        'bonus-for-woo') . ': <code>' . esc_url(site_url() . '?bfwkey=' . $get_referral) . '</code><br>';
                if ($get_referral_invite == 0 or $get_referral_invite == '') {
                    echo '';
                } else {

                    $user_info = get_userdata($get_referral_invite);

                    echo __('Invited by user',
                            'bonus-for-woo') . ': <a href="/wp-admin/user-edit.php?user_id=' . $get_referral_invite . '" >' . $user_info->user_login . '(' . $user_info->first_name . ' ' . $user_info->last_name . ')</a><br>';
                }

                echo __('Invited', 'bonus-for-woo') . ' ' . count($refere_data) . ' ' . __('people', 'bonus-for-woo');


                echo ': ';
                for ($i = 0; $i <= count($refere_data)-1; $i++) {
                    /*Выводим список приглашенных первого уровня*/
                    echo ' <a href="/wp-admin/user-edit.php?user_id='.$referral_one_id[$i].'">'.$referral_one_user_name[$i].'</a>, ';
                }


                if (!empty($val['level-two-referral'])) {
                    /* Считаем второй уровень. */
                    $refere_data_two_two = 0;
                    foreach ($refere_data as $refere_data_two) {
                        $argsatwo['meta_query'] = array(
                            array(
                                'key' => 'bfw_points_referral_invite',
                                'value' => trim($refere_data_two->ID),
                                'compare' => '==',
                            ),
                        );

                        $refere_data_two_two += count(get_users($argsatwo));
                        $ref_data_two =  get_users($argsatwo);
                        foreach ($ref_data_two as $ref_data_twos) {
                            $referral_two_user_name[] = $ref_data_twos->user_nicename;
                            $referral_two_id[] = $ref_data_twos->ID;
                        }


                        /*Считаем второй уровень*/
                    }



                echo '<br>'.__('Invited friends', 'bonus-for-woo'). ' ' .$refere_data_two_two . ' ' . __('people', 'bonus-for-woo');
                    echo ': ';/*Выводим список приглашенных второго уровня*/
                    for ($i = 0; $i <= $refere_data_two_two-1; $i++) {
                        echo ' <a href="/wp-admin/user-edit.php?user_id='.$referral_two_id[$i].'">'.$referral_two_user_name[$i].'</a>, ';
                    }
                }
            } ?>


        </div>
        <hr>
        <?php
    }


    public static function bfwoo_action_pre_get_users($query): void
    {
        if (!is_admin()) {
            return;
        }
        $orderby = $query->get('orderby');
        if ($orderby == __('Bonus points', 'bonus-for-woo')) {
            $query->set('orderby', 'meta_value_num');
            $query->set('meta_key', 'computy_point');
        }
    }


    public static function bfwoo_add_new_user_column_bonus($columns)
    {
        $columns['computy_point'] = __('Bonus points', 'bonus-for-woo');
        return $columns;
    }
    public static function bfwoo_add_new_user_column_bonus_status($columns)
    {
        $columns['bfw_status'] = __('User status', 'bonus-for-woo');
        return $columns;
    }

    public static function bfwoo_add_new_user_column_content($content, $column, $user_id)
    {
        if ('computy_point' === $column) {
            $content = (new BfwPoints)->getPoints($user_id);
        }
        return $content;
    }
    public static function bfwoo_add_new_user_column_content_status($content, $column, $user_id)
    {
        if ('bfw_status' === $column) {
            $content = (new BfwRoles())->getRole($user_id)['name'];
        }
        return $content;
    }


    public static function bfwoo_computy_input_points_add($user_id): void
    {

        if ((new BfwRoles)->is_pro()) {

            /*Обработчик запроса проведения оффлайн-заказа*/
            if (!empty($_POST['bfw_offline_order_price'])) {
               (new BfwPoints)->add_offline_order(sanitize_text_field($_POST['bfw_offline_order_price']),$user_id);
                return;
            }
            /*Обработчик запроса проведения оффлайн-заказа*/


            /*Сохранения дня рождения*/
            if (isset($_POST['dob'])) {
                update_user_meta($user_id, 'dob', sanitize_text_field($_POST['dob']));
            }
            /*Сохранения дня рождения*/


            if(isset($_POST['computy_input_points'])){
                /*При редактировании баллов клиента*/
                $addball =  sanitize_text_field($_POST['computy_input_points']);
                $prichina = sanitize_text_field($_POST['prichinaizmeneniya']);
                if ($prichina == '') {
                    $prichina = __('Not specified.', 'bonus-for-woo');
                }
                $oldpoint = (new BfwPoints)->getPoints($user_id);

                if ($addball > $oldpoint) {
                    $naskoko = $addball - $oldpoint;

                    /*Записываем в историю*/
                    (new BfwHistory)->add_history($user_id, '+', $naskoko, '0', $prichina);
                    /*Записываем в историю*/

                    $val = get_option('bonus_option_name');
                    $title_email = $val['email-change-admin-title'] ?? __('Bonus points have been added to you!', 'bonus-for-woo');

                    $user = get_userdata($user_id);
                    /*Шаблонизатор письма*/

                    $text_email = $val['email-change-admin-text'] ?? '';

                    $get_referral = get_user_meta($user_id, 'bfw_points_referral', true);
                    $text_email_array = array(
                        '[referral-link]' => esc_url(site_url() . '?bfwkey=' . $get_referral),
                        '[user]' => $user->display_name,
                        '[points]' => $naskoko,
                        '[total]' => $addball,
                        '[cause]' => $prichina
                    );
                    $message_email = (new BfwEmail)->template($text_email, $text_email_array);
                    /*Шаблонизатор письма*/

                    /*Отправляем email клиенту*/
                    if (!empty($val['email-change-admin'])) {
                            (new BfwEmail)->getMail($user_id, '', $title_email, $message_email);
                                           }
                    /*Отправляем email клиенту*/
                } elseif ($addball < $oldpoint) {
                    $naskoko = $oldpoint - $addball;

                    /*Записываем в историю*/
                    (new BfwHistory)->add_history($user_id, '-', $naskoko, '0', $prichina);
                    /*Записываем в историю*/

                    $val = get_option('bonus_option_name');
                    $title_email = $val['email-change-admin-title-spisanie'] ?? __('Writing off bonus points', 'bonus-for-woo');

                    $user = get_userdata($user_id);
                    /*Шаблонизатор письма*/

                    $text_email = $val['email-change-admin-text-spisanie'] ?? '';

                    $get_referral = get_user_meta($user_id, 'bfw_points_referral', true);
                    $text_email_array = array(
                        '[referral-link]' => esc_url(site_url() . '?bfwkey=' . $get_referral),
                        '[user]' => $user->display_name,
                        '[points]' => $naskoko,
                        '[total]' => $addball,
                        '[cause]' => $prichina
                    );
                    $message_email = (new BfwEmail)->template($text_email, $text_email_array);
                    /*Шаблонизатор письма*/

                    /*Отправляем email клиенту*/
                    if (!empty($val['email-change-admin'])) {
                     (new BfwEmail)->getMail($user_id, '', $title_email, $message_email);
                                          }
                    /*Отправляем email клиенту*/
                }


                (new BfwPoints)->updatePoints($user_id, $addball);
            }

        }
        else {
            $balluser = (new BfwPoints)->getPoints($user_id);
            if ($_POST['computy_input_points'] > 0) {

                $addball_nopro = sanitize_text_field($_POST['computy_input_points']);
                $addball = $addball_nopro + $balluser;
                $prichina = sanitize_text_field($_POST['prichinaizmeneniya']);
                /*Записываем в историю*/
                (new BfwHistory)->add_history($user_id, '+', $addball_nopro, '0', $prichina);
                /*Записываем в историю*/

                /*Отправляем email клиенту*/
                $title_email = __('Bonus points have been added to you!', 'bonus-for-woo');
                $info_email = sprintf(__('%s bonus points have been added to you.', 'bonus-for-woo'),
                    sanitize_text_field($_POST['computy_input_points']));

                $message_email = '<p>' . $info_email . '</p>';
                $message_email .= '<p>' . __('Cause', 'bonus-for-woo') . ': ' . $prichina . '</p>';
                $message_email .= '<p>' . __('The sum of your bonus points is now',
                        'bonus-for-woo') . ': <b>' . $addball . ' ' . __('points', 'bonus-for-woo') . '</b></p>';
                $val = get_option('bonus_option_name');
                if (!empty($val['email-change-admin'])) {
                    (new BfwEmail)->getMail($user_id, '', $title_email, $message_email);
                }
                /*Отправляем email клиенту*/


                (new BfwPoints)->updatePoints($user_id, $addball);
            }

        }

    }


    /*----Добавление поля на странице wp-admin/users.php----*/
    public static function bfwoo_sortable_cake_column($columns)
    {
        $columns['computy_point'] = __('Bonus points', 'bonus-for-woo');
        return $columns;
    }
    public static function bfwoo_sortable_cake_column_status($columns){
        $columns['bfw_status'] = __('User status', 'bonus-for-woo');
        return $columns;
    }

    public static function bonuses_link($menu_links): array
    {
        $menu_links = array_slice($menu_links, 0, 5, true) + array(
                'bonuses' => __('My bonuses', 'bonus-for-woo')
            ) + array_slice($menu_links, 5, null, true);
        $menu_links['bonuses'] = __('My bonuses', 'bonus-for-woo');
        return $menu_links;
    }


    public static function bonuses_add_endpoint(): void
    {
        add_rewrite_endpoint('bonuses', EP_PAGES);
    }



    /* Инициализируем меню в админке*/
    public static function add_admin_menu(): void
    {

        $home_menu_title = __('Bonus for Woo', 'bonus-for-woo');
        $menu_list_history = __('Bonus points history', 'bonus-for-woo');
        $menu_list_history_title = __('History of bonus points for all customers', 'bonus-for-woo');

        /*Страница с основными настройками*/
        add_menu_page( $home_menu_title, $home_menu_title, 'manage_options', 'bonus_for_woo-plugin-options', array('BfwAdmin', 'bonus_plugin_options'), plugins_url( 'bonus-for-woo/img/coin.svg' ), 30 );

        /* Добавляем в меню историю всех начислений клиентов*/
        add_submenu_page('bonus_for_woo-plugin-options', $menu_list_history_title, $menu_list_history, 'manage_options',
            'bonus-for-woo/index/list_history.php', '', 1);

        /*cтраница статистик*/
        add_submenu_page('bonus_for_woo-plugin-options', __('Bonus points statistic - Bonus for Woo', 'bonus-for-woo'),
              __('Bonus system statistic', 'bonus-for-woo'), 'manage_woocommerce',
            'bonus-for-woo/index/statistic.php', '', 2);

        /*Страница управления купонами*/
        $val = get_option('bonus_option_name');
        if ( (new BfwRoles)->is_pro() AND !empty($val['coupon-system'])) {
            add_submenu_page('bonus_for_woo-plugin-options', __('Bonus points coupons - Bonus for Woo', 'bonus-for-woo'),
                __('Bonus points coupons', 'bonus-for-woo'), 'manage_woocommerce',
                'bonus-for-woo/index/coupons.php', '', 3);
        }
          if(determine_locale()=='ru_RU' OR determine_locale()=='uk-UA'){
        /*Генератор правил и условий*/
        add_submenu_page('bonus_for_woo-plugin-options', __('Rules and Conditions Generator', 'bonus-for-woo'),
              __('Rules and Conditions Generator', 'bonus-for-woo'), 'manage_woocommerce',
            'bonus-for-woo/index/generator.php', '', 4);
          }

    }


    public static function load_scripts(): void
    {
        wp_register_style('bonus-for-woo-computy-style-admin',
            BONUS_COMPUTY_PLUGIN_URL . '_inc/bonus-for-woo-style-admin.css', array(), BONUS_COMPUTY_VERSION);
        wp_enqueue_style('bonus-for-woo-computy-style-admin');

        wp_register_style('slimselectcss', BONUS_COMPUTY_PLUGIN_URL . '_inc/slimselect.min.css', array(),
            BONUS_COMPUTY_VERSION);
        wp_enqueue_style('slimselectcss');

        wp_register_script('bonus-computy-script-admin',
            BONUS_COMPUTY_PLUGIN_URL . '_inc/bonus-computy-script-admin.js',
            array('jquery'), null, true);
        wp_enqueue_script('bonus-computy-script-admin');

        wp_register_script('slimselect', BONUS_COMPUTY_PLUGIN_URL . '_inc/slimselect.min.js',
            array('jquery'), null, true);
        wp_enqueue_script('slimselect');
    }


    /*Вывод настроек в меню*/
    public static function plugin_settings(): void
    {
        register_setting('option_group_bonus', 'bonus_option_name', 'sanitize_callback');
        $trans1 = __('Plugin settings', 'bonus-for-woo');


        $fee_or_coupon = __('Coupon Based Bonus System', 'bonus-for-woo')
    .bfw_help_tip(__('Simplifies the calculation of taxes and deferred payments. Default: commission based.', 'bonus-for-woo'),'danger');
        $trans_write_points_order_status = __('At what order status can points be debited?', 'bonus-for-woo');
        $trans_add_points_order_status= __('At what status of an order can points be awarded?', 'bonus-for-woo')
        .bfw_help_tip(__('Attention! The amount of orders and the setting of user statuses will be calculated according to this order status.', 'bonus-for-woo'),'danger');
        $trans_refunded_points_order_status = __('At what order status will points be returned?', 'bonus-for-woo');
        $trans2 = __('Number of bonus points for a product review', 'bonus-for-woo');
         $trans2 .= bfw_help_tip(__('* If the value is greater than 0, all reviews will have to be manually approved.',
                    'bonus-for-woo')   .' '. __('* Accrued only if the customer bought this product.',
                    'bonus-for-woo'));

        $trans_rulles = __('Link to the terms and conditions.', 'bonus-for-woo');
        $trans_round_points = __('Do not round decimals in points?', 'bonus-for-woo');
        $trans12 = __('Should you display the points that the customer will receive on the product page?',
            'bonus-for-woo');
        $trans12b = __('Should the points that will be returned to the customer be displayed on other pages?',
            'bonus-for-woo');
        $trans12c = __('Do not show the word "up to" before the score.', 'bonus-for-woo').bfw_help_tip(__('The prefix "Up to" is displayed only for unregistered users.', 'bonus-for-woo'));

        $trans14 = __('Writing off points in ordering', 'bonus-for-woo').bfw_help_tip(__('Uncheck the checkbox if the checkout is in the basket.', 'bonus-for-woo'),'danger');
        $trans15 = __('Hide deduction of points for sale items?', 'bonus-for-woo');

        $trans16 = __('Percentage of the order amount that can be spent by the client in points.', 'bonus-for-woo');
        $trans17 = __('Exclude product categories that cannot be purchased with cashback points.', 'bonus-for-woo');
        $transpaymethod = __('Exclude payment method from the bonus system.', 'bonus-for-woo');
        $trans18 = __('Exclude products that cannot be purchased with cashback points.', 'bonus-for-woo');
        $trans19 = __('Points for registration', 'bonus-for-woo');
        $trans20 = __('Accrue cashback on excluded products and categories?', 'bonus-for-woo');
        $trans20a = __('If the client uses points, cashback is not credited.', 'bonus-for-woo');
        $trans21 = '<span style="font-size: 20px;">' . __('Referral system', 'bonus-for-woo') . '</span>';
        $trans_coupon = '<label for="coupon-system" style="font-size: 20px;">' . __('Coupons', 'bonus-for-woo') . '</label>';
        $trans21qty = __('Clear points that the customer wants to deduct when the number of items in the cart changes',
            'bonus-for-woo');
        $trans22 = __('Exclude roles from the bonus system', 'bonus-for-woo');
        $trans23 = __('Don\'t spend points if a coupon is applied?', 'bonus-for-woo').bfw_help_tip(__('When the checkbox is selected, the points cannot be spent, but the cashback will be accrued.', 'bonus-for-woo'));
        $transefees    = __('Ignore coupons and discounts when calculating cashback', 'bonus-for-woo');
        if (function_exists('bfw_help_tip')) {
            $transefees .= bfw_help_tip(__('Cashback will be credited without taking into account discounts.', 'bonus-for-woo'));
        }
        $trans24 = __('Remove cashback for delivery?', 'bonus-for-woo').bfw_help_tip(__('When the checkbox is checked, cashback for delivery will not be credited.', 'bonus-for-woo'));
        $trans25 = __('The minimum order amount to use points.', 'bonus-for-woo');
        $trans26 = __('The percentage of points accrued from the referral\'s order.', 'bonus-for-woo');
        $trans_referral_cashback_two_level = __('The percentage of points accrued from the second level referral\'s order.', 'bonus-for-woo');
        $trans27 = __('Earn points only for the first referral order?', 'bonus-for-woo').bfw_help_tip(__('The referer will not receive points from the second order.', 'bonus-for-woo'));
        $leveltwo = __('Use a two-tier system', 'bonus-for-woo').bfw_help_tip(__('Points will come for those invited by your friends.', 'bonus-for-woo'));

        $trans28 = __('Hide customer points history?', 'bonus-for-woo');
        $trans12a = __('Show points that will be returned to the customer in the cart and checkout?', 'bonus-for-woo');
        $trans29 = __('The Sum of orders after which the referral system will become available to the client.',
            'bonus-for-woo');
        $trans_soc = __('Social links for the referral system', 'bonus-for-woo');
        if (function_exists('bfw_help_tip')) {
            $trans_soc .= bfw_help_tip(__('The icons will appear on the user\'s account page.', 'bonus-for-woo'));
        }
        $buy_balls = __('Product for which 100% cashback is charged.', 'bonus-for-woo');
        $order_start_date = __('From what date to count the amount of orders?', 'bonus-for-woo');
        $birthday = __('Points on your birthday', 'bonus-for-woo');
        if (function_exists('bfw_help_tip')) {
            $birthday .= bfw_help_tip(__('If more than 0 is specified, then the client will have a date entry field in the account settings.',
                'bonus-for-woo'));
        }
        $every_days = __('Daily points for the first login', 'bonus-for-woo');
        if (function_exists('bfw_help_tip')) {
            $every_days .= bfw_help_tip(__('Awarding points to the client for logging in. Charged once a day.', 'bonus-for-woo'));
        }
        $trans_soc2 = __('Show social media links on product page?', 'bonus-for-woo');
        $transclear = __('Remove plugin traces upon activation.', 'bonus-for-woo').bfw_help_tip(__('Attention! This will remove all settings and all user bonus points.', 'bonus-for-woo'),'danger');
        $trans30 = __('The order of the "My bonuses" menu item in the client account.', 'bonus-for-woo');
        $trans_inactive = __('Removing bonus points for inactivity', 'bonus-for-woo');
        $trans_inactive_notice = __('How many days in advance do I have to give notice to deduct points?', 'bonus-for-woo');
        $trans_fill_burn_point_in_account =  __('Do not show in your account how many days of points burning are left.', 'bonus-for-woo');
        register_setting('option_trans_group_bonus', 'bonus_option_name', 'sanitize_callback');
        add_settings_section('bonus_section_id', $trans1, '', 'primer_page_bonus');
        //позднее включим когда время будет все доделать
        if (function_exists('bfw_help_tip')) {
            add_settings_field('bonus_field0', __('Points Label', 'bonus-for-woo') .
                bfw_help_tip(__('First field singular, second plural.', 'bonus-for-woo') ), array('BfwAdmin', 'name_points'), 'primer_page_bonus', 'bonus_section_id');
        } else {
            add_settings_field('bonus_field0', __('Points Label', 'bonus-for-woo'), array('BfwAdmin', 'name_points'),
                'primer_page_bonus', 'bonus_section_id');
        }
        add_settings_field('field_rulles', $trans_rulles, array('BfwAdmin', 'fill_rulles'), 'primer_page_bonus', 'bonus_section_id');

        add_settings_field('field_round_points', $trans_round_points, array('BfwAdmin', 'fill_round_points'), 'primer_page_bonus', 'bonus_section_id');
        add_settings_field('field_write_points_order_status', $trans_write_points_order_status, array('BfwAdmin', 'fill_write_points_order_status'), 'primer_page_bonus',
            'bonus_section_id');

        add_settings_field('field_add_points_order_status', $trans_add_points_order_status, array('BfwAdmin', 'fill_add_points_order_status'), 'primer_page_bonus',
            'bonus_section_id');
        add_settings_field('field_refunded_points_order_status', $trans_refunded_points_order_status, array('BfwAdmin', 'fill_refunded_points_order_status'), 'primer_page_bonus',
            'bonus_section_id');
        add_settings_field('bonus_field1', $trans2, array('BfwAdmin', 'fill_primer_field1'), 'primer_page_bonus',
            'bonus_section_id');


        add_settings_field('my_checkbox_field', $trans12, array('BfwAdmin', 'fill_primer_field12'), 'primer_page_bonus',
            'bonus_section_id');
        add_settings_field('my_checkbox_fieldb', $trans12b, array('BfwAdmin', 'fill_primer_field12b'),
            'primer_page_bonus', 'bonus_section_id');
        add_settings_field('my_checkbox_fieldc', $trans12c, array('BfwAdmin', 'fill_primer_field12c'),
            'primer_page_bonus', 'bonus_section_id');

        add_settings_field('my_checkbox_field12a', $trans12a, array('BfwAdmin', 'fill_primer_field12a'),
            'primer_page_bonus', 'bonus_section_id');

        add_settings_field('my_checkbox_field14', $trans14, array('BfwAdmin', 'fill_primer_field14'),
            'primer_page_bonus', 'bonus_section_id');
        add_settings_field('my_checkbox_field15', $trans15, array('BfwAdmin', 'fill_primer_field15'),
            'primer_page_bonus', 'bonus_section_id');
        add_settings_field('my_checkbox_field23', $trans23, array('BfwAdmin', 'fill_primer_field23'),
            'primer_page_bonus', 'bonus_section_id');
        add_settings_field('my_checkbox_exclude_fees_coupons', $transefees, array('BfwAdmin', 'exclude_fees_coupons'),
            'primer_page_bonus', 'bonus_section_id');
        add_settings_field('my_checkbox_field24', $trans24, array('BfwAdmin', 'fill_primer_field24'),
            'primer_page_bonus', 'bonus_section_id');
        add_settings_field('my_checkbox_fee_or_coupon', $fee_or_coupon, array('BfwAdmin', 'fee_or_coupon'),
            'primer_page_bonus', 'bonus_section_id');

        add_settings_field('my_checkbox_field28', $trans28, array('BfwAdmin', 'fill_primer_field28'),
            'primer_page_bonus', 'bonus_section_id');

        add_settings_field('bonus_field30', $trans30, array('BfwAdmin', 'fill_primer_field30'), 'primer_page_bonus',
            'bonus_section_id');


        register_setting('option_mail_group_bonus', 'bonus_option_name', 'sanitize_callback');
        add_settings_section('bonus_section_id', __('Pro settings', 'bonus-for-woo'), '', 'pro_page_bonus');


        if ((new BfwRoles)->is_pro()) {

            add_settings_field('input_fill_order_start_date', $order_start_date, array('BfwAdmin', 'fill_order_start_date'), 'pro_page_bonus', 'bonus_section_id');

            add_settings_field('input_birthday', $birthday, array('BfwAdmin', 'fill_birthday'), 'pro_page_bonus', 'bonus_section_id');

            add_settings_field('input_every_days', $every_days, array('BfwAdmin', 'every_days'), 'pro_page_bonus', 'bonus_section_id');

            add_settings_field('my_checkbox_field16', $trans16, array('BfwAdmin', 'fill_primer_field16'),
                'pro_page_bonus', 'bonus_section_id');
            add_settings_field('my_checkbox_field_pay_method', $transpaymethod, array('BfwAdmin', 'fill_primer_field_pay_method'),
                'pro_page_bonus', 'bonus_section_id');
            add_settings_field('my_checkbox_field17', $trans17, array('BfwAdmin', 'fill_primer_field17'),
                'pro_page_bonus', 'bonus_section_id');



            add_settings_field('my_checkbox_field18', $trans18, array('BfwAdmin', 'fill_primer_field18'),
                'pro_page_bonus', 'bonus_section_id');
            add_settings_field('my_checkbox_field_buy_balls', $buy_balls, array('BfwAdmin', 'fill_buy_balls'),
                'pro_page_bonus', 'bonus_section_id');

            add_settings_field('my_checkbox_field20', $trans20, array('BfwAdmin', 'fill_primer_field20'),
                'pro_page_bonus', 'bonus_section_id');
            add_settings_field('my_checkbox_field20o', $trans20a, array('BfwAdmin', 'yous_balls_no_cashback_fild'),
                'pro_page_bonus', 'bonus_section_id');

            add_settings_field('my_checkbox_field19', $trans19, array('BfwAdmin', 'fill_primer_field19'),
                'pro_page_bonus', 'bonus_section_id');
            add_settings_field('my_checkbox_field122', $trans22, array('BfwAdmin', 'fill_primer_field22'),
                'pro_page_bonus', 'bonus_section_id');
            add_settings_field('my_checkbox_field125', $trans25, array('BfwAdmin', 'fill_primer_field25'),
                'pro_page_bonus', 'bonus_section_id');
            add_settings_field('my_checkbox_field21qty', $trans21qty, array('BfwAdmin', 'fill_primer_field21qty'),
                'pro_page_bonus', 'bonus_section_id');
            add_settings_field('my_input_inactive', $trans_inactive, array('BfwAdmin', 'fill_inactive'),
                'pro_page_bonus', 'bonus_section_id');
            add_settings_field('my_input_inactive_notice', $trans_inactive_notice, array('BfwAdmin', 'fill_inactive_notice'),
                'pro_page_bonus', 'bonus_section_id');
            add_settings_field('my_input_fill_burn_point_in_account', $trans_fill_burn_point_in_account, array('BfwAdmin', 'fill_burn_point_in_account'),
                'pro_page_bonus', 'bonus_section_id');

            add_settings_field('my_checkbox_field21', $trans21, array('BfwAdmin', 'fill_primer_field21'),
                'pro_page_bonus', 'bonus_section_id');
            $ref = get_option('bonus_option_name');
            if (!empty($ref['referal-system'])) {
                    add_settings_field('my_checkbox_field27', $trans27, array('BfwAdmin', 'fill_primer_field27'),
                        'pro_page_bonus', 'bonus_section_id');
                add_settings_field('my_checkbox_fill_level_two', $leveltwo, array('BfwAdmin', 'fill_level_two'),
                    'pro_page_bonus', 'bonus_section_id');
                    add_settings_field('my_checkbox_field26', $trans26, array('BfwAdmin', 'fill_primer_field26'),
                        'pro_page_bonus', 'bonus_section_id');
                if (!empty($ref['level-two-referral'])) {
                    add_settings_field('my_cashback_two_level', $trans_referral_cashback_two_level, array('BfwAdmin', 'fill_referal_cashback_two_level'),
                        'pro_page_bonus', 'bonus_section_id');
                }
                    add_settings_field('my_checkbox_field29', $trans29, array('BfwAdmin', 'fill_primer_field29'),
                        'pro_page_bonus', 'bonus_section_id');
                    add_settings_field('social_icons', $trans_soc, array('BfwAdmin', 'fill_primer_field_social'),
                        'pro_page_bonus', 'bonus_section_id');
                    add_settings_field('social_icons2', $trans_soc2,
                        array('BfwAdmin', 'fill_primer_field_social_on_page'), 'pro_page_bonus', 'bonus_section_id');

            }
            add_settings_field('my_checkbox_coupon', $trans_coupon, array('BfwAdmin', 'fill_primer_coupons'),
                'pro_page_bonus', 'bonus_section_id');


        }

        add_settings_field('my_checkbox_clear', $transclear, array('BfwAdmin', 'fill_primer_clear'),
            'primer_page_bonus', 'bonus_section_id');




        $trans3 = __('The title in the customers account.', 'bonus-for-woo');
        $trans3h = __('Bonus point history heading.', 'bonus-for-woo');
        $trans4 = __('"My status" in the customer account.', 'bonus-for-woo');
        $trans5 = __('"My cashback percentage" in the customer account.', 'bonus-for-woo');
        $trans6 = __('"My bonus points" in the customer account.', 'bonus-for-woo');
        $trans7 = __('Bonus text in the shopping cart.', 'bonus-for-woo');
        $trans8 = __('"Use points" text in the shopping cart.', 'bonus-for-woo');
        $trans9 = __('"Bonus points" text in the shopping cart.', 'bonus-for-woo');
        $trans10 = __('"Remove points" text on button in the shopping cart.', 'bonus-for-woo');
        $trans_offline = __('Name of the offline product in the buyer\'s order', 'bonus-for-woo');
        $trans11 = __('Information about the remaining amount for the transition to another status.', 'bonus-for-woo');

        add_settings_section('bonus_section_id', __('Translate', 'bonus-for-woo'), '', 'trans_page_bonus');
        add_settings_field('bonus_field2', $trans3, array('BfwAdmin', 'fill_primer_field2'), 'trans_page_bonus',
            'bonus_section_id');
        add_settings_field('bonus_field2h', $trans3h, array('BfwAdmin', 'fill_primer_field2h'), 'trans_page_bonus',
            'bonus_section_id');
        add_settings_field('bonus_field4', $trans4, array('BfwAdmin', 'fill_primer_field4'), 'trans_page_bonus',
            'bonus_section_id');
        add_settings_field('bonus_field5', $trans5, array('BfwAdmin', 'fill_primer_field5'), 'trans_page_bonus',
            'bonus_section_id');
        add_settings_field('bonus_field6', $trans6, array('BfwAdmin', 'fill_primer_field6'), 'trans_page_bonus',
            'bonus_section_id');
        add_settings_field('bonus_field11', $trans11, array('BfwAdmin', 'fill_primer_field11'), 'trans_page_bonus',
            'bonus_section_id');
        add_settings_field('bonus_field7', $trans7, array('BfwAdmin', 'fill_primer_field7'), 'trans_page_bonus',
            'bonus_section_id');
        add_settings_field('bonus_field8', $trans8, array('BfwAdmin', 'fill_primer_field8'), 'trans_page_bonus',
            'bonus_section_id');
        add_settings_field('bonus_field9', $trans9, array('BfwAdmin', 'fill_primer_field9'), 'trans_page_bonus',
            'bonus_section_id');
        add_settings_field('bonus_field10', $trans10, array('BfwAdmin', 'fill_primer_field10'), 'trans_page_bonus',
            'bonus_section_id');
        add_settings_field('bonus_field12', $trans_offline, array('BfwAdmin', 'fill_title_product_offline_order'), 'trans_page_bonus',
            'bonus_section_id');

        /*add_settings_field( 'bonus_field10a', __('Translations for the history of bonus accrual'), array( 'Bonus_Admin', 'fill_primer_field166' ), 'trans_page_bonus', 'bonus_section_id' );*/


        /*emails*/
        $trans_e = __('Email settings', 'bonus-for-woo');
        $trans_e0 = __('Use your send method', 'bonus-for-woo').bfw_help_tip(__('Notifications will no longer be sent to customers by email. Check out the setup in the manual.', 'bonus-for-woo'),'danger');
        $trans_e1 = __('Headings and styling:', 'bonus-for-woo');
        $trans_e2 = __('Changes to points by the administrator', 'bonus-for-woo');
        $trans_e3 = __('When registering', 'bonus-for-woo');
        $trans_e4 = __('When the order is confirmed', 'bonus-for-woo');
        $trans_e5 = __('When processing an order by an administrator(paid, returned)', 'bonus-for-woo');
        $trans_e6 = __('Product Review Notice(approval, rejection)', 'bonus-for-woo');
        $trans_e7 = __('When the status changes', 'bonus-for-woo');
        $fill_email_on_birthday = __('When are points earned on your birthday', 'bonus-for-woo');
        $fill_email_on_remove_points = __('About the imminent deletion of points.', 'bonus-for-woo');
        $fill_email_on_every_day =  __('About earning points for signing in.', 'bonus-for-woo');
        register_setting('option_mail_group_bonus', 'bonus_option_name', 'sanitize_callback');

        add_settings_section('bonus_section_id', $trans_e, '', 'mail_page_bonus');

          add_settings_field('bonus_field_email_0', $trans_e0, array('BfwAdmin', 'fill_email_my_methode'), 'mail_page_bonus',
              'bonus_section_id');
        add_settings_field('bonus_field_email_1', $trans_e1, array('BfwAdmin', 'fill_email_1'), 'mail_page_bonus',
            'bonus_section_id');
        add_settings_field('bonus_field_email_2', $trans_e2, array('BfwAdmin', 'fill_email_2'), 'mail_page_bonus',
            'bonus_section_id');
        add_settings_field('bonus_field_email_2t',
            __('Email template when points are added by admin', 'bonus-for-woo') . '<br>' . __('Use shortcodes:',
                'bonus-for-woo') . '<br> [user]-' . __('Client name',
                'bonus-for-woo') . '<br>[total]-' . __('Total points for the client',
                'bonus-for-woo') . '<br>[points]-' . __('Number of points',
                'bonus-for-woo') . '<br>[cause]-' . __('Cause',
                'bonus-for-woo') . '<br>[referral-link]-' . __('Referral link', 'bonus-for-woo'),
            array('BfwAdmin', 'fill_email_2_template'), 'mail_page_bonus', 'bonus_section_id');
        if ((new BfwRoles)->is_pro()) {
            add_settings_field('bonus_field_email_2ts',
                __('Email template when writing off points by admin', 'bonus-for-woo') . '<br>' . __('Use shortcodes:',
                    'bonus-for-woo') . '<br> [user]-' . __('Client name',
                    'bonus-for-woo') . '<br>[total]-' . __('Total points for the client',
                    'bonus-for-woo') . '<br>[points]-' . __('Number of points',
                    'bonus-for-woo') . '<br>[cause]-' . __('Cause',
                    'bonus-for-woo') . '<br>[referral-link]-' . __('Referral link', 'bonus-for-woo'),
                array('BfwAdmin', 'fill_email_2_template2'), 'mail_page_bonus', 'bonus_section_id');
        }

        if ((new BfwRoles)->is_pro()) {
            add_settings_field('bonus_field_email_3', $trans_e3, array('BfwAdmin', 'fill_email_3'), 'mail_page_bonus',
                'bonus_section_id');
            add_settings_field('bonus_field_email_3t',
                __('New user registration email template', 'bonus-for-woo') . '<br>' . __('Use shortcodes:',
                    'bonus-for-woo') . '<br> [user]-' . __('Client name',
                    'bonus-for-woo') . '<br>[total]-' . __('Total points for the client',
                    'bonus-for-woo') . '<br>[points]-' . __('Number of points',
                    'bonus-for-woo') . '<br>[cause]-' . __('Cause',
                    'bonus-for-woo') . '<br>[referral-link]-' . __('Referral link', 'bonus-for-woo'),
                array('BfwAdmin', 'fill_email_3_template'), 'mail_page_bonus', 'bonus_section_id');
        }
        add_settings_field('bonus_field_email_4', $trans_e4, array('BfwAdmin', 'fill_email_4'), 'mail_page_bonus',
            'bonus_section_id');
        add_settings_field('bonus_field_email_4t', __('Template of the letter when the customer confirmed the order',
                'bonus-for-woo') . '<br>' . __('Use shortcodes:', 'bonus-for-woo') . '<br>[order]-' . __('Order',
                'bonus-for-woo') . '<br> [user]-' . __('Client name',
                'bonus-for-woo') . '<br>[total]-' . __('Total points for the client',
                'bonus-for-woo') . '<br>[points]-' . __('Number of points',
                'bonus-for-woo') . '<br>[cause]-' . __('Cause',
                'bonus-for-woo') . '<br>[referral-link]-' . __('Referral link', 'bonus-for-woo'),
            array('BfwAdmin', 'fill_email_4_template'), 'mail_page_bonus', 'bonus_section_id');

        add_settings_field('bonus_field_email_5', $trans_e5, array('BfwAdmin', 'fill_email_5'), 'mail_page_bonus',
            'bonus_section_id');
        add_settings_field('bonus_field_email_5t',
            __('Email template, when order status is complete', 'bonus-for-woo') . '<br>' . __('Use shortcodes:',
                'bonus-for-woo') . '<br> [user]-' . __('Client name',
                'bonus-for-woo') . '<br>[total]-' . __('Total points for the client',
                'bonus-for-woo') . '<br>[points]-' . __('Points amount', 'bonus-for-woo') . '<br>[order]-' . __('Order',
                'bonus-for-woo') . '<br>[referral-link]-' . __('Referral link', 'bonus-for-woo'),
            array('BfwAdmin', 'fill_email_5_template'), 'mail_page_bonus', 'bonus_section_id');
        add_settings_field('bonus_field_email_5tr', __('Email template when order status is complete from referral.',
                'bonus-for-woo') . '<br>' . __('Use shortcodes:', 'bonus-for-woo') . '<br> [user]-' . __('Client name',
                'bonus-for-woo') . '<br>[total]-' . __('Total points for the client',
                'bonus-for-woo') . '<br>[points]-' . __('Points amount', 'bonus-for-woo') . '<br>[cause]-' . __('Cause',
                'bonus-for-woo') . '<br>[referral-link]-' . __('Referral link', 'bonus-for-woo'),
            array('BfwAdmin', 'fill_email_5_template_ref'), 'mail_page_bonus', 'bonus_section_id');

        add_settings_field('bonus_field_email_5t2',
            __('Email template, when order status is returned', 'bonus-for-woo') . '<br>' . __('Use shortcodes:',
                'bonus-for-woo') . '<br> [user]-' . __('Client name',
                'bonus-for-woo') . '<br>[total]-' . __('Total points for the client',
                'bonus-for-woo') . '<br>[cashback]-' . __('Cashback amount',
                'bonus-for-woo') . '<br>[points]-' . __('Number of points',
                'bonus-for-woo') . '<br>[order]-' . __('Order',
                'bonus-for-woo') . '<br>[referral-link]-' . __('Referral link', 'bonus-for-woo'),
            array('BfwAdmin', 'fill_email_5_template2'), 'mail_page_bonus', 'bonus_section_id');

        add_settings_field('bonus_field_email_6', $trans_e6, array('BfwAdmin', 'fill_email_6'), 'mail_page_bonus',
            'bonus_section_id');
        add_settings_field('bonus_field_email_6t',
            __('Email template when leaving a product review', 'bonus-for-woo') . '<br>' . __('Use shortcodes:',
                'bonus-for-woo') . '<br> [user]-' . __('Client name',
                'bonus-for-woo') . '<br>[total]-' . __('Total points for the client',
                'bonus-for-woo') . '<br>[points]-' . __('Number of points',
                'bonus-for-woo') . '<br>[cause]-' . __('Сause',
                'bonus-for-woo') . '<br>[referral-link]-' . __('Referral link', 'bonus-for-woo'),
            array('BfwAdmin', 'fill_email_6_template'), 'mail_page_bonus', 'bonus_section_id');


        add_settings_field('bonus_field_email_7', $trans_e7, array('BfwAdmin', 'fill_email_7'), 'mail_page_bonus',
            'bonus_section_id');
        add_settings_field('bonus_field_email_7t',
            __('Template for a letter when a client status changes', 'bonus-for-woo') . '<br>' . __('Use shortcodes:',
                'bonus-for-woo') . '<br> [user]-' . __('Client name',
                'bonus-for-woo') . '<br>[role]-' . __('User status',
                'bonus-for-woo') . '<br>[cashback]-' . __('Cashback percentage',
                'bonus-for-woo') . '<br>[referral-link]-' . __('Referral link', 'bonus-for-woo'),
            array('BfwAdmin', 'fill_email_7_template'), 'mail_page_bonus', 'bonus_section_id');

        add_settings_field('bonus_field_email_birthday', $fill_email_on_birthday,
            array('BfwAdmin', 'fill_email_on_birthday'), 'mail_page_bonus', 'bonus_section_id');




        add_settings_field('bonus_field_email_birthday_text',
            __('Letter template when points are awarded on a birthday',
                'bonus-for-woo') . '<br>' . __('Use shortcodes:', 'bonus-for-woo') . '<br> [user]-' . __('Client name',
                'bonus-for-woo') . '<br>[points_for_birthday]-' . __('Points on your birthday', 'bonus-for-woo'),
            array('BfwAdmin', 'fill_email_on_birthday_text'), 'mail_page_bonus', 'bonus_section_id');


         add_settings_field('bonus_field_email_on_remove_points', $fill_email_on_remove_points,
             array('BfwAdmin', 'fill_email_on_remove_points'), 'mail_page_bonus', 'bonus_section_id');

        add_settings_field('bonus_field_email_remove_points_text',
            __('Letter template for deducting points soon',
                'bonus-for-woo') . '<br>' . __('Use shortcodes:', 'bonus-for-woo') . '<br> [user]-' . __('Client name',
                'bonus-for-woo') . '<br>[points]-' . __('Number of points', 'bonus-for-woo') .
            '<br>[days]-' . __('Days before deducting points', 'bonus-for-woo'),
            array('BfwAdmin', 'fill_email_inactive_notice_text'), 'mail_page_bonus', 'bonus_section_id');


        add_settings_field('bonus_field_email_on_every_day', $fill_email_on_every_day,
            array('BfwAdmin', 'fill_email_on_every_day'), 'mail_page_bonus', 'bonus_section_id');

        add_settings_field('bonus_field_email_every_day_text',
            __('Letter template for about earning points for signing in.',
                'bonus-for-woo') . '<br>' . __('Use shortcodes:', 'bonus-for-woo') . '<br> [user]-' . __('Client name',
                'bonus-for-woo') . '<br>[points]-' . __('Number of points', 'bonus-for-woo') .
            '<br>[total]-' . __('Total points for the client', 'bonus-for-woo'),
            array('BfwAdmin', 'fill_email_every_day_text'), 'mail_page_bonus', 'bonus_section_id');
        /*emails*/
    }

    /*проверка на про*/
    public static function bfw_search_pro(): void
    {

        if((new BfwRoles)->is_pro()){$pro='pro';}else{$pro='nepro';}

        $get = array(
            'key'  => sanitize_text_field($_POST['bonus-for-woo-pro']),
            'site' => get_site_url(),
            'pro' => $pro
        );
        $curl = curl_init('https://computy.ru/API/api.php?' . http_build_query($get));
        curl_setopt($curl,    CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($curl);
        $data = json_decode($response, true);
        curl_close($curl);
        if($data['status']!='OK'){
            function author_admin_notice_bfwpro(): void
            {
                echo '<div class="notice notice-info is-dismissible">
          <p>'. __('The Pro version of the Bonus for Woo plugin has not been confirmed. Reactivate the Pro version. If you have any difficulties, write to us at info@computy.ru', 'bonus-for-woo').'</p>
         </div>';

            }
            add_action('admin_notices', 'author_admin_notice_bfwpro');
            update_option( 'bonus-for-woo-pro', 'noactive' );
        }
    }
    ## Социальные ссылки для рефералов
    public static function fill_primer_field_social(): void
    {
        $val = get_option('bonus_option_name');
        $checkedvk = isset($val['ref-social-vk']) ? "checked" : "";
        $checkedfb = isset($val['ref-social-fb']) ? "checked" : "";
        $checkedtw = isset($val['ref-social-tw']) ? "checked" : "";
        $checkedtg = isset($val['ref-social-tg']) ? "checked" : "";
        $checkedwhatsapp = isset($val['ref-social-whatsapp']) ? "checked" : "";
        $checkedviber = isset($val['ref-social-viber']) ? "checked" : "";
        $checkcopy = isset($val['ref-copy']) ? "checked" : "";
        ?>
        <input id="ref-social-vk" name="bonus_option_name[ref-social-vk]" type="checkbox"
               value="1" <?php echo $checkedvk; ?>>
        <label for="ref-social-vk" class="ref-social ref-social-vk">VK</label>

        <input id="ref-social-fb" name="bonus_option_name[ref-social-fb]" type="checkbox"
               value="1" <?php echo $checkedfb; ?>>
        <label for="ref-social-fb" class="ref-social ref-social-fb">FACEBOOK</label>

        <input id="ref-social-tw" name="bonus_option_name[ref-social-tw]" type="checkbox"
               value="1" <?php echo $checkedtw; ?>>
        <label for="ref-social-tw" class="ref-social ref-social-tw">TWITTER</label>

        <input id="ref-social-tg" name="bonus_option_name[ref-social-tg]" type="checkbox"
               value="1" <?php echo $checkedtg; ?>>
        <label for="ref-social-tg" class="ref-social ref-social-tg">TELEGRAM</label>

        <input id="ref-social-whatsapp" name="bonus_option_name[ref-social-whatsapp]" type="checkbox"
               value="1" <?php echo $checkedwhatsapp; ?>>
        <label for="ref-social-whatsapp" class="ref-social ref-social-whatsapp">WHATSAPP</label>

        <input id="ref-social-viber" name="bonus_option_name[ref-social-viber]" type="checkbox"
               value="1" <?php echo $checkedviber; ?>>
        <label for="ref-social-viber" class="ref-social ref-social-viber">VIBER</label>

        <input id="ref-copy" name="bonus_option_name[ref-copy]" type="checkbox"
               value="1" <?php echo $checkcopy; ?>>
        <label for="ref-copy" class="ref-social ref-copy">COPY</label>
    <?php }


    ## Социальные ссылки для рефералов на странице продукта
    public static function fill_primer_field_social_on_page(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['ref-links-on-single-page']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[ref-links-on-single-page]" type="checkbox" value="1" <?php echo $checked; ?>>

    <?php }

## Поддержка купонов
    public static function fill_primer_coupons(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['coupon-system']) ? "checked" : "";
        ?>
        <input id="coupon-system" name="bonus_option_name[coupon-system]" type="checkbox" value="1" <?php echo $checked; ?>>

    <?php }


    ## Свой метод отправки формы
    public static function fill_email_my_methode(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['email-my-methode']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[email-my-methode]" type="checkbox" value="1" <?php echo $checked; ?>>
    <?php }

## Заполняем опцию fill_email_1
    public static function fill_email_1(): void
    { ?>
        <p><?php echo __('In the <a href="/wp-admin/admin.php?page=wc-settings&tab=email" target="_blank">settings woocommerce</a>, it is indicated from whom the letter will be sent. If not specified, the general settings <a href="/wp-admin/options-general.php" target="_blank">from the page</a> will be used. All email design in settings Woocommerce. The notification text is generated automatically.',
                'bonus-for-woo'); ?></p>
    <?php }

    ## Отправлять ли письмо при изменениях баллов админом
    public static function fill_email_2(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['email-change-admin']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[email-change-admin]" type="checkbox" value="1" <?php echo $checked; ?>>
    <?php }

    ## Заполняем шаблон при начислении баллов админом
    public static function fill_email_2_template(): void
    {
        $val = get_option('bonus_option_name');
        $value = $val['email-change-admin-title'] ?? __('Bonus points have been added to you!', 'bonus-for-woo');

        $text_email = '<p>' . __('Hello', 'bonus-for-woo') . ', [user]</p>
      <p>' . sprintf(__('%s bonus points have been added to you.', 'bonus-for-woo'), '[points]') . '</p>
      <p>' . __('Cause', 'bonus-for-woo') . ': [cause]</p> 
      <p>' . __('The sum of your bonus points is now', 'bonus-for-woo') . ': <b>[total] ' . __('points',
                'bonus-for-woo') . '</b></p>';
        ?>
        <div class="label-input-mail"><?php echo __('Email header', 'bonus-for-woo'); ?></div>
        <input style="width:100%"
               placeholder="<?php echo __('Bonus points have been added to you!', 'bonus-for-woo'); ?>" type="text"
               name="bonus_option_name[email-change-admin-title]" value="<?php echo esc_attr($value) ?>"/>
        <div class="label-editor-mail"><?php echo __('Letter template', 'bonus-for-woo'); ?></div>
        <?php
        $content = $val['email-change-admin-text'] ?? $text_email;
        $editor_id = 'email-change-admin-text';
        $settings = array('media_buttons' => true, 'textarea_name' => 'bonus_option_name[email-change-admin-text]');
        wp_editor($content, $editor_id, $settings);

    }

    ## Заполняем шаблон при списании баллов админом
    public static function fill_email_2_template2(): void
    {
        $val = get_option('bonus_option_name');
        $value = $val['email-change-admin-title-spisanie'] ?? __('Writing off bonus points', 'bonus-for-woo');

        $text_email = '<p>' . __('Hello', 'bonus-for-woo') . ', [user]</p>
      <p>' . sprintf(__('%s bonus points were deducted from you.', 'bonus-for-woo'), '[points]') . '</p>
      <p>' . __('Cause', 'bonus-for-woo') . ': [cause]</p> 
      <p>' . __('The sum of your bonus points is now', 'bonus-for-woo') . ': <b>[total] ' . __('points',
                'bonus-for-woo') . '</b></p>';
        ?>
        <div class="label-input-mail"><?php echo __('Email header', 'bonus-for-woo'); ?></div>
        <input style="width:100%" placeholder="<?php echo __('Writing off bonus points', 'bonus-for-woo'); ?>"
               type="text" name="bonus_option_name[email-change-admin-title-spisanie]"
               value="<?php echo esc_attr($value) ?>"/>
        <div class="label-editor-mail"><?php echo __('Letter template', 'bonus-for-woo'); ?></div>
        <?php
        $content = $val['email-change-admin-text-spisanie'] ?? $text_email;
        $editor_id = 'email-change-admin-text-spisanie';
        $settings = array(
            'media_buttons' => true,
            'textarea_name' => 'bonus_option_name[email-change-admin-text-spisanie]'
        );
        wp_editor($content, $editor_id, $settings);

    }

    ## Отправлять ли письмо о начислении баллов за регистрацию
    public static function fill_email_3(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['email-when-register']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[email-when-register]" type="checkbox" value="1" <?php echo $checked; ?>>
    <?php }

    ## Заполняем шаблон письма при регистрации
    public static function fill_email_3_template(): void
    {
        $val = get_option('bonus_option_name');
        $value = $val['email-when-register-title'] ?? __('Bonus points have been added to you!', 'bonus-for-woo');

        $text_email = '<p>' . __('Hello', 'bonus-for-woo') . ', [user]</p>
      <p>' . sprintf(__('%s bonus points have been added to you.', 'bonus-for-woo'), '[points]') . '</p>
      <p>' . __('Cause', 'bonus-for-woo') . ': [cause]</p> 
      <p>' . __('The sum of your bonus points is now', 'bonus-for-woo') . ': <b>[total] ' . __('points',
                'bonus-for-woo') . '</b></p>';
        ?>
        <div class="label-input-mail"><?php echo __('Email header', 'bonus-for-woo'); ?></div>
        <input style="width:100%"
               placeholder="<?php echo __('Bonus points have been added to you!', 'bonus-for-woo'); ?>" type="text"
               name="bonus_option_name[email-when-register-title]" value="<?php echo esc_attr($value) ?>"/>
        <div class="label-editor-mail"><?php echo __('Letter template', 'bonus-for-woo'); ?></div>
        <?php
        $content = $val['email-when-register-text'] ?? $text_email;
        $editor_id = 'email-when-register-text';
        $settings = array('media_buttons' => true, 'textarea_name' => 'bonus_option_name[email-when-register-text]');
        wp_editor($content, $editor_id, $settings);

    }

    ## Отправлять ли письмо о начислении баллов когда заказ подтвержден клиентом
    public static function fill_email_4(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['email-when-order-confirm']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[email-when-order-confirm]" type="checkbox" value="1" <?php echo $checked; ?>>
    <?php }

## Заполняем шаблон письма когда заказ подтвержден клиентом
    public static function fill_email_4_template(): void
    {
        $val = get_option('bonus_option_name');
        $value = $val['email-when-order-confirm-title'] ?? __('Writing off bonus points', 'bonus-for-woo');

        $text_email = '<p>' . __('Hello', 'bonus-for-woo') . ', [user]</p>
      <p>' . sprintf(__('You used %s bonus points to pay for order number %s.', 'bonus-for-woo'), '[points]',
                '[order]') . '</p>
      <p>' . __('Cause', 'bonus-for-woo') . ': [cause]</p> 
      <p>' . __('The sum of your bonus points is now', 'bonus-for-woo') . ': <b>[total] ' . __('points',
                'bonus-for-woo') . '</b></p>';
        ?>
        <div class="label-input-mail"><?php echo __('Email header', 'bonus-for-woo'); ?></div>
        <input style="width:100%"
               placeholder="<?php echo __('Bonus points have been added to you!', 'bonus-for-woo'); ?>" type="text"
               name="bonus_option_name[email-when-order-confirm-title]" value="<?php echo esc_attr($value) ?>"/>
        <div class="label-editor-mail"><?php echo __('Letter template', 'bonus-for-woo'); ?></div>
        <?php
        $content = $val['email-when-order-confirm-text'] ?? $text_email;
        $editor_id = 'email-when-order-confirm-text';
        $settings = array(
            'media_buttons' => true,
            'textarea_name' => 'bonus_option_name[email-when-order-confirm-text]'
        );
        wp_editor($content, $editor_id, $settings);

    }


    ## Отправлять ли письмо когда поменялся статус заказа
    public static function fill_email_5(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['email-when-order-change']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[email-when-order-change]" type="checkbox" value="1" <?php echo $checked; ?>>
    <?php }

## Заполняем шаблон при начислении баллов когда заказ оплачен
    public static function fill_email_5_template(): void
    {
        $val = get_option('bonus_option_name');
        $value = $val['email-when-order-change-title'] ?? __('Points accrual', 'bonus-for-woo');

        $text_email = '<p>' . __('Hello', 'bonus-for-woo') . ', [user]</p>
      <p>' . sprintf(__('You have accrued %s bonus points from order number %s.', 'bonus-for-woo'), '[points]',
                '[order]') . '</p>
      <p>' . __('The sum of your bonus points is now', 'bonus-for-woo') . ': <b>[total] ' . __('points',
                'bonus-for-woo') . '</b></p>';
        ?>
        <div class="label-input-mail"><?php echo __('Email header', 'bonus-for-woo'); ?></div>
        <input style="width:100%"
               placeholder="<?php echo __('Bonus points have been added to you!', 'bonus-for-woo'); ?>" type="text"
               name="bonus_option_name[email-when-order-change-title]" value="<?php echo esc_attr($value) ?>"/>
        <div class="label-editor-mail"><?php echo __('Letter template', 'bonus-for-woo'); ?></div>
        <?php
        $content = $val['email-when-order-change-text'] ?? $text_email;
        $editor_id = 'email-when-order-change-text';
        $settings = array(
            'media_buttons' => true,
            'textarea_name' => 'bonus_option_name[email-when-order-change-text]'
        );
        wp_editor($content, $editor_id, $settings);

    }

## Заполняем шаблон при начислении баллов когда заказ оплачен рефералом
    public static function fill_email_5_template_ref(): void
    {
        $val = get_option('bonus_option_name');
        $value = $val['email-when-order-change-referal-title'] ?? __('Points accrual', 'bonus-for-woo');

        $text_email = '<p>' . __('Hello', 'bonus-for-woo') . ', [user]</p>
      <p>' . sprintf(__('You have been credited with %s bonus points.', 'bonus-for-woo'), '[points]') . '</p>
      <p>' . __('Cause', 'bonus-for-woo') . ': [cause]</p>
      <p>' . __('The sum of your bonus points is now', 'bonus-for-woo') . ': <b>[total] ' . __('points',
                'bonus-for-woo') . '</b></p>';
        ?>
        <div class="label-input-mail"><?php echo __('Email header', 'bonus-for-woo'); ?></div>
        <input style="width:100%" placeholder="<?php echo __('Points accrual', 'bonus-for-woo'); ?>" type="text"
               name="bonus_option_name[email-when-order-change-referal-title]" value="<?php echo esc_attr($value) ?>"/>
        <div class="label-editor-mail"><?php echo __('Letter template', 'bonus-for-woo'); ?></div>
        <?php
        $content = $val['email-when-order-change-referal-text'] ?? $text_email;
        $editor_id = 'email-when-order-change-referal-text';
        $settings = array(
            'media_buttons' => true,
            'textarea_name' => 'bonus_option_name[email-when-order-change-referal-text]'
        );
        wp_editor($content, $editor_id, $settings);
    }

    ## Заполняем шаблон при начислении баллов когда заказ возвращен
    public static function fill_email_5_template2(): void
    {
        $val = get_option('bonus_option_name');
        $value = $val['email-when-order-change-title-vozvrat'] ?? __('Refund of bonus points', 'bonus-for-woo');

        $text_email = '<p>' . __('Hello', 'bonus-for-woo') . ', [user]</p>
      <p>' . sprintf(__('The %s bonus points you earned for order no. %s have been canceled.', 'bonus-for-woo'),
                '[cashback]', '[order]') . '</p>
      <p>' . sprintf(__('You have returned %s bonus points for order number %s.', 'bonus-for-woo'), '[points]',
                '[order]') . '</p>
      <p>' . __('The sum of your bonus points is now', 'bonus-for-woo') . ': <b>[total] ' . __('points',
                'bonus-for-woo') . '</b></p>';
        ?>
        <div class="label-input-mail"><?php echo __('Email header', 'bonus-for-woo'); ?></div>
        <input style="width:100%"
               placeholder="<?php echo __('Bonus points have been added to you!', 'bonus-for-woo'); ?>" type="text"
               name="bonus_option_name[email-when-order-change-title-vozvrat]" value="<?php echo esc_attr($value) ?>"/>
        <div class="label-editor-mail"><?php echo __('Letter template', 'bonus-for-woo'); ?></div>
        <?php
        $content = $val['email-when-order-change-text-vozvrat'] ?? $text_email;
        $editor_id = 'email-when-order-change-text-vozvrat';
        $settings = array(
            'media_buttons' => true,
            'textarea_name' => 'bonus_option_name[email-when-order-change-text-vozvrat]'
        );
        wp_editor($content, $editor_id, $settings);

    }

    ## Заполняем опцию fill_email_6
    public static function fill_email_6(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['email-when-review']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[email-when-review]" type="checkbox" value="1" <?php echo $checked; ?>>
    <?php }


    ## Заполняем шаблон при начислении баллов когда одобрен отзыв
    public static function fill_email_6_template(): void
    {
        $val = get_option('bonus_option_name');
        $value = $val['email-when-review-title'] ?? __('Points accrual', 'bonus-for-woo');

        $text_email = '<p>' . __('Hello', 'bonus-for-woo') . ', [user]</p>
      <p>' . sprintf(__('You have received %s bonus points for a product review.', 'bonus-for-woo'), '[points]') . '</p>
      <p>' . __('Cause', 'bonus-for-woo') . ': [cause]</p>
      <p>' . __('The sum of your bonus points is now', 'bonus-for-woo') . ': <b>[total] ' . __('points',
                'bonus-for-woo') . '</b></p>';
        ?>
        <div class="label-input-mail"><?php echo __('Email header', 'bonus-for-woo'); ?></div>
        <input style="width:100%"
               placeholder="<?php echo __('Bonus points have been added to you!', 'bonus-for-woo'); ?>" type="text"
               name="bonus_option_name[email-when-review-title]" value="<?php echo esc_attr($value) ?>"/>
        <div class="label-editor-mail"><?php echo __('Letter template', 'bonus-for-woo'); ?></div>
        <?php
        $content = $val['email-when-review-text'] ?? $text_email;
        $editor_id = 'email-when-review-text';
        $settings = array('media_buttons' => true, 'textarea_name' => 'bonus_option_name[email-when-review-text]');
        wp_editor($content, $editor_id, $settings);

    }


    ## Заполняем опцию fill_email_7
    public static function fill_email_7(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['email-when-status-chenge']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[email-when-status-chenge]" type="checkbox" value="1" <?php echo $checked; ?>>
    <?php }


    ## Шаблон письма, когда меняется статус
    public static function fill_email_7_template(): void
    {
        $val = get_option('bonus_option_name');
        $value = $val['email-when-status-chenge-title'] ?? __('Changing your status', 'bonus-for-woo');

        $text_email = '<p>' . __('Hello', 'bonus-for-woo') . ', [user]</p>
      <p>' . sprintf(__('Now your status is "%s".', 'bonus-for-woo'), '[role]') . '</p> 
      <p>' . __('Now the percentage of cashback:', 'bonus-for-woo') . ' [cashback]%</p>';
        ?>
        <div class="label-input-mail"><?php echo __('Email header', 'bonus-for-woo'); ?></div>
        <input style="width:100%" placeholder="<?php echo __('Changing your status', 'bonus-for-woo'); ?>" type="text"
               name="bonus_option_name[email-when-status-chenge-title]" value="<?php echo esc_attr($value) ?>"/>
        <div class="label-editor-mail"><?php echo __('Letter template', 'bonus-for-woo'); ?></div>
        <?php
        $content = $val['email-when-status-chenge-text'] ?? $text_email;
        $editor_id = 'email-when-status-chenge-text';
        $settings = array(
            'media_buttons' => true,
            'textarea_name' => 'bonus_option_name[email-when-status-chenge-text]'
        );
        wp_editor($content, $editor_id, $settings);

    }


    ## Отправлять ли письмо о начислении балов за др
    public static function fill_email_on_birthday(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['email-when-birthday']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[email-when-birthday]" type="checkbox" value="1" <?php echo $checked; ?>>
    <?php }


## Шаблон письма о начислении балов за др
    public static function fill_email_on_birthday_text(): void
    {
        $val = get_option('bonus_option_name');
        $value = $val['email-whens-birthday-title'] ?? __('Bonus points on your birthday', 'bonus-for-woo');

        $text_email = '<p>' . __('Hello', 'bonus-for-woo') . ', [user].</p>
       
      <p>' . __('Happy birthday and give you bonus points:', 'bonus-for-woo') . ' [points_for_birthday]</p>';
        ?>
        <div class="label-input-mail"><?php echo __('Email header', 'bonus-for-woo'); ?></div>
        <input style="width:100%" placeholder="<?php echo __('Bonus points on your birthday', 'bonus-for-woo'); ?>"
               type="text" name="bonus_option_name[email-whens-birthday-title]" value="<?php echo esc_attr($value) ?>"/>
        <div class="label-editor-mail"><?php echo __('Letter template', 'bonus-for-woo'); ?></div>
        <?php
        $content = $val['email-when-birthday-text'] ?? $text_email;
        $editor_id = 'email-when-birthday-text';
        $settings = array('media_buttons' => true, 'textarea_name' => 'bonus_option_name[email-when-birthday-text]');
        wp_editor($content, $editor_id, $settings);

    }


    ## Отправлять ли письмо о скором сгорании баллов
    public static function fill_email_on_remove_points(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['email-when-inactive-notice']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[email-when-inactive-notice]" type="checkbox" value="1" <?php echo $checked; ?>>
    <?php }


## Шаблон письма о скором сгорании баллов
    public static function fill_email_inactive_notice_text(): void
    {
        $val = get_option('bonus_option_name');
        $value = $val['email-when-inactive-notice-title'] ?? __('Your points will be deleted soon', 'bonus-for-woo');

        $text_email = '<p>' . __('Hello', 'bonus-for-woo') . ', [user].</p>
       <p>' . sprintf(__('Your points will be deleted after %s days.', 'bonus-for-woo'), '[days]') . '</p>';
        ?>
        <div class="label-input-mail"><?php echo __('Email header', 'bonus-for-woo'); ?></div>
        <input style="width:100%" placeholder="<?php echo __('Your points will be deleted soon', 'bonus-for-woo'); ?>"
               type="text" name="bonus_option_name[email-when-inactive-notice-title]" value="<?php echo esc_attr($value) ?>"/>
        <div class="label-editor-mail"><?php echo __('Letter template', 'bonus-for-woo'); ?></div>
        <?php
        $content = $val['email-when-inactive-notice-text'] ?? $text_email;
        $editor_id = 'email-when-inactive-notice-text';
        $settings = array('media_buttons' => true, 'textarea_name' => 'bonus_option_name[email-when-inactive-notice-text]');
        wp_editor($content, $editor_id, $settings);

    }


    ## Отправлять ли письмо о ежедневном начислении баллов
    public static function fill_email_on_every_day(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['email-when-everyday-login']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[email-when-everyday-login]" type="checkbox" value="1" <?php echo $checked; ?>>
    <?php }

## Шаблон письма о ежедневном начислении баллов
    public static function fill_email_every_day_text(): void
    {
        $val = get_option('bonus_option_name');
        $value = $val['email-when-everyday-login-title'] ?? __('Bonus points have been added to you!', 'bonus-for-woo');

        $text_email = '<p>' . __('Hello', 'bonus-for-woo') . ', [user].</p>
       <p>' . sprintf(__('You get %s points for logging into your account.  ', 'bonus-for-woo'), '[points]') . '</p>
        <p>' . __('The sum of your bonus points is now', 'bonus-for-woo') . ': <b>[total] ' . __('points','bonus-for-woo') . '</b></p>';
        ?>
        <div class="label-input-mail"><?php echo __('Email header', 'bonus-for-woo'); ?></div>
        <input style="width:100%" placeholder="<?php echo __('Bonus points have been added to you!', 'bonus-for-woo'); ?>"
               type="text" name="bonus_option_name[email-when-everyday-login-title]" value="<?php echo esc_attr($value) ?>"/>
        <div class="label-editor-mail"><?php echo __('Letter template', 'bonus-for-woo'); ?></div>
        <?php
        $content = $val['email-when-everyday-login-text'] ?? $text_email;
        $editor_id = 'email-when-everyday-login-text';
        $settings = array('media_buttons' => true, 'textarea_name' => 'bonus_option_name[email-when-everyday-login-text]');
        wp_editor($content, $editor_id, $settings);

    }



    ## Заполняем опцию 166
    public static function fill_primer_field166(): void
    { ?>
        <hr>
    <?php }


    ## Заполняем опцию 12
    public static function fill_primer_field12(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['bonus-in-price']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[bonus-in-price]" type="checkbox" value="1" <?php echo $checked; ?>>
    <?php }


    ## Заполняем опцию 12
    public static function fill_primer_field12b(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['bonus-in-price-loop']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[bonus-in-price-loop]" type="checkbox" value="1" <?php echo $checked; ?>>
    <?php }


## Заполняем опцию upto
    public static function fill_primer_field12c(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['bonus-in-price-upto']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[bonus-in-price-upto]" type="checkbox" value="1" <?php echo $checked; ?>>
    <?php }


    ## Заполняем опцию 12а
    public static function fill_primer_field12a(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['cashback-in-cart']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[cashback-in-cart]" type="checkbox" value="1" <?php echo $checked; ?>>
    <?php }


    ## Списание баллов в оформлении заказа
    public static function fill_primer_field14(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['spisanie-in-checkout']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[spisanie-in-checkout]" type="checkbox" value="1" <?php echo $checked; ?>>

    <?php }


    ## Скрыть возможность потратить баллы для товаров со скидкой?
    public static function fill_primer_field15(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['spisanie-onsale']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[spisanie-onsale]" type="checkbox" value="1" <?php echo $checked; ?>>

    <?php }


    ## С какой даты начинать считать заказы
    public static function fill_order_start_date(): void
    {
        $val = get_option('bonus_option_name');
        $val =  $val['order_start_date'] ?? '';
        ?>

        <input type="date"   name="bonus_option_name[order_start_date]"
               value="<?php echo esc_attr($val) ?>"/>
        <small style="color:#999999"><?php  ?></small>
    <?php }


    ## Баллы за день рождение
    public static function fill_birthday(): void
    {
        $val = get_option('bonus_option_name');
        $val = isset($val['birthday']) ? (float)$val['birthday'] : 0;
        ?>

        <input style="width: 60px" type="text"   name="bonus_option_name[birthday]"
               value="<?php echo esc_attr($val) ?>"/>
        <small style="color:#999999"><?php echo (new BfwPoints())->pointsLabel( esc_attr($val));   ?></small>
    <?php }

    ## Ежедневные баллы за первый вход
    public static function every_days(): void
    {
        $val = get_option('bonus_option_name');
        $val = isset($val['every_days']) ? (float)$val['every_days'] : 0;
        ?>

        <input style="width: 60px" type="text"   name="bonus_option_name[every_days]"
               value="<?php echo esc_attr($val) ?>"/>
        <small style="color:#999999"><?php echo (new BfwPoints())->pointsLabel( esc_attr($val));   ?></small>
    <?php }

    ## Заполняем опцию 16
    public static function fill_primer_field16(): void
    {
        $val = get_option('bonus_option_name');
        $val = isset($val['max-percent-bonuses']) ? (int)$val['max-percent-bonuses'] : 100;
        ?>

        <input style="width: 60px" type="number" min="0" max="100" name="bonus_option_name[max-percent-bonuses]"
               value="<?php echo esc_attr($val) ?>"/>
        <small style="color:#999999"><?php echo __('%', 'bonus-for-woo') ?></small>
    <?php }


    ## Заполняем опцию 17
    public static function fill_primer_field17(): void
    {
        $val = get_option('bonus_option_name');

        if (empty($val['exclude-category-cashback'])) {
            $val['exclude-category-cashback'] = array();
        }
        ?>


        <select multiple="multiple" id="exclude-category" name="bonus_option_name[exclude-category-cashback][]">
            <?php

            $get_categories_product = get_terms("product_cat", [
                "orderby" => "name", // Тип сортировки
                "order" => "ASC", // Направление сортировки
                "hide_empty" => 0, // Скрывать пустые. 1 - да, 0 - нет.
            ]);
            foreach ($get_categories_product as $cat_item) {
                $selected = in_array($cat_item->term_id,
                    $val['exclude-category-cashback']) ? ' selected="selected" ' : '';

                echo "<option " . $selected . " value='" . esc_attr($cat_item->term_id) . "'>" . esc_html($cat_item->name) . "</option>";
            }
            ?>
        </select>

        <?php
    }

    ## Исключаем метод оплаты из бонусной системы
    public static function fill_primer_field_pay_method(): void
    {
        $val = get_option('bonus_option_name');

        if (empty($val['exclude-payment-method'])) {
            $val['exclude-payment-method'] = array();
        }
        ?>


        <select id="exclude-payment-method" name="bonus_option_name[exclude-payment-method][]">
            <option><?php echo __('Select a Payment Method', 'bonus-for-woo'); ?></option>
            <?php

                $gateways = WC()->payment_gateways->payment_gateways();
                $options = array();
                foreach ( $gateways as $id => $gateway ) {
                    $options[$id] = $gateway->get_method_title();
                }

            foreach ( $options as $payment_id => $method_title ) {
                $selected = in_array($payment_id, $val['exclude-payment-method']) ? ' selected="selected" ' : '';

                $option = '<option value="' . $payment_id . '" ';
                $option .= $selected;
                $option .= '>';
                $option .= $method_title ;
                $option .= '</option>';
                echo $option;
            }

            ?>
        </select>

        <?php
    }



    ## Заполняем опцию 18
    public static function fill_primer_field18(): void
    {
        $val = get_option('bonus_option_name');
        $value = $val['exclude-tovar-cashback'] ?? '';
        ?>
        <input style="width: 250px" placeholder="3124,524,231" type="text"
               name="bonus_option_name[exclude-tovar-cashback]" value="<?php echo esc_attr($value) ?>"/>
        <?php
    }


    public static function fill_buy_balls(): void
    {
        $val = get_option('bonus_option_name');
        $value = $val['buy_balls-cashback'] ?? '';
        ?>
        <input style="width: 250px" placeholder="2374" type="text" name="bonus_option_name[buy_balls-cashback]"
               value="<?php echo esc_attr($value) ?>"/>
        <small style="color:grey">* <?php echo __('Only for logged in clients.', 'bonus-for-woo') ?>
        </small>  <?php
    }


    ## Заполняем опцию 19
    public static function fill_primer_field19(): void
    {
        $val = get_option('bonus_option_name');
        $valpr = isset($val['points-for-registration']) ? (float)$val['points-for-registration'] : 100;
        ?>
        <input style="width: 80px"  placeholder="50" type="text"
               name="bonus_option_name[points-for-registration]" value="<?php echo esc_attr($valpr) ?>"/>

        <?php

        if (!empty($val['referal-system'])) {
                $checkedr = isset($val['register-points-only-referal']) ? "checked" : "";
                ?>
                <input name="bonus_option_name[register-points-only-referal]" type="checkbox"
                       value="1" <?php echo $checkedr; ?>>
                <small style="color:grey">* <?php echo __('Add points only to the referral.', 'bonus-for-woo') ?>
                </small>
                <?php
                  }
    }


   /* не начислять кешбэк если клиент использует баллы*/
    public static function yous_balls_no_cashback_fild(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['yous_balls_no_cashback']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[yous_balls_no_cashback]" type="checkbox" value="1" <?php echo $checked; ?>>

    <?php }


    ## Заполняем опцию 20
    public static function fill_primer_field20(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['addkeshback-exclude']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[addkeshback-exclude]" type="checkbox" value="1" <?php echo $checked; ?>>
        <small style="color:grey">* <?php echo __('Yes, accrue', 'bonus-for-woo'); ?>
        </small>

    <?php }


    ## очищать баллы, которые хочет списать клиент при изменении количества товаров корзине
    public static function fill_primer_field21qty(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['clear-fast-bonus-were-qty-cart']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[clear-fast-bonus-were-qty-cart]" type="checkbox"
               value="1" <?php echo $checked; ?>>

    <?php }


    ## Удаление баллов за бездействие
    public static function fill_inactive(): void
    {
        $val = get_option('bonus_option_name');
        $value = $val['day-inactive'] ?? '';
        ?>
        <input style="width: 100px" placeholder="365" step="1" pattern="\d+" type="number"
               name="bonus_option_name[day-inactive]"
               value="<?php echo esc_attr($value) ?>"/> <?php echo __('days without orders', 'bonus-for-woo') ?>
        <?php

    }

    ## Удаление баллов за бездействие
    public static function fill_inactive_notice(): void
    {
        $val = get_option('bonus_option_name');
        $value = $val['day-inactive-notice'] ?? '';
        ?>
        <input style="width: 100px" placeholder="0" step="1" pattern="\d+" type="number"
               name="bonus_option_name[day-inactive-notice]"
               value="<?php echo esc_attr($value) ?>"/> <?php echo __('Days before deducting points', 'bonus-for-woo') ?>
        <?php

    }

    ## скрыть сколько дней осталось до списания баллов
    public static function fill_burn_point_in_account(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['burn_point_in_account']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[burn_point_in_account]" type="checkbox"
               value="1" <?php echo $checked; ?>>

    <?php }

    ## реферальная система
    public static function fill_primer_field21(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['referal-system']) ? "checked" : "";
        ?>
        <input id="referal-system" name="bonus_option_name[referal-system]" type="checkbox"
               value="1" <?php echo $checked; ?>>
    <?php if(empty($val['referal-system'])){echo __('When the referral system is activated, additional settings will appear.', 'bonus-for-woo');} }


    ## начислять баллы только за 1 заказ реферала
    public static function fill_primer_field27(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['first-order-referal']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[first-order-referal]" type="checkbox" value="1" <?php echo $checked; ?>>

    <?php }

    ## второй уровень реферала
    public static function fill_level_two(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['level-two-referral']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[level-two-referral]" type="checkbox" value="1" <?php echo $checked; ?>>

    <?php }

    ## кешбэк за инвайта первого уровня
    public static function fill_primer_field26(): void
    {
        $val = get_option('bonus_option_name');

        $val = isset($val['referal-cashback']) ? (int)$val['referal-cashback'] : 0;
        ?>

        <input style="width: 60px" placeholder="0" min="0" max="100" type="number"
               name="bonus_option_name[referal-cashback]" value="<?php echo esc_attr($val) ?>"/>
        <small style="color:#999999">%</small>
    <?php }

    ## кешбэк за инвайта второго уровня
    public static function fill_referal_cashback_two_level(): void
    {
        $val = get_option('bonus_option_name');

        $val = isset($val['referal-cashback-two-level']) ? (int)$val['referal-cashback-two-level'] : 0;
        ?>

        <input style="width: 60px" placeholder="0" min="0" max="100" type="number"
               name="bonus_option_name[referal-cashback-two-level]" value="<?php echo esc_attr($val) ?>"/>
        <small style="color:#999999">%</small>
    <?php }

    ## Сумма заказов, после которой клиенту станет доступна реферальная система.
    public static function fill_primer_field29(): void
    {
        $val = get_option('bonus_option_name');
        $val = isset($val['sum-orders-for-referral']) ? (float)$val['sum-orders-for-referral'] : 0;
        ?>

        <input style="width: 80px" placeholder="0"  type="text"
               name="bonus_option_name[sum-orders-for-referral]"
               value="<?php echo esc_attr($val) ?>"/> <?php echo get_woocommerce_currency_symbol(); ?>

    <?php }


    ## Заполняем опцию 24(минимальная сумма заказа)
    public static function fill_primer_field25(): void
    {
        $val = get_option('bonus_option_name');
        $valas = isset($val['minimal-amount']) ? (float)$val['minimal-amount'] : 0;
        ?>
        <input style="width: 80px" placeholder="50"   type="text" name="bonus_option_name[minimal-amount]"
               value="<?php echo esc_attr($valas) ?>"/> <?php echo get_woocommerce_currency_symbol();

        $checkedmin = isset($val['minimal-amount-cashback']) ? "checked" : "";

        ?>
        <input name="bonus_option_name[minimal-amount-cashback]" type="checkbox" value="1" <?php echo $checkedmin; ?>>
        <small style="color:grey">* <?php echo __('Valid for cashback', 'bonus-for-woo'); ?>
        </small>
        <?php
    }


    ## Исключить роли
    public static function fill_primer_field22(): void
    {
        $val = get_option('bonus_option_name');
        if (empty($val['exclude-role'])) {
            $val['exclude-role'] = array();
        }
        ?>
        <select multiple="multiple" id="exclude-role" name="bonus_option_name[exclude-role][]">
            <?php
            foreach (get_editable_roles() as $role => $details) {
                $name = translate_user_role($details['name']);
                $selected = in_array($role, $val['exclude-role']) ? ' selected="selected" ' : '';
                if ($role == 'administrator') {
                    $selected = 'selected="selected"  data-mandatory="true"';
                }
                echo "<option " . $selected . " value='" . esc_attr($role) . "'>$name</option>";
            }
            ?>
        </select>
        <?php
    }

    ## Не округлять десятичные дроби в баллах? Do not round decimals in points?
    public static function fill_rulles(): void
    {

        $val = get_option('bonus_option_name');
        $rulles_value =  $val['rulles_value']  ?? __('Terms & Conditions','bonus-for-woo');
        $rulles_url =  $val['rulles_url']  ?? '';
        ?>
        <input name="bonus_option_name[rulles_value]" type="text" value="<?php echo $rulles_value; ?>" placeholder="<?php echo $rulles_value; ?>" >
        <input name="bonus_option_name[rulles_url]" type="text" value="<?php echo $rulles_url; ?>" placeholder="https://" >
        <?php
    }

    ## Не округлять десятичные дроби в баллах? Do not round decimals in points?
    public static function fill_round_points(): void
    {

        $val = get_option('bonus_option_name');
        $checked = isset($val['round_points']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[round_points]" type="checkbox" value="1" <?php echo $checked; ?>>
        <small style="color: red"><?php echo __('If selected, points will not be rounded.','bonus-for-woo'); ?></small>
    <?php
    }


    ## При каком статусе списывать баллы
    public static function fill_write_points_order_status(): void
    {
        $val = get_option('bonus_option_name');

        if (empty($val['write_points_order_status'])) {
            $val['write_points_order_status'] = 'processed';
        }
        ?>
        <select id="write_points_order_status" name="bonus_option_name[write_points_order_status]">
            <?php
            $selected ='';
            if($val['write_points_order_status'] =='processed'){   $selected= 'selected="selected"';}

            echo  '<option value="processed" '.$selected.'>'.__('At the time of order confirmation','bonus-for-woo').'</option>';
            foreach (wc_get_order_statuses() as $status => $status_name) {
                if( $val['write_points_order_status'] == mb_substr($status, 3)){
                    $selected= 'selected="selected"';
                }else{$selected='';}

                echo  '<option value="' .mb_substr($status, 3) .'" '.$selected.'>'.$status_name.'</option>';
            }
            ?>
        </select>

        <?php
    }

    ## Начисляем баллы по указанному статусу заказа
    public static function fill_add_points_order_status(): void
    {
        $val = get_option('bonus_option_name');

        if (empty($val['add_points_order_status'])) {
            $val['add_points_order_status'] = 'completed';
        }
        ?>
        <select id="add_points_order_status" name="bonus_option_name[add_points_order_status]">
            <?php
            foreach (wc_get_order_statuses() as $status => $status_name) {

                 if( $val['add_points_order_status'] == mb_substr($status, 3)){
                     $selected= 'selected="selected"';
                 }else{$selected='';}

                echo  '<option value="' .mb_substr($status, 3) .'" '.$selected.'>'.$status_name.'</option>';
            }
            ?>
        </select>

        <?php
    }

    ## Возврат баллов по указанному статусу заказа
    public static function fill_refunded_points_order_status(): void
    {
        $val = get_option('bonus_option_name');

        if (empty($val['refunded_points_order_status'])) {
            $val['refunded_points_order_status'] = 'refunded';
        }
        ?>
        <select id="refunded_points_order_status" name="bonus_option_name[refunded_points_order_status]">
            <?php
            foreach (wc_get_order_statuses() as $status => $status_name) {

                if( $val['refunded_points_order_status'] == mb_substr($status, 3)){
                    $selected= 'selected="selected"';
                }else{$selected='';}

                echo  '<option value="' .mb_substr($status, 3) .'" '.$selected.'>'.$status_name.'</option>';
            }
            ?>
        </select>

        <?php
    }

    //название баллов
    public static function name_points(): void
    {
        $value = get_option('bonus_option_name');

        $label_point = $value['label_point'] ?? __('Point', 'bonus-for-woo');
        $label_point_two = $value['label_point_two'] ?? __('Points', 'bonus-for-woo');
        $label_points = $value['label_points'] ?? __('Points', 'bonus-for-woo');
        ?>
        1 <input style="width: 80px" type="text" name="bonus_option_name[label_point]"
                 value="<?php echo esc_attr($label_point) ?>"/>,
        2 <input style="width: 80px" type="text" name="bonus_option_name[label_point_two]"
                 value="<?php echo esc_attr($label_point_two) ?>"/>,
        25 <input style="width: 80px" type="text" name="bonus_option_name[label_points]"
                  value="<?php echo esc_attr($label_points) ?>"/>
        <?php
    }


    ## Количество бонусов за отзыв
    public static function fill_primer_field1(): void
    {
        $val = get_option('bonus_option_name');
        $val = isset($val['bonus-for-otziv']) ? (float)$val['bonus-for-otziv'] : 0;
        ?>

        <input style="width: 60px" type="text" name="bonus_option_name[bonus-for-otziv]"
               value="<?php echo esc_attr($val) ?>"/> <?php echo (new BfwPoints())->pointsLabel($val); ?>
    <?php }


    ## Заполняем опцию 30 порядок меню в аккаунте
    public static function fill_primer_field30(): void
    {
        $val = get_option('bonus_option_name');
        $val = isset($val['poryadok-in-account']) ? (int)$val['poryadok-in-account'] : 4;
        ?>

        <input style="width: 50px" min="1" max="10" type="number" name="bonus_option_name[poryadok-in-account]"
               value="<?php echo esc_attr($val) ?>"/>
    <?php }


    ## Не тратить баллы, если применяется купон?
    public static function fill_primer_field23(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['balls-and-coupon']) ? "checked" : "";
        ?>
        <input id="balls-and-coupon" name="bonus_option_name[balls-and-coupon]" type="checkbox"
               value="1" <?php echo $checked; ?>>

    <?php }

    /*не учитывать купоны и скидки при подсчете кешбэке*/
    public static function exclude_fees_coupons(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['exclude-fees-coupons']) ? "checked" : "";
        ?>
        <input id="balls-and-coupon" name="bonus_option_name[exclude-fees-coupons]" type="checkbox"
               value="1" <?php echo $checked; ?>>

    <?php }

    ## опция24
    public static function fill_primer_field24(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['cashback-for-shipping']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[cashback-for-shipping]" type="checkbox" value="1" <?php echo $checked; ?>>

    <?php }


    ## Удалите следы плагина при удалении плагина.
    public static function fill_primer_clear(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['clear-bfw-bd']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[clear-bfw-bd]" type="checkbox" value="1" <?php echo $checked; ?>>

    <?php }



    ## Скрыть историю начисления баллов
    public static function fill_primer_field28(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['hystory-hide']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[hystory-hide]" type="checkbox" value="1" <?php echo $checked; ?>>
    <?php }

    ## Скрыть историю начисления баллов
    public static function fee_or_coupon(): void
    {
        $val = get_option('bonus_option_name');
        $checked = isset($val['fee-or-coupon']) ? "checked" : "";
        ?>
        <input name="bonus_option_name[fee-or-coupon]" type="checkbox" value="1" <?php echo $checked; ?>>
    <?php }

    ## Заголовок на странице аккаунта
    public static function fill_primer_field2(): void
    {
        $val = get_option('bonus_option_name');
        $value = $val['title-on-account'] ?? __('Bonus page', 'bonus-for-woo');
        ?>
        <input style="width: 350px" type="text" name="bonus_option_name[title-on-account]"
               value="<?php echo esc_attr($value) ?>"/>
    <?php }


    public static function fill_primer_field2h(): void
    {
        $val = get_option('bonus_option_name');
        $title_history = $val['title-on-history-account'] ?? __('Points accrual', 'bonus-for-woo');
        ?>
        <input style="width: 350px" type="text" name="bonus_option_name[title-on-history-account]"
               value="<?php echo esc_attr($title_history) ?>"/>
    <?php }


    ## Заголовок статуса в аккаунте
    public static function fill_primer_field4(): void
    {
        $val = get_option('bonus_option_name');
        $value = $val['title-my-status-on-account'] ?? __('My status', 'bonus-for-woo');
        ?>
        <input style="width: 350px" type="text" name="bonus_option_name[title-my-status-on-account]"
               value="<?php echo esc_attr($value) ?>"/>
    <?php }


    ## Заголовок "Мой процент кэшбека"
    public static function fill_primer_field5(): void
    {
        $val = get_option('bonus_option_name');
        $value = $val['my-procent-on-account'] ?? __('My cashback percentage', 'bonus-for-woo');
        ?>
        <input style="width: 350px" type="text" name="bonus_option_name[my-procent-on-account]"
               value="<?php echo esc_attr($value) ?>"/>
    <?php }


    ## Заполняем заголовок "Мои бонусные баллы"
    public static function fill_primer_field6(): void
    {
        $val = get_option('bonus_option_name');
        $value = $val['my-bonus-points-on-account'] ?? __('My bonus points', 'bonus-for-woo');
        ?>
        <input style="width: 350px" type="text" name="bonus_option_name[my-bonus-points-on-account]"
               value="<?php echo esc_attr($value) ?>"/>
    <?php }


    ## Заполняем опцию 11
    public static function fill_primer_field11(): void
    {
        $val = get_option('bonus_option_name');

        $value1 = $val['remaining-amount1'] ?? __('Up to', 'bonus-for-woo');
        $value2 = $val['remaining-amount2'] ?? __('cashback and status', 'bonus-for-woo');
        $value3 = $val['remaining-amount3'] ?? __(', you have to purchase goods for', 'bonus-for-woo');

        ?>
        <input style="width: 100px" type="text" name="bonus_option_name[remaining-amount1]"
               value="<?php echo esc_attr($value1) ?>"/>
        20% <input style="width: 170px" type="text" name="bonus_option_name[remaining-amount2]"
                   value="<?php echo esc_attr($value2) ?>"/> GOLD
        <input style="width: 170px" type="text" name="bonus_option_name[remaining-amount3]"
               value="<?php echo esc_attr($value3) ?>"/> 500 <?php echo get_woocommerce_currency_symbol(); ?>


    <?php }


    ## Заполняем опцию 7
    public static function fill_primer_field7(): void
    {
        $val = get_option('bonus_option_name');

        $value1 = $val['bonustext-in-cart1'] ?? __('Use', 'bonus-for-woo');
        $value2 = $val['bonustext-in-cart2'] ?? __('Points to get a discount', 'bonus-for-woo');
        $value3 = $val['bonustext-in-cart3'] ?? __(' for this order!', 'bonus-for-woo');
        $value4 = $val['bonustext-in-cart4'] ?? __('Use points', 'bonus-for-woo');
        ?>
        <input style="width: 100px" type="text" name="bonus_option_name[bonustext-in-cart1]"
               value="<?php echo esc_attr($value1) ?>"/>
        20 <input style="width: 170px" type="text" name="bonus_option_name[bonustext-in-cart2]"
                  value="<?php echo esc_attr($value2) ?>"/> 20 <?php echo get_woocommerce_currency_symbol(); ?>
        <input style="width: 170px" type="text" name="bonus_option_name[bonustext-in-cart3]"
               value="<?php echo esc_attr($value3) ?>"/>.

        <input style="width: 170px" type="text" name="bonus_option_name[bonustext-in-cart4]"
               value="<?php echo esc_attr($value4) ?>"/>
    <?php }


    ## Заполняем опцию 8
    public static function fill_primer_field8(): void
    {
        $val = get_option('bonus_option_name');

        $value = $val['use-points-on-cart'] ?? __('Use points', 'bonus-for-woo');
        ?>
        <input style="width: 350px" type="text" name="bonus_option_name[use-points-on-cart]"
               value="<?php echo esc_attr($value) ?>"/>
    <?php }


    ## Заполняем опцию 9
    public static function fill_primer_field9(): void
    {
        $val = get_option('bonus_option_name');
        $value = $val['bonus-points-on-cart'] ?? __('Bonus points', 'bonus-for-woo');
        ?>
        <input style="width: 350px" type="text" name="bonus_option_name[bonus-points-on-cart]"
               value="<?php echo esc_attr($value) ?>"/>
    <?php }


## Заполняем опцию 10
    public static function fill_primer_field10(): void
    {
        $val = get_option('bonus_option_name');
        $value = $val['remove-on-cart'] ?? __('Remove points', 'bonus-for-woo');
        ?>
        <input style="width: 350px" type="text" name="bonus_option_name[remove-on-cart]"
               value="<?php echo esc_attr($value) ?>"/>
    <?php }

    ## Название офлайн-продукта в заказе покупателя
    public static function fill_title_product_offline_order(): void
    {
        $val = get_option('bonus_option_name');
        $value = $val['title-product-offline-order'] ?? __('Offline product', 'bonus-for-woo');
        ?>
        <input style="width: 350px" type="text" name="bonus_option_name[title-product-offline-order]"
               value="<?php echo esc_attr($value) ?>"/>
    <?php }


## Очистка данных
    public static function sanitize_callback($options)
    {
        foreach ($options as $name => & $val) {
            if ($name) {
                $val = strip_tags($val);
            }
        }
        return $options;
    }


    public static function bonus_plugin_options(): void
    {
        if (current_user_can('manage_options')) {

            if ((new BfwRoles)->is_pro()) {
                $pro_text = ' Pro';
            } else {
                $pro_text = '';
            }
            ?>
            <div class="wrap bonus-for-woo-admin">

                <h2><?php echo _e('Bonus for Woo', 'bonus-for-woo'), $pro_text, ' V', BONUS_COMPUTY_VERSION; ?></h2>
                <p><?php echo __('With the support of', 'bonus-for-woo'); ?> <a href="https://computy.ru"
                                                                                target="_blank"
                                                                                title="Разработка и поддержка сайтов на WordPress">
                        computy </a> <br>
                    <a class="give_me_your_money"
                       href="https://sobe.ru/na/kompyuti_na_podderzhku_plagina_computy_for_plugin_support"
                       target="_blank"><?php echo __('Donate to support the plugin', 'bonus-for-woo'); ?></a><br>
                    <a href="https://computy.ru/blog/bonus-for-woo-wordpress/"
                       target="_blank"><?php echo __('About plugin',
                            'bonus-for-woo'); ?></a>. <?php echo __('Thank you for rating', 'bonus-for-woo'); ?> <a
                            style="color:#e51f1f;" href="https://wordpress.org/plugins/bonus-for-woo/#reviews"
                            target="_blank">★★★★★</a> <br>
                    <a href="https://computy.ru/plugins.php"
                       target="_blank"><?php echo __('Our other plugins', 'bonus-for-woo'); ?></a>
                </p>
                <hr>
                <div class="bfw_texts_wrap">
                    <div class="bfw_text">
                        <h3><?php echo __('Description', 'bonus-for-woo'); ?></h3>
                        <p><?php echo __('This plugin is designed to create a bonus system with cashback.',
                                'bonus-for-woo'); ?><br>
                            <?php echo __('The cashback percentage is calculated based on the users status in the form of bonus points.',
                                'bonus-for-woo'); ?>
                            <br>
                            <?php echo __('Each user status has a corresponding cashback percentage.',
                                'bonus-for-woo'); ?>
                            <br>
                            <?php echo __('The users status depends on the total amount of the users orders.',
                                'bonus-for-woo'); ?>
                            <br>
                            <?php echo __('You can add points to your customers on the page  <a href="users.php" target="_blank">users</a>,by selecting the desired client.',
                                'bonus-for-woo'); ?>
                        </p>
                        <a class="pdf-button" href="https://computy.ru/blog/docs/bonus-for-woo/"
                           target="_blank"><?php echo __('Documentation',
                                'bonus-for-woo'); ?></a>


                    </div>
                    <div class="bfw_text">
                        <h3><?php echo __('Shordcodes', 'bonus-for-woo'); ?></h3>
                        <p>
                            1. <?php echo __('You can place the client status anywhere in the site using a shortcode:  [bfw_status]','bonus-for-woo'); ?>
                            <br>
                            2. <?php echo __('To display the percentage of cachek, use a shortcode: [bfw_cashback]','bonus-for-woo'); ?>
                            <br>
                            3. <?php echo __('To display the number of points awarded, use the shortcode: [bfw_points]','bonus-for-woo'); ?>
                            <br>
                            4. <?php echo __('Withdrawal of the bonus page from the personal account: [bfw_account]','bonus-for-woo'); ?>
                            <br>
                            5. <?php echo __('Withdrawal of a block of information of the referral system from the account: [bfw_account_referral]','bonus-for-woo'); ?>


                        </p><a class="pdf-button" href="https://computy.ru/blog/docs/bonus-for-woo/shortkody/"
                               target="_blank"><?php echo __('Full list of shortcodes',
                                'bonus-for-woo'); ?></a>
                    </div>

                </div>


                <div class="wrap">
                    <?php


                    /*Обработчик запроса добавления роли*/
                    if (isset($_POST['bfw_computy_ajax'])) {
                        if ($_POST['bfw_computy_ajax'] == 'bfw_computy_ajax') {
                            global $wpdb;
                            $name_role = _sanitize_text_fields($_POST['name_role']);
                            $percent_role = _sanitize_text_fields($_POST['percent_role']);
                            $summa_start = _sanitize_text_fields($_POST['summa_start']);
                            $s = $name_role;
                            $s = (string)$s; // преобразуем в строковое значение
                            $s = strip_tags($s); // убираем HTML-теги
                            $s = str_replace(array("\n", "\r"), " ", $s); // убираем перевод каретки
                            $s = preg_replace("/\s+/", ' ', $s); // удаляем повторяющие пробелы
                            $s = trim($s); // убираем пробелы в начале и конце строки
                            $s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)
                            $s = str_replace(" ", "-", $s); // заменяем пробелы знаком -

                            if (preg_match("/[А-Яа-я]/", $s)) {
                                /*переводим в английский русские слова*/
                                $s = strtr($s, array(
                                    'а' => 'a',
                                    'б' => 'b',
                                    'в' => 'v',
                                    'г' => 'g',
                                    'д' => 'd',
                                    'е' => 'e',
                                    'ё' => 'e',
                                    'ж' => 'j',
                                    'з' => 'z',
                                    'и' => 'i',
                                    'й' => 'y',
                                    'к' => 'k',
                                    'л' => 'l',
                                    'м' => 'm',
                                    'н' => 'n',
                                    'о' => 'o',
                                    'п' => 'p',
                                    'р' => 'r',
                                    'с' => 's',
                                    'т' => 't',
                                    'у' => 'u',
                                    'ф' => 'f',
                                    'х' => 'h',
                                    'ц' => 'c',
                                    'ч' => 'ch',
                                    'ш' => 'sh',
                                    'щ' => 'shch',
                                    'ы' => 'y',
                                    'э' => 'e',
                                    'ю' => 'yu',
                                    'я' => 'ya',
                                    'ъ' => '',
                                    'ь' => ''
                                ));
                            }
                            $slug = $s;
                            // подготавливаем данные
                            $table_name = $wpdb->prefix . 'bfw_computy';

                            //проверка полей

                            if ($wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "bfw_computy  WHERE `name` =  '$name_role'  ")) {
                                echo '<div id="message" class="notice notice-warning is-dismissible">
	<p>Статус <b>' . $name_role . '</b> уже используется. Введите другое имя статуса.</p>
</div>';
                                $propusk = 'no';
                            } elseif ($wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "bfw_computy  WHERE `summa_start` =  '$summa_start'  ")) {

                                $table_bfw = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "bfw_computy  WHERE `summa_start` =  '$summa_start'");
                                foreach ($table_bfw as $bfw) {
                                    $isp_role = $bfw->name;
                                }
                                $propusk = 'no';

                                echo '<div id="message" class="notice notice-warning is-dismissible">
	<p>' . __('Amount', 'bonus-for-woo') . '  ' . $summa_start . ' ' . __('already used for role',
                                        'bonus-for-woo') . ' <b>' . $isp_role . '</b>. ' . __('Enter another cumulative amount to create a status.',
                                        'bonus-for-woo') . '</p></div>';
                            } else {
                                $propusk = 'yes';
                            }
                            //добавляем статус пользователя
                            if ($propusk == 'yes') {
                                if ($summa_start < '0') {
                                    echo '<div id="message" class="notice notice-warning is-dismissible"><p>' . __('The accumulated amount cannot be less than 0.',
                                            'bonus-for-woo') . '</p></div>';
                                } elseif ($name_role == '' || $slug == '') {
                                    echo '<div id="message" class="notice notice-warning is-dismissible"><p>' . __('The status name cannot be empty.',
                                            'bonus-for-woo') . '</p></div>';
                                } else {

                                    $wpdb->insert(
                                        $table_name, array(
                                        'name' => $name_role,
                                        'slug' => $slug,
                                        'percent' => $percent_role,
                                        'summa_start' => $summa_start,
                                    ), array("%s", "%s", "%s", "%s")
                                    );


                                    echo '<div id="message" class="notice notice-warning is-dismissible">
	<p>' . __('Status', 'bonus-for-woo') . ' <b>' . $name_role . '</b> ' . __('added', 'bonus-for-woo') . '.</p>
</div>';
                                }
                            }
                        } elseif ($_POST['bfw_computy_ajax'] == 'editrolehidden') {
                            global $wpdb;
                            $percent_role = sanitize_text_field($_POST['percent_role']);
                            $summa_start = sanitize_text_field($_POST['summa_start']);
                            $name_role = sanitize_text_field($_POST['name_role']);
                            $wpdb->update($wpdb->prefix . "bfw_computy",
                                array('percent' => $percent_role, 'summa_start' => $summa_start),
                                array('name' => $name_role)
                            );
                        }
                    }
                    /*Обработчик запроса добавления роли*/


                    /*Обработчик запроса удаления роли*/
                    if (isset($_POST['delete_role'])) {
                        $delete_role_slag = sanitize_text_field($_POST['delete_role_slag']);
                        $delete_role_name = sanitize_text_field($_POST['delete_role_name']);
                        global $wpdb;
                        $table_name = $wpdb->prefix . 'bfw_computy';
                        $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id = %d",
                            $_POST['delete_role'])
                        );
                        remove_role($delete_role_slag);
                        echo '<div id="message" class="notice notice-warning is-dismissible">
	<p>' . __('Status', 'bonus-for-woo') . ' <b>' . $delete_role_name . '</b> ' . __('deleted', 'bonus-for-woo') . '.</p>
</div>';
                    }
                    /*Обработчик запроса удаления роли*/


                    ?>


                    <div class="tabs_bfw">
                        <input type="radio" name="odin" checked="checked" id="vkl1"/><label for="vkl1"><i
                                    class="statusicon"></i><?php echo __('Client status management',
                                'bonus-for-woo'); ?></label>
                        <input type="radio" name="odin" id="vkl2"/><label for="vkl2"><i
                                    class="settingsicon"></i><?php echo __('Plugin settings', 'bonus-for-woo'); ?>
                        </label>
                        <?php
                        if ((new BfwRoles)->is_pro()) { ?>
                            <input type="radio" name="odin" id="vkl5"/><label class="nf0" for="vkl5"><i
                                        class="proicon"></i><?php echo __('Pro', 'bonus-for-woo'); ?></label>
                        <?php } ?>
                        <input type="radio" name="odin" id="vkl3"/><label for="vkl3"><i
                                    class="notifiicon"></i><?php echo __('Email notifications', 'bonus-for-woo'); ?>
                        </label>
                        <input type="radio" name="odin" id="vkl4"/><label for="vkl4"><i
                                    class="transicon"></i><?php echo __('Translate', 'bonus-for-woo'); ?></label>
                        <input type="radio" name="odin" id="vkl6"/><label for="vkl6"><i
                                    class="exporticon"></i><?php echo __('Export/Import', 'bonus-for-woo'); ?></label>
                        <?php
                        if (!(new BfwRoles)->is_pro()) { ?>
                            <input type="radio" name="odin" id="vkl5"/><label class="nf0" for="vkl5"><i
                                        class="proicon"></i><?php echo __('Pro', 'bonus-for-woo'); ?></label>
                        <?php } ?>
                        <div class="tab_bfw tab_bfw1"><h2><?php echo __('Client status management',
                                    'bonus-for-woo'); ?></h2>
                            <h3><b><?php echo __('Terms:', 'bonus-for-woo'); ?></b></h3>
                            <p><?php echo __('<b>Status name</b> - the name that will be displayed in the client\'s account and his personal account.',
                                    'bonus-for-woo'); ?></p>
                            <p><?php echo __('<b> Slug </b> is a unique name for the system to work.',
                                    'bonus-for-woo'); ?></p>
                            <p><?php echo __('<b> Cashback percentage </b> - the percentage that will be credited to a client with the corresponding status.',
                                    'bonus-for-woo'); ?></p>
                            <p><?php echo __('<b> Amount of orders </b> - is a number corresponding to the amount of customer orders with which this role is applied to the user.
              For example, if you enter 3000, this status will change only when the sum of all customer orders exceeds 3000. If you enter 0, the users status will change immediately, without purchases.</p>',
                                    'bonus-for-woo'); ?>
                                <?php
                                global $wpdb;

                                $table_bfw = $wpdb->get_results("SELECT *,summa_start FROM " . $wpdb->prefix . "bfw_computy  ORDER BY summa_start + 0 asc");
                                if ($table_bfw) {
                                    echo " 
          <div class='table-content-bfw'> <table class='table-bfw' ><thead><tr>
                  <th>" . __('Status name', 'bonus-for-woo') . "</th>
                  <th>" . __('Role slug (automatic)', 'bonus-for-woo') . "</th>
                  <th>" . __('Cashback percentage', 'bonus-for-woo') . "</th>
                  <th>" . __('Amount of orders', 'bonus-for-woo') . "</th>
                  <th>" . __('Action', 'bonus-for-woo') . "</th>
              </tr> </thead><tbody>
    ";

                                    foreach ($table_bfw as $bfw) {
                                        echo '<tr><td  >' . $bfw->name . '</td><td>' . $bfw->slug . '</td><td>' . $bfw->percent . '%</td><td>' . $bfw->summa_start . ' '.get_woocommerce_currency_symbol().'</td>
                  <td class="action_for_role"><input  class="pencil" type="button" value="' . $bfw->id . '"> 
                  <form method="post" action="" class="list_role_computy">
                  <input type="hidden" name="delete_role_summa_start" value="' . $bfw->summa_start . '" >
                  <input type="hidden" name="delete_role_percent" value="' . $bfw->percent . '" >
                  <input type="hidden" name="delete_role" value="' . $bfw->id . '" >
                  <input type="hidden" name="delete_role_slag" value="' . $bfw->slug . '" >
                  <input type="hidden" name="delete_role_name" value="' . $bfw->name . '" >
                  <input type="submit" value="+" class="delete_role-bfw" title="' . __('Delete status',
                                                'bonus-for-woo') . '" onclick="return window.confirm(\' ' . __('Are you sure you want to delete the status?',
                                                'bonus-for-woo') . ' \');">
                  </form></td></tr>';
                                    }
                                    echo '</tbody></table></div>';

                                } else {
                                    echo '<h3>' . __('To get started with the bonus system with cashback, create a new status for customers.',
                                            'bonus-for-woo') . '</h3>';
                                }
                                ?>
                            <form method="post" action="" id="add_role_form">
                                <input type="hidden" id="bfw_computy_ajax" name="bfw_computy_ajax"
                                       value="bfw_computy_ajax">
                                <table class="form-table" role="presentation">
                                    <tbody>
                                    <tr>
                                        <th scope="row"
                                            id="text_new_status"><?php echo __('New status for clients',
                                                'bonus-for-woo'); ?></th>
                                        <td class="table-bfw">
                                            <input type="text" id="add_role_form_name_role" name="name_role" value=""
                                                   placeholder="<?php echo __('Status name', 'bonus-for-woo'); ?>">
                                            <input type="number" id="add_role_form_percent_role" name="percent_role"
                                                   value="" min="0" step="any"
                                                   placeholder="<?php echo __('Cashback percentage',
                                                       'bonus-for-woo'); ?>">
                                            <input type="number" id="add_role_form_summa_start" name="summa_start"
                                                   value=""
                                                   min="0"
                                                   placeholder="<?php echo __('Amount of orders', 'bonus-for-woo'); ?>">
                                        </td>
                                    </tr>

                                    </tbody>
                                </table>

                                <p class="submit"><input type="submit" name="submit" id="submitaddrole"
                                                         class="bfw-admin-button"
                                                         value="<?php echo __('Add satus', 'bonus-for-woo'); ?>"></p>
                            </form>


                            <script>
                                function addrole() {
                                    let rolename = jQuery(this).parent().find('input[name="delete_role_name"]').val();
                                    let rolepercent = jQuery(this).parent().find('input[name="delete_role_percent"]').val();
                                    let rolesumma_start = jQuery(this).parent().find('input[name="delete_role_summa_start"]').val();
                                    jQuery("#add_role_form_name_role ").val(rolename).addClass('hidden')/*.prop('disabled', true)*/;
                                    jQuery("#add_role_form_percent_role").val(rolepercent);
                                    jQuery("#add_role_form_summa_start").val(rolesumma_start);
                                    jQuery("#text_new_status").text("<?php echo __('Change status',
                                        'bonus-for-woo');?> " + rolename);
                                    jQuery("#add_role_form #submitaddrole").val("<?php echo __('Edit',
                                        'bonus-for-woo');?>");
                                    jQuery("#bfw_computy_ajax").val("editrolehidden");
                                }

                                jQuery(function () {
                                    jQuery('.pencil').on('click', addrole);
                                })
                            </script>
                        </div>
                        <div class="tab_bfw tab_bfw2">
                            <form action="options.php" method="POST">
                                <?php
                                settings_fields('option_group_bonus');
                                do_settings_sections('primer_page_bonus');
                               // submit_button();
                                ?>
                                <button class="bfw-admin-button bfw-save-button" type="submit"><?php echo __('Save Changes', 'bonus-for-woo'); ?></button>
                        </div>
                        <?php
                        if ((new BfwRoles)->is_pro()) { ?>
                            <div class="tab_bfw tab_bfw5">
                                <?php
                                    settings_fields('option_pro_group_bonus');
                                    do_settings_sections('pro_page_bonus');
                                 //   submit_button();
                                    ?>
                                <button class="bfw-admin-button bfw-save-button" type="submit"><?php echo __('Save Changes', 'bonus-for-woo'); ?></button>
                             </div>
                        <?php } ?>
                        <div class="tab_bfw tab_bfw3">

                            <?php

                            settings_fields('option_mail_group_bonus');
                            do_settings_sections('mail_page_bonus');
                           // submit_button();
                            ?>
                            <button class="bfw-admin-button bfw-save-button" type="submit"><?php echo __('Save Changes', 'bonus-for-woo'); ?></button>
                        </div>
                        <div class="tab_bfw tab_bfw4">

                            <?php
                            settings_fields('option_trans_group_bonus');
                            do_settings_sections('trans_page_bonus'); ?>
                            <button class="bfw-admin-button bfw-save-button" type="submit"><?php echo __('Save Changes', 'bonus-for-woo'); ?></button>
                            </form>
                        </div>

                        <div class="tab_bfw tab_bfw6">
                            <h2><?php echo __('Export/Import', 'bonus-for-woo'); ?> <?php $val = get_option('bonus_option_name'); echo $val['label_points']; ?></h2>
                            <?php $filename = BONUS_COMPUTY_PLUGIN_DIR . '/export_bfw.csv';
                            // echo 'При нажатии кнопки "Создать CSV файл экспорта", рядом появиться ссылка для скачивания файла.';
                            echo '<p>' . __('When you click the "Create CSV export file" button, a link to download the file will appear next to it. You can download the file and edit it and then import it in the form below. After that, the bonus points data will be updated.',
                                    'bonus-for-woo') . '</p><br>';
                            if (file_exists($filename)) {
                                echo '<a class="bfw-admin-button" href="?page=bonus_for_woo-plugin-options&export_bfw_points=true">' . __('Recreate CSV export file','bonus-for-woo') . '</a> ';

                                echo ' <a href="' . BONUS_COMPUTY_PLUGIN_URL . 'export_bfw.csv" download>' . __('download CSV file','bonus-for-woo') . '</a>';
                            } else {
                                echo '<a class="bfw-admin-button" href="?page=bonus_for_woo-plugin-options&export_bfw_points=true">' . __('Сreate CSV export file','bonus-for-woo') . '</a> ';

                            }

                            echo '<br><br><br>';
                            echo '<form action="' . admin_url("admin-post.php") . '" class="bfw_export_bonuses"  method="post"  enctype="multipart/form-data">
                        <input type="hidden" name="action" value="bfw_export_bonuses" />
                        <lable for="bfw-file-export">' . __('Upload CSV file', 'bonus-for-woo') . '<br></lable>
                        <input name="file" type="file" id="bfw-file-export" required>
                        <label><input type="checkbox" id="by_email" name="by_email" value="1">'. __('Search for clients by email', 'bonus-for-woo').'</label>';
                            echo '<input class="bfw-admin-button" type="submit" value="' . __('import',
                                    'bonus-for-woo') . '" onclick="upload();return false;">
                        <div id="bfw-file-export-result"></div>
                    </form>';
                            ?>
                            <script type="text/javascript">
                                function upload() {
                                    let fileExtension = ['csv'];
                                    if (jQuery.inArray(jQuery('#bfw-file-export').val().split('.').pop().toLowerCase(), fileExtension) === -1) {
                                        jQuery('#bfw-file-export-result').html('<span style="color:red"><?php echo __('You can only use csv file format!',
                                            'bonus-for-woo');?></span>');
                                    } else {
                                        jQuery('.bfw_export_bonuses').addClass('bfv_uplouads');
                                        let formData = new FormData();
                                        formData.append("action", "upload-attachment");
                                        let fileInputElement = document.getElementById("bfw-file-export");
                                        let by_email = 0;
                                        if (document.getElementById('by_email').checked) {
                                        by_email = 1;
                                        }
                                        formData.append("async-upload", fileInputElement.files[0]);
                                        formData.append("name", fileInputElement.files[0].name);
                                        <?php $my_nonce = wp_create_nonce('media-form'); ?>
                                        formData.append("_wpnonce", "<?php echo $my_nonce; ?>");
                                        let xhr = new XMLHttpRequest();
                                        xhr.onreadystatechange = function () {
                                            if (xhr.readyState === 4 && xhr.status === 200) {
                                                //  console.log(xhr.responseText);
                                                jQuery.ajax({
                                                    type: 'POST',
                                                    url: "/wp-admin/admin-ajax.php",
                                                    data: {
                                                        action: 'bfw_export_bonuses',
                                                        response: xhr.responseText,
                                                        by_email: by_email,
                                                    },
                                                    success: function (data) {

                                                        if (data === 'good') {
                                                            jQuery('#bfw-file-export-result').html('<span style="color:green"><?php echo __('Export completed successfully!','bonus-for-woo');?></span>');
                                                            jQuery('.bfw_export_bonuses').removeClass('bfv_uplouads');
                                                            jQuery('#bfw-file-export').val('');
                                                        }
                                                    },
                                                    error: function (error) {
                                                        console.log(error);
                                                    }
                                                });
                                            }
                                        }
                                        xhr.open("POST", "/wp-admin/async-upload.php", true);
                                        xhr.send(formData);
                                    }
                                }
                            </script>


                            <hr>
                            <h2><?php // todo echo __('Export/Import settings', 'bonus-for-woo'); ?></h2>
                            <?php
/*
                            $response = json_encode($val);
м                             $response = json_encode_cyr($response);
                            echo $response;*/
                            ?>

                        </div>
                        <?php
                        if (!(new BfwRoles)->is_pro()) { ?>
                            <div class="tab_bfw tab_bfw5">
                                <h2><?php echo __('Bonus for woo Pro', 'bonus-for-woo'); ?></h2>

                                <p><?php echo __('Activate the Pro version for your site now and forever. Hurry up to buy cheaper, because with the addition of new features the price will increase.',
                                        'bonus-for-woo'); ?></p>
                                <p><?php echo __('Pro version price: <del>2500</del> <b>1990 rubles</b>',
                                        'bonus-for-woo'); ?></p>
                                <h3><?php echo __('What will the Pro version give?', 'bonus-for-woo'); ?></h3>
                                <ul>

                                    <li><?php echo __('The ability to earn points on your birthday.', 'bonus-for-woo'); ?></li>
                                    <li><?php echo __('The choice from which date to start counting the status of customers.', 'bonus-for-woo'); ?></li>
                                    <li><?php echo __('The ability to not only add bonus points to customers, but also remove them.', 'bonus-for-woo'); ?></li>
                                    <li><?php echo __('The ability to set the percentage of the order amount that can be spent by the client with points.', 'bonus-for-woo'); ?></li>
                                    <li><?php echo __('Ability to exclude products and categories that cannot be purchased with points.',  'bonus-for-woo'); ?></li>
                                    <li><?php echo __('Possibility to earn daily points for logging in.','bonus-for-woo'); ?></li>
                                    <li><?php echo __('The ability to credit cashback for excluded products and categories.',  'bonus-for-woo'); ?></li>
                                    <li><?php echo __('Possibility to earn points for registration.', 'bonus-for-woo'); ?></li>
                                    <li><?php echo __('Set up a product with 100% cashback (required to purchase points).', 'bonus-for-woo'); ?></li>
                                    <li><?php echo __('Ability to exclude roles from the bonus system.', 'bonus-for-woo'); ?></li>
                                    <li><?php echo __('Withdrawal of bonus points if the client has not made orders for a long time.', 'bonus-for-woo'); ?></li>
                                    <li><?php echo __('The ability to set a minimum order amount at which you can write off points.', 'bonus-for-woo'); ?></li>
                                    <li><?php echo __('Referral system.', 'bonus-for-woo'); ?></li>
                                </ul>
                                <hr>
                                <p><?php echo __('To purchase Bonus for Woo Pro, click on the "Buy" button, in the window that opens, enter the e-mail to which the activation key for your site will be sent and pay for the order following the instructions.',
                                        'bonus-for-woo'); echo '<br>'. sprintf( __('The key will be sent to your email: %s.', 'bonus-for-woo'), get_option('admin_email'));
                                      ?> <span style="color:#ff001d"><?php echo __('In case of problems, please contact info@computy.ru'); ?></span></p>

                                 <form action="https://computy.ru/buy_plugin.php" method="post">
                                     <input type="hidden" name="successURL" value="<?php echo get_site_url() . $_SERVER['REQUEST_URI']; ?>">
                                     <input type="hidden" name="label" value="<?php echo get_site_url(); ?>|bonus-for-woo|<?php echo BONUS_COMPUTY_VERSION; ?>|<?php echo get_option('admin_email'); ?>">
                                     <input type="submit" value="<?php echo __('Buy', 'bonus-for-woo'); ?>" class="buy_pro_button">
                                </form>



                                <p><?php echo __('Enter the activation key sent to your email.', 'bonus-for-woo'); ?></p>
                                <form method="post" action="">
                                    <input style="width: 250px" type="text" name="bonus-for-woo-pro" value=""/>
                                    <input type="submit" value="<?php echo __('Activate', 'bonus-for-woo'); ?>" class="active-pro-button">
                                </form>


                                <?php /*Обработчик активации версии Pro*/
                                if (isset($_POST['bonus-for-woo-pro'])) {
                                    $get = array(
                                        'key'  => sanitize_text_field($_POST['bonus-for-woo-pro']),
                                        'site' => get_site_url()
                                    );
                                    $curl = curl_init('https://computy.ru/API/api.php?' . http_build_query($get));
                                    curl_setopt($curl,    CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                                    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                                    $response = curl_exec($curl);
                                    $data = json_decode($response, true);
                                    curl_close($curl);


                                    if ($data['status'] == 'OK') {
                                        if ($data['response']) {
                                            /*Да, вот так просто.☺ Есть вопросы и предложения пиши https://t.me/ca666ko , пообщаемся.*/
                                            update_option('bonus-for-woo-pro', 'active');
                                        } else {
                                            echo '<div class="notice notice-error is-dismissible">' . __('The key is not correct! Contact info@computy.ru', 'bonus-for-woo') . '</div>';
                                        }
                                    } else {
                                        echo 'Код ошибки: ' . $data['error'];
                                    }
                                }
                                /*Обработчик активации версии Pro*/
                                ?>

                            </div>
                        <?php } ?>


                    </div>


                </div>
            </div>
            <?php
        }
    }


}