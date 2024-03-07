<?php
/*
 * Plugin Name:     Bonus for Woo
 * Version:         5.7.1
 * Text Domain:     bonus-for-woo
 * Plugin URI:      https://computy.ru/blog/bonus-for-woo-wordpress
 * Description:     This plugin adds a cashback system to the woocommerce functionality in the form of bonuses. Also, adding a percentage of cashback depending on the user's role. Also, changing the user's role, depending on the amount of all his purchases.
 * Author:          computy
 * Author URI:      https://computy.ru
 *
 * WC requires at least: 5.0
 * WC tested up to: 8.0.1
 *
 * License:           GNU General Public License v3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 */

if (!defined('ABSPATH')) {
    exit;
}
define('BONUS_COMPUTY_VERSION', '5.7.1'); /*версия плагина*/
define('BONUS_COMPUTY_VERSION_DB', '2'); /*версия базы данных*/
define('BONUS_COMPUTY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BONUS_COMPUTY_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );

require_once(BONUS_COMPUTY_PLUGIN_DIR . '/index/functions.php');/*-------функции плагина-------*/
require_once(BONUS_COMPUTY_PLUGIN_DIR . '/classes/BfwFunctions.php');/*-------функции плагина-------*/
require_once(BONUS_COMPUTY_PLUGIN_DIR . '/classes/BfwDB.php');/*-------инициализация базы данных-------*/
require_once(BONUS_COMPUTY_PLUGIN_DIR . '/classes/BfwPoints.php');/*-------инициализация бонусных баллов-------*/
require_once(BONUS_COMPUTY_PLUGIN_DIR . '/classes/BfwRoles.php');/*-------инициализация ролей-------*/
require_once(BONUS_COMPUTY_PLUGIN_DIR . '/classes/BfwEmails.php');/*-------email уведомления-------*/
require_once(BONUS_COMPUTY_PLUGIN_DIR . '/classes/BfwCoupons.php');/*-------инициализация Купонов-------*/
require_once(BONUS_COMPUTY_PLUGIN_DIR . '/classes/BfwHistory.php');/*-------Класс истории начислений-------*/
//require_once(BONUS_COMPUTY_PLUGIN_DIR . '/classes/BfwTimePoints.php');/*-------функционал временных баллов-------*/

/*-------Страница админки*-------*/
if (is_admin() || (defined('WP_CLI') && WP_CLI)) {
    require_once(BONUS_COMPUTY_PLUGIN_DIR . 'classes/BfwAdmin.php');

    add_action('init', array('BfwAdmin', 'init'));

    /*------- Создаем ссылку "настройки" на странице плагинов-------*/
    add_filter( 'plugin_action_links', function($links, $file){
        // Проверка - наш это плагин или нет
        if ( $file != plugin_basename(__FILE__) ){return $links;}

        $settings_link = sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=bonus_for_woo-plugin-options'), __( 'Settings', 'bonus-for-woo' ));
        array_unshift( $links, $settings_link );
        return $links;}, 10, 2 );
    /*------- Создаем ссылку "настройки" на странице плагинов-------*/
}
/*-------Страница админки-------*/


/*-------Действия после обновления-------*/
/**
 * Действие после обновления плагина
 *
 * @version      5.6.2
 */

add_action( 'upgrader_process_complete', 'bfw_update_completed', 10, 2 );

function bfw_update_completed($upgrader_object, $options ): void
{
    $our_plugin = plugin_basename( __FILE__ );
    if( $options['action'] === 'update' && $options['type'] === 'plugin') {
        foreach( $options['plugins'] as $plugin ) {
            if( $plugin == $our_plugin ) {
                set_transient( 'bfw_pro_updated', 1 );

            }
        }
    }
}
if( get_transient( 'bfw_pro_updated' ) ) {
    /*Проверка бд после обновления */
    (new BfwAdmin)-> bfw_search_pro();
    (new BfwDB)->checkDb();
    delete_transient( 'bfw_pro_updated' );
}
/*-------Действия после обновления-------*/


/*-------Проверка активен ли woocommerce-------*/
    $plugins = array();
    $plugin = 'woocommerce/woocommerce.php';
    $subsite_active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
    $network_active_plugins = apply_filters('active_plugins', get_site_option('active_sitewide_plugins'));
    if((empty($subsite_active_plugins) || !in_array($plugin, $subsite_active_plugins)) && (empty($network_active_plugins) || !array_key_exists($plugin, $network_active_plugins))) {

        /* Деактивируем bonus for woo*/
        add_action('admin_init', 'bonus_for_woo_true_plugin_off');
        function bonus_for_woo_true_plugin_off(): void
        {
            deactivate_plugins(plugin_basename(__FILE__));
        }

        add_action('admin_notices', 'bonus_for_woo_true_plugin_uvedomlenie');
        function bonus_for_woo_true_plugin_uvedomlenie(): void
        {
            echo '<div id="message" class="notice notice-warning is-dismissible">
	<p>' . __('Woocommers is not activated. The <b> Bonus for Woo </b> plugin cannot be activated.', 'bonus-for-woo') . '</p>
</div>';
            if (isset($_GET['activate'])) {
                unset($_GET['activate']);
            }
        }
    }
/*-------Проверка активирован ли woocommerce-------*/


/*-------Функция, которая запускается при активации плагина-------*/
register_activation_hook(__FILE__, 'bfwoo_computy_activate');
function bfwoo_computy_activate(): void
{
    delete_option('rewrite_rules');
    /*Проверка бд */
    (new BfwDB)->checkDb();
    (new BfwAdmin)-> bfw_search_pro();
}


/*------Функция, которая запускается при деактивации плагина------*/
register_deactivation_hook(__FILE__, 'bfwoo_computy_deactivate');
function bfwoo_computy_deactivate(): void
{
    delete_option('rewrite_rules');
}


/*-------Добавляем стили на фронте-------*/
add_action('wp_enqueue_scripts', 'bfwoo_computy_styles');
function bfwoo_computy_styles(): void
{
    wp_register_style('bonus-computy-style', plugin_dir_url(__FILE__) . '_inc/bonus-computy-style.css',array(),BONUS_COMPUTY_VERSION);
    wp_enqueue_style('bonus-computy-style');
}
/*-------Добавляем стили на фронте-------*/


/*-------Добавляем скрипты на фронте-------*/
add_action('wp_enqueue_scripts', 'bfwoo_computy_script');

function bfwoo_computy_script(): void
{
    wp_register_script('bonus-computy-script', plugin_dir_url(__FILE__) . '_inc/bonus-computy-script.js',array('jquery'), BONUS_COMPUTY_VERSION, true);
    wp_enqueue_script('bonus-computy-script');
}
/*-------Добавляем скрипты на фронте-------*/




/*-------Реферальная система-------*/
 require_once(BONUS_COMPUTY_PLUGIN_DIR . '/index/referal.php');
/*-------Реферальная система-------*/


/*-------Добавляем страницу бонусов в аккаунте клиента-------*/
require_once(BONUS_COMPUTY_PLUGIN_DIR . '/index/account.php');
/*-------Добавляем страницу бонусов в аккаунте клиента-------*/


/*-------Действие когда статус заказа выполнен-------*/
require_once(BONUS_COMPUTY_PLUGIN_DIR . '/index/status_completed.php');
/*-------Действие когда статус заказ выполнен-------*/


/*-------Добавление бонусов когда добавлен отзыв о товаре-------*/
require_once(BONUS_COMPUTY_PLUGIN_DIR . '/index/review.php');
/*-------Добавление бонусов когда добавлен отзыв о товаре-------*/


/*-------Вывод текста над формой регистрации-------*/
add_action( 'woocommerce_register_form_start', 'bfw_form_register', 2, 1 );
function bfw_form_register(): void
{
    $val = get_option('bonus_option_name');

    if(!empty($val['points-for-registration']) AND empty($val['register-points-only-referal'])){
        echo '<div class="bfw-in-register-form">'.sprintf(__('Register and get %s %s.', 'bonus-for-woo'), $val['points-for-registration'], (new BfwPoints())->pointsLabel(5)).'</div>';
    }
}
/*-------Вывод текста над формой регистрации-------*/


/*------- Действия  при регистрации пользователя-------*/
add_action('user_register', 'action_points_for_registration_bfw');
function action_points_for_registration_bfw($user_id): void
{
    (new BfwRoles)->updateRole($user_id);

    if ((new BfwRoles)->is_pro()) {

        $val = get_option('bonus_option_name');
        $pointsforeg = $val['points-for-registration'];

        $mojno = 1;
        /*Только для рефералов */

            if (!empty($val['register-points-only-referal'])) {
                /*Только для рефералов*/
                $cookie_val = isset($_COOKIE['bfw_ref_cookie_set']) ? sanitize_text_field(wp_unslash($_COOKIE['bfw_ref_cookie_set'])) : '';
                if (empty($cookie_val)) {
                    $mojno = 0;
                }
            }

/*Начисление баллов*/
        if($mojno==1){
            if($pointsforeg>0){
                (new BfwPoints)->updatePoints($user_id,$pointsforeg);

                $prichina  = sprintf(__('%s for registration','bonus-for-woo'), (new BfwPoints())->pointsLabel(5));
                /*Записываем в историю*/
                (new BfwHistory)->add_history($user_id,'+',$pointsforeg,'0',$prichina);
                /*Записываем в историю*/

                /*Отправляем email клиенту*/
                $user = get_userdata($user_id);
                /*Шаблон письма*/
                $val = get_option('bonus_option_name');

                $text_email = $val['email-when-register-text'] ?? '';


                $title_email = $val['email-when-register-title'] ?? __('Bonus points have been added to you!', 'bonus-for-woo');
                $get_referral = get_user_meta( $user_id, 'bfw_points_referral', true );
                $text_email_array = array('[referral-link]'=> esc_url(site_url().'?bfwkey='. $get_referral),'[user]'=>$user->display_name,'[points]'=>$pointsforeg,'[total]'=>$pointsforeg,'[cause]'=>$prichina);
                $message_email = (new BfwEmail)->template($text_email,$text_email_array);
                /*Шаблон письма*/


                   if(!empty($val['email-when-register'])){
                        (new BfwEmail)->getMail($user_id, '', $title_email, $message_email);
                    }
                /*Отправляем email клиенту*/
            }
        }


    }
}
/*-------Начисление баллов при регистрации пользователя-------*/

/*-------Действия при авторизации пользователя-------*/
add_action( 'wp_login', 'bfw_update_role_when_user_logged_in', 10, 2 );
function bfw_update_role_when_user_logged_in( $user_login, $user ): void
{
    if((new BfwRoles)->is_invalve($user->ID)) {

        // Обновление статуса при авторизации пользователя
        (new BfwRoles)->updateRole($user->ID);

        //Начисление ежедневных баллов за первый вход
        (new BfwPoints)->add_every_days($user->ID);
    }
}

/*-------Обновление статуса баллов при авторизации пользователя-------*/

/*
* Вывод списания баллов в корзине и в оформлении товаров.
* Обрабатываем отправленные данные, когда клиент хочет потратить бонусы.
*/

/*-------Вывод кешбэка в корзине и в оформлении товара-------*/
require_once(BONUS_COMPUTY_PLUGIN_DIR . '/index/cashback_in_cart.php');
/*-------Вывод кешбэка в корзине и в оформлении товара-------*/


/*-------Списание баллов в корзине и оформлении заказа-------*/
require_once(BONUS_COMPUTY_PLUGIN_DIR . '/index/spisanie_cart_checkout.php');
/*-------Списание баллов в корзине и оформлении заказа-------*/


/*-------Трата баллов-------*/
 add_action('wp_ajax_computy_trata_points', 'bfwoo_trata_points');
 add_action('wp_ajax_nopriv_computy_trata_points', 'bfwoo_trata_points');

function bfwoo_trata_points(): void
{
    $balls = sanitize_text_field($_POST['computy_input_points']);
    $balls = (new BfwPoints())->roundPoints($balls);
    $max_points =  sanitize_text_field($_POST['maxpoints']);
    $max_points = (new BfwPoints())->roundPoints($max_points);
    $userid = get_current_user_id();
if($balls > $max_points){$balls=$max_points;}
    $allpoint = (new BfwPoints)->getPoints($userid);
    if ($allpoint < $balls) {
        (new BfwPoints)->updateFastPoints($userid,$allpoint);//выносим баллы на скидку
    } else {
        (new BfwPoints)->updateFastPoints($userid,$balls);//выносим баллы на скидку
    }
    WC()->cart->calculate_totals();
    wp_send_json_success($_POST['redirect']);
}
/*-------Трата баллов-------*/

add_filter( 'woocommerce_get_query_vars', function ($vars) {
    $vars[ 'bonuses' ] = 'bonuses';
    return $vars;
} );

/*-------Получение баллов с купона-------*/
add_action('wp_ajax_bfw_take_coupon_action', 'bfw_take_coupon_action');

function bfw_take_coupon_action(): void
{
    $code_coupon = sanitize_text_field($_POST['code_coupon']);
    $userid = get_current_user_id();
    $zapros = (new BfwCoupons)->enterCoupon($userid,$code_coupon);
    if($zapros=='not_coupon'){
        $code_otveta = 404;
        $message = __('Sorry, no such coupon found.','bonus-for-woo');
    }else{
        $message = __('Coupon activated.','bonus-for-woo');
        $code_otveta = 200;
    }

    $return = array(
        'redirect'   => $_POST['redirect'],
        'message'        => $message,
        'cod'       => $code_otveta
    );
    wp_send_json_success( $return );
     }
/*-------Получение баллов с купона-------*/


/*-------Действие при нажатии кнопки экспорта баллов

 * @since 4.4.0
 * @version 4.4.0
 */
if (isset($_GET['export_bfw_points'])) {
    if (is_admin()){
    $buffer = fopen(BONUS_COMPUTY_PLUGIN_DIR . '/export_bfw.csv', 'w');
    fputs($buffer, chr(0xEF) . chr(0xBB) . chr(0xBF));
    global $wpdb;
    $users = $wpdb->get_results("SELECT * FROM $wpdb->users ORDER BY ID");

    $titlexport = array('User id','User name','Email','Points','Comment' );
    fputcsv($buffer, $titlexport, ',');

    $prods = array();
    foreach($users as $user) {
        $points = get_user_meta($user->ID, 'computy_point', true) ?? 0;
        $prods[] = array(
            'id'       => $user->ID,
            'name'     => $user->user_nicename,
            'email'     =>$user->user_email,
            'points'   =>  $points,
            'comment'    =>''
        );
    }
    foreach($prods as $val) {
        fputcsv($buffer, (array)$val, ',');
    }
    fclose($buffer);
    }
}
/*-------Действие при нажатии кнопки экспорта баллов-------*/


/*-------Экспорт csv файла бонусов

 * @since 4.4.0
 * @version 5.3.3
 * */
add_action('wp_ajax_bfw_export_bonuses', 'bfw_export_bonuses');
add_action('wp_ajax_nopriv_bfw_export_bonuses', 'bfw_export_bonuses');
function bfw_export_bonuses(): void
{
    $array = stripslashes($_POST['response']);
    $resp = json_decode($array, JSON_UNESCAPED_SLASHES);
    $url_export_file = $resp['data']['url']; /*ссылка на загруженный файл экспорта*/

//Сколько строк обрабатывать в каждом пакете
    $limit = 100;

    $fileHandle = fopen($url_export_file, "r");
    if ($fileHandle === FALSE)
    {
        die(__('Error opening','bonus-for-woo').' '.htmlspecialchars($url_export_file));
    }
    $offset = 0;   $by_email=false;
    if($_POST['by_email']=='1'){
     $by_email=true;
    }
    while(!feof($fileHandle))
    {
//Иди туда, где мы были, когда мы закончили последнюю итерацию
        fseek($fileHandle, $offset);
        $i = 0;
        while (($currRow = fgetcsv($fileHandle)) !== FALSE)
        {
            $i++;
            $email = (string)$currRow[2];
            if($by_email){
                $user = get_user_by( 'email', $email);
                $id = $user->ID;
            }else{
                $id = (int)$currRow[0];
            }

            $point = (float)$currRow[3];
            $user_point  = (float)get_user_meta($id, 'computy_point', true);
            $comment = (string)$currRow[4] ?? '';

            if(get_user_meta($id, 'computy_point', true)!=$point){

                //Проверяем добавились ли баллы или убавились и записываем в историю
                if($user_point>$point){
                    $raznica = $user_point-$point;
                    (new BfwHistory)->add_history($id, '-', $raznica, '0', $comment);
                }elseif ($user_point<$point){
                    $raznica = $point-$user_point;
                    (new BfwHistory)->add_history($id, '+', $raznica, '0', $comment);
                }

                update_user_meta($id, 'computy_point', $point);
                /*Обновляем баллы пользователям*/
            }
            if($i >= $limit)
            {
                $offset = ftell($fileHandle);
                break;
            }
        }
    }
    fclose($fileHandle);
    echo 'good';
    exit();
}
/*-------Экспорт csv файла бонусов-------*/





/*-------Добавляем скидку-------*/

 add_action('woocommerce_cart_calculate_fees', 'bfwoo_add_fee', 10, 1);

function bfwoo_add_fee(): void
{
    $val = get_option('bonus_option_name');

    if ((new BfwPoints)->getFastPoints(get_current_user_id()) > 0) {
        $userid = get_current_user_id();
        $computy_point_old = (new BfwPoints)->getFastPoints($userid); //узнаем баллы которые он решил списать
        $computy_point_old = (new BfwPoints())->roundPoints($computy_point_old);

        if (empty($val['fee-or-coupon'])) {
            /* бонусы с помощью комиссий*/
            WC()->cart->add_fee($val['bonus-points-on-cart'], -$computy_point_old, false);
        }else{
            /* Бонусы с помощью купонов*/
            global $woocommerce;
            $cart_discount = mb_strtolower($val['bonus-points-on-cart']);
            if ( isset( $woocommerce->cart ) ) {
                if ( ! $woocommerce->cart->has_discount( $cart_discount ) ) {
                    if ( $woocommerce->cart->applied_coupons ) {
                        foreach ( $woocommerce->cart->applied_coupons as $code ) {
                            if ( $cart_discount === $code ) {
                                return;
                            }
                        }
                    }
                    $woocommerce->cart->applied_coupons[] = $cart_discount;
                }
            }
        }

    }
}


/*Кнопка удаления в подытоге */
 add_filter ( 'woocommerce_cart_totals_fee_html', 'bfw_button_delete_fast_point', 10, 2  );
function bfw_button_delete_fast_point( $cart_totals_fee_html, $fee ) {
    if ( isset( $fee ) && ! empty( $fee ) ) {
        $fee_name = $fee->name;
        $val = get_option('bonus_option_name');
        $cart_discount = $val['bonus-points-on-cart'];
        if ( isset( $fee_name ) && $cart_discount == $fee_name ) {
            $cart_totals_fee_html = $cart_totals_fee_html . '<a href="javascript:void(0);" id="bfw_remove_cart_point" title="'.$val['remove-on-cart'].'">' . $val['remove-on-cart'] . '</a>';
        }
    }
    return $cart_totals_fee_html;
}


if (!empty($val['fee-or-coupon'])) {
    /*Создаем виртуальный купон*/
    add_filter('woocommerce_get_shop_coupon_data', 'get_virtual_coupon_data_bfw', 10, 2);
    /*Вид купонов в корзине*/
    add_filter('woocommerce_cart_totals_coupon_html', 'bfw_coupon_html', 99, 2);

    /* Убираем "купон" в корзине*/
    add_filter( 'woocommerce_cart_totals_coupon_label', 'woocommerce_change_coupon_label_bfw', 10, 2  );
}
function get_virtual_coupon_data_bfw($response, $coupon_data){
    $val = get_option('bonus_option_name');
    $cart_discount = mb_strtolower($val['bonus-points-on-cart']);
    if ( $coupon_data == $cart_discount ) {
    $userid = get_current_user_id();
    $computy_point_old = (new BfwPoints)->getFastPoints($userid); //узнаем баллы которые он решил списать
    $computy_point_old = (new BfwPoints())->roundPoints($computy_point_old);
    $discount_type = 'fixed_cart';
    $coupon = array(
        'id' => time() . rand( 2, 9 ),
        'amount' => $computy_point_old,
        'individual_use' => false,
        'product_ids' => array(),
        'exclude_product_ids' => array(),
        'usage_limit' => '',
        'usage_limit_per_user' => '',
        'limit_usage_to_x_items' => '',
        'usage_count' => '',
        'expiry_date' => '',
        'apply_before_tax' => 'yes',
        'free_shipping' => false,
        'product_categories' => array(),
        'exclude_product_categories' => array(),
        'exclude_sale_items' => false,
        'minimum_amount' => '',
        'maximum_amount' => '',
        'customer_email' => '',
    );
    $coupon['discount_type'] = $discount_type;

    return    $coupon;
    }
}

function bfw_coupon_html($html, $coupon)
{
    $val = get_option('bonus_option_name');
    $cart_discount =  mb_strtolower($val['bonus-points-on-cart']);

    $coupon_data = mb_strtolower($coupon->get_code());
    $userid = get_current_user_id();
    $computy_point_old = (new BfwPoints)->getFastPoints($userid); //узнаем баллы которые он решил списать
    $computy_point_old = (new BfwPoints())->roundPoints($computy_point_old);

if(strtolower($coupon_data)=== strtolower($cart_discount)){
    $html =  ' <span class="woocommerce-Price-amount amount">-'.wc_price($computy_point_old).'</span>
    <a href="javascript:void(0);" id="bfw_remove_cart_point" title="'.$val['remove-on-cart'].'">' . $val['remove-on-cart'] . '</a>';
}
  return $html;

}

function woocommerce_change_coupon_label_bfw( $sprintf, $coupon ) {
      $val = get_option('bonus_option_name');

      $cart_discount =  mb_strtolower($val['bonus-points-on-cart']);
    $coupon_data   = $coupon->get_data();
    if ( ! empty( $coupon_data ) ) {
        if ( strtolower( $coupon_data['code'] ) === strtolower( $cart_discount ) ) {
            $sprintf = $val['bonus-points-on-cart'];
        }
    }
    return $sprintf;
}



// Исключаем скидку из налогов
function excludeCartFeesTaxes($taxes, $fee, $cart): array
{
    return [];
}
add_action('woocommerce_cart_totals_get_fees_from_cart_taxes', 'excludeCartFeesTaxes', 10 ,3);
/*-------Добавляем скидку-------*/


/*-------Очистка схваченных баллов-------*/
add_action('wp_ajax_clear_fast_bonus', 'bfwoo_clean_fast_bonus');
add_action('wp_ajax_nopriv_clear_fast_bonus', 'bfwoo_clean_fast_bonus');
function bfwoo_clean_fast_bonus(): void
{
    $userid = get_current_user_id();
    (new BfwPoints)->updateFastPoints($userid,0);
    $val = get_option('bonus_option_name');
    $cart_discount =  mb_strtolower($val['bonus-points-on-cart']);
    if ( isset( WC()->cart ) ) {
        if ( null !== WC()->cart->get_applied_coupons() && ! empty( WC()->cart->get_applied_coupons() ) ) {
            foreach ( WC()->cart->get_applied_coupons() as $code ) {
                $coupon = new WC_Coupon( $code );
                if ( strtolower( $code ) === mb_strtolower( $cart_discount ) ) {
                    WC()->cart->remove_coupon( $code );
                }
            }
        }
    }

    wp_send_json_success($_POST['redirect']);
}
/*-------Очистка схваченных баллов-------*/


/*-------Шорт коды-------*/
require_once(BONUS_COMPUTY_PLUGIN_DIR . '/index/shortcodes.php');
/*-------Шорт коды-------*/


/*-------Текст на странице товара (сколько бонусов получите)-------*/
require_once(BONUS_COMPUTY_PLUGIN_DIR . '/index/single-product-page.php');
/*-------текст на странице товара (сколько бонусов получите)-------*/


/*------- Бездействие и день рождение-------*/
require_once(BONUS_COMPUTY_PLUGIN_DIR . '/index/cron.php');
/*-------Бездействие и день рождение-------*/



/*-------Очищение истории при удалении пользователя-------*/
function bfw_when_delete_user(int $user_id ): void
{
    (new BfwHistory)->clearAllHistoryUser($user_id);
 }
add_action( 'delete_user', 'bfw_when_delete_user' );
/*-------Очищение истории при удалении пользователя-------*/


/*-------Удаление временных баллов при очистке корзины-------*/

function action_woocommerce_before_cart_item_quantity_zero(): void
{
    (new BfwPoints)->updateFastPoints(get_current_user_id(),0);
}
add_action( 'woocommerce_remove_cart_item', 'action_woocommerce_before_cart_item_quantity_zero', 10, 1 );

/*Действие сработает при изменении количества товаров*/
add_action( 'woocommerce_cart_item_set_quantity', 'bfw_cart_item_set_quantity', 10, 3 );
function bfw_cart_item_set_quantity( $cart_item_key, $quantity, $that ): void
{
    $val = get_option( 'bonus_option_name' );
    $qty_cart= isset( $val['clear-fast-bonus-were-qty-cart'] ) ? intval( $val['clear-fast-bonus-were-qty-cart'] ) : 0;

    if($qty_cart){
        (new BfwPoints)->updateFastPoints(get_current_user_id(),0);
    }

}

/*-------Вывод списанных баллов в редактировании заказа админом-------*/
add_action( 'woocommerce_admin_order_totals_after_tax', array('BfwFunctions', 'bfw_inadminzakaz'));
/*-------Вывод списанных баллов в редактировании заказа админом-------*/


/**
 * Возможность менеджерам настраивать плагин
 * @version 3.2.0
 **/
$shop_manager = get_role( 'shop_manager' );
$shop_manager->add_cap( 'manage_options' );
$shop_manager->add_cap( 'edit_users' );
$shop_manager->add_cap( 'edit_user' );
function bonus_for_woo_manager_role_edit_capabilities( $roles ) {
    global $wpdb;
    $table_bfw = $wpdb->get_results("SELECT *,summa_start FROM " . $wpdb->prefix . "bfw_computy  ORDER BY summa_start + 0 asc");
    foreach ($table_bfw as $bfw) {
        $roles[] =   $bfw->slug;
    }
    $roles[] = 'subscriber';
    return $roles;
}
add_filter( 'woocommerce_shop_manager_editable_roles', 'bonus_for_woo_manager_role_edit_capabilities' );
/*-------Возможность менеджерам настраивать плагин-------*/


/*-------Поле дня рождения-------*/
add_action( 'woocommerce_edit_account_form_start', 'bfw_dob_account_details' );
function bfw_dob_account_details(): void
{
    $val = get_option( 'bonus_option_name' );
    if( isset($val['birthday']) AND $val['birthday']>0){

    $user = wp_get_current_user();
    $disabled = '';
   if(!empty(esc_attr( $user->dob ))) {
     $disabled = 'disabled';
   }
    ?>
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="dob"><?php esc_html_e( 'Date of birth', 'bonus-for-woo' ); ?></label>
        <input type="date" class="woocommerce-Input woocommerce-Input--text input-text" name="dob" id="dob" value="<?php echo esc_attr( $user->dob ); ?>" <?php echo $disabled; ?>/>
    </p>
    <?php
    }
}

add_action( 'woocommerce_save_account_details', 'bfw_dob_save_account_details' );
function bfw_dob_save_account_details( $user_id ): void
{
    if ( isset( $_POST['dob'] )) {
        update_user_meta( $user_id, 'dob', sanitize_text_field( $_POST['dob'] ) );
    }
}
/*-------Поле  дня рождения-------*/


/*-------Если есть исключенный метод оплаты-------*/
if (!empty($val['exclude-payment-method'])){
/* Добавляет обновление страницы при выборе метода оплаты*/
add_action('wp_footer', 'update_page_if_change_payment_method');
function update_page_if_change_payment_method(): void
{
    $val = get_option('bonus_option_name');
    $expm = $val['exclude-payment-method'] ?? array();
 if(isset($expm[0])){?>
     <script>

         jQuery(document).ready(function($){
            let ert = $('input[name^="payment_method"]:checked').val();
             if(ert==='<?php echo $expm[0]; ?>'){
                 $('.order-cashback').hide();
                 $('#computy-bonus-message-cart').hide();
                 $('.bfw-how-match-cashback').hide();
             }

             $( 'form.checkout' ).on( 'change', 'input[name^="payment_method"]', function() {
                 let et = $(this).val();
                 if(et==='<?php echo $expm[0]; ?>'){
                    $('.order-cashback').hide();
                    $('#computy-bonus-message-cart').hide();
                     $('.bfw-how-match-cashback').hide();
                     $( "#bfw_remove_cart_point" ).trigger( "click" );
                }else{
                    $('.order-cashback').show();
                    $('#computy-bonus-message-cart').show();
                     $('.bfw-how-match-cashback').show();
                }
             });
         });
     </script>
 <?php }
    ?>
   <script>jQuery(document).ready(function($){
           $( 'form.checkout' ).on( 'change', 'input[name^="payment_method"]', function() {
               $(document.body).trigger('update_checkout');
           });
       });
   </script>
    <?php
}
}
/*-------Если есть исключенный метод оплаты-------*/


/*-------Переводы @version 5.2.0 -------*/
add_action('plugins_loaded', array('BfwFunctions', 'lang_load_bonus_for_woo'));
/*-------переводы-------*/