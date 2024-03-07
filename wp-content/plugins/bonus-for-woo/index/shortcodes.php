<?php
/*-------Шорткоды-------*/

/*-------Шорткод: вывод статуса клиента-------*/
add_shortcode('bfw_status', 'bfw_status_func');
function bfw_status_func(){
    $roles = (new BfwRoles)->getRole(get_current_user_id());
     return $roles['name'];
}


/*-------Шорткод: вывод процента кешбэка-------*/
add_shortcode('bfw_cashback', 'bfw_cashback_func');
function bfw_cashback_func(): string
{
    $roles = (new BfwRoles)->getRole(get_current_user_id());
    return $roles['percent']. '%';
}


/*-------Шорткод: вывод количества баллов-------*/
add_shortcode('bfw_points', 'bfw_points_func');
function bfw_points_func(): float
{
 return (new BfwPoints)->getPoints(get_current_user_id());
}


/*-------Шорткод: вывод аккаунта пользователя-------*/
add_shortcode('bfw_account', 'bfwoo_computy_content2');
function bfwoo_computy_content2(): string
{ ob_start();
    if (file_exists(get_stylesheet_directory() . '/bonus-for-woo/account.php')) {
        get_template_part( 'bonus-for-woo/account' );
    } else {
        require_once(BONUS_COMPUTY_PLUGIN_DIR . '/templates/account.php');
    }
    $string = ob_get_contents();

    ob_end_clean();
    return $string;
}
/*-------Шорткод: вывод реферальной системы-------*/
add_shortcode('bfw_account_referral', 'bfw_account_referal');


/*-------Шорткод: Вывод реферальной ссылки клиента-------*/
add_shortcode('bfw_ref', 'bfw_ref_func');
function bfw_ref_func(): ?string
{
    $userid = get_current_user_id();
    $get_referral = get_user_meta($userid, 'bfw_points_referral', true);
    if($get_referral != '') {
        return '<div class="bonus_computy_account bfw-account_referral"><span class="title_bca">' . __('My referral link',
                'bonus-for-woo') . ':</span> <code id="code_referal" class="value_bca">' . esc_url(site_url() . '?bfwkey=' . $get_referral) . '</code> <span  title="'.__('Copy link', 'bonus-for-woo').'"  id="copy_referal"></span><span id="copy_good"></span> </div>';
    }else{
        return null;
    }
}