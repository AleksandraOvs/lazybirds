<?php
/**
**** Разработка и продвижение сайтов *****
**** по вопросам доработки/разработки ****
**** MM76.RU *****************************
**** Tel: +7 920 650 76-76 ***************
**** WhatsApp: +7 920 650 76-76 **********
**** Tg: @mm76_ru ************************
**** Site: https://mm76.ru/ ***************
*/

require_once( __DIR__ . '/inc/init.php' );
require_once( __DIR__ . '/inc/wc.php' );
require_once( __DIR__ . '/inc/profile.php');

add_action( 'template_redirect', 'redirect_to_specific_page' );

function redirect_to_specific_page() {
    if ( is_page('my-account') && ! is_user_logged_in() ) {
        wp_redirect( get_the_permalink( 642 ), 301 ); 
        exit;
    }
}