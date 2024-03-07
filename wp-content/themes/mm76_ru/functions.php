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

function site_scripts(){
    //wp_deregister_script('jquery');
    //wp_enqueue_script ('jquery', get_stylesheet_directory_uri() . '/assets/js/jquery-3.7.1.js', array(), null, true);
    //wp_enqueue_script ('jquery-ajax', 'https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js', array(), null, true);
    wp_enqueue_script ('swiper-js-bundle', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js', array(), null, true);
    wp_enqueue_script ('fancy-js', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js', array(), null, true);
    wp_enqueue_script ('fa-js', 'https://kit.fontawesome.com/66298a2362.js', array(), null, true);
    wp_enqueue_script ('toastr-js', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js', array(), null, true);
    wp_enqueue_script ('meskesinput-js', get_stylesheet_directory_uri() . '/assets/js/jquery.maskedinput.min.js', array(), null, true);
    wp_enqueue_script ('konte-js', get_stylesheet_directory_uri() . '/assets/js/konte_js_notify.min.js', array(), null, true);
    wp_enqueue_script ('theme-js', get_stylesheet_directory_uri() . '/assets/js/scripts.js', array(), null, true);
    wp_enqueue_script ('js-accordion-ui', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', array('jquery'), null, true);
    wp_enqueue_script ('js-accordion', get_stylesheet_directory_uri() . '/assets/js/accordion.js', array('jquery'), null, true);

	wp_enqueue_style( 'wc-styles', get_stylesheet_directory_uri() . '/assets/css/wc.css', array(), time() );
    wp_enqueue_style( 'swiper-styles', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css', array(), time() );
	wp_enqueue_style( 'facy-styles', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css', array(), time() );
	wp_enqueue_style( 'bundle-styles', get_stylesheet_directory_uri() . '/assets/css/bundle.css', array(), time() );
    wp_enqueue_style( 'konte-styles', get_stylesheet_directory_uri() . '/assets/css/konte.css', array(), time() );
    wp_enqueue_style( 'theme-styles', get_stylesheet_directory_uri() . '/assets/css/styles.css', array(), time() );
    wp_enqueue_style( 'add-styles', get_stylesheet_directory_uri() . '/assets/css/add-styles.css', array(), time() );
}
add_action( 'wp_enqueue_scripts', 'site_scripts' );

function my_customize_register( $wp_customize ) {
    $wp_customize->add_setting('header_logo', array(
        'default' => '',
        //'height' => '48',
        'width' => '84',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'header_logo', array(

        'section' => 'title_tagline',
        'label' => 'Логотип Header'

    )));
    $wp_customize->add_setting('footer_logo', array(
        'default' => '',
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'footer_logo', array(
        'section' => 'title_tagline',
        'label' => 'Логотип Footer'
    )));
}
add_action( 'customize_register', 'my_customize_register' );
