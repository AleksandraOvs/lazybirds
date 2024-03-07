<?php
/**
 * Генерация реферального кода
 *
 * @since   1.9.0
 * @version 4.4.0
 * @return  string
 */

function bfw_create_referal_code(): string
{
    $userid = get_current_user_id();
    $length = 10;
    $refkey = '';
    $alphabets = range( 'A', 'Z' );
    $numbers = range( '0', '9' );
    $final_array = array_merge( $alphabets, $numbers );
    while ( $length-- ) {
        $key = array_rand( $final_array );
        $refkey .= $final_array[ $key ];
    }
    return $refkey.$userid;
}


$val = get_option('bonus_option_name');

/*Если включена реферальная система*/
if(!empty($val['referal-system'])) {

    add_action('init', 'bfw_set_cookies');
    //создаем пользователям инвайт(от какого пользователя пришел)(только для незарегистрированных)
    /* если незарегистрированный зашел по рефералке, то сохраняем ее в сессию */
    function bfw_set_cookies()
    {

        if (!is_user_logged_in() AND !is_admin()) {
            $bfw_days_cookies = 365;
            if (isset($_GET['bfwkey']) && !empty($_GET['bfwkey'])) {// phpcs:ignore WordPress.Security.NonceVerification
                $bfw_referral_key = sanitize_text_field(wp_unslash($_GET['bfwkey']));// phpcs:ignore WordPress.Security.NonceVerification
                $referral_link = trim($bfw_referral_key);// phpcs:ignore WordPress.Security.NonceVerification
                setcookie('bfw_ref_cookie_set', $referral_link, time() + (86400 * $bfw_days_cookies), '/');
            }
        }

    }

    /**
     * Действие при регистрации пользователя
     *
     * @since   1.9.0
     * @version 4.4.0
     * @param int $user_id
     */
    add_action('user_register', 'register_po_invate');
    function register_po_invate(int $user_id): void
    {

        //Генерируем реферальную ссылку
        $referral_key = bfw_create_referal_code();
        update_user_meta( $user_id, 'bfw_points_referral', $referral_key );

        // У которого в сессии есть   bfw_ref_cookie_set
        $cookie_val = isset($_COOKIE['bfw_ref_cookie_set']) ? sanitize_text_field(wp_unslash($_COOKIE['bfw_ref_cookie_set'])) : '';
        $retrive_data = $cookie_val;
        if (!empty($retrive_data)) {
            $args['meta_query'] = array(
                array(
                    'key' => 'bfw_points_referral',
                    'value' => trim($retrive_data),
                    'compare' => '==',
                ),
            );

            $refere_data = get_users($args);
            $refere_id = $refere_data[0]->data->ID;
            update_user_meta($user_id, 'bfw_points_referral_invite', $refere_id);
        }
    }
}