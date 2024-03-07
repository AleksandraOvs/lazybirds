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

// Регистрируем возможности темы
add_action( 'after_setup_theme', function(){
    
	add_theme_support( 'menus' );
	add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );

});

/**
 * Страница опций для настроек темы
 */
if( function_exists('acf_add_options_page') ) {

    acf_add_options_page(array(
        'page_title'    => 'Настройки темы MM76',
        'menu_title'    => 'Настройки темы MM76',
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));

}

/**
 * поддержка вукомерц
 */
add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

/**
 * убираем заголовок
 */
add_filter('woocommerce_show_page_title', 'bbloomer_hide_shop_page_title');
function bbloomer_hide_shop_page_title($title) {
   $title = false;
   return $title;
}