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

register_nav_menus(
    array(
        'menu-header' => esc_html__( 'Верхнее меню', 'mm' ),
        'menu-footer' => esc_html__( 'Меню в футере', 'mm' ),
    )
);

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

/**
 * регистрация виджетов
 */

 function theme_widgets_init() {
	
	register_sidebar(
		array(
			'name'          => esc_html__( 'header-search', 'theme' ),
			'id'            => 'sidebar-search-header',
			'description'   => esc_html__( ' Виджет для поиска', 'theme' ),
			'before_widget' => '<div id="%1$s" class="search-form">',
			'after_widget'  => '</div>',
		)
	);

    register_sidebar(
		array(
			'name'          => esc_html__( 'footer-contacts', 'theme' ),
			'id'            => 'footer-contacts-widget',
			'description'   => esc_html__( ' Виджет для контактов в футере', 'theme' ),
			'before_widget' => '<div id="%1$s" class="footer-contacts__item">',
			'after_widget'  => '</div>',
		)
	);

    register_sidebar(
		array(
			'name'          => esc_html__( 'footer-docs', 'theme' ),
			'id'            => 'footer-docs-widget',
			'description'   => esc_html__( ' Виджет для ссылок на документы', 'theme' ),
			'before_widget' => '<div id="%1$s" class="footer-docs__item">',
			'after_widget'  => '</div>',
		)
	);

}
add_action( 'widgets_init', 'theme_widgets_init' );