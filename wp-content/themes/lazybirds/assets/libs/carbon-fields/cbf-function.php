<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

add_action( 'after_setup_theme', 'crb_load' );
function crb_load() {
  require get_stylesheet_directory() . '/assets/libs/carbon-fields/carbon-fields-3.3.2/vendor/autoload.php';
  \Carbon_Fields\Carbon_Fields::boot();
}
/*
* Основная консоль сайта настройки произвольных полей   
*
**/
require get_stylesheet_directory() . '/assets/libs/carbon-fields/cbf-custom-fields/cbf-theme-options.php';

/*
* Основная консоль сайта настройки мета полей   
*
**/
require get_stylesheet_directory() . '/assets/libs/carbon-fields/cbf-custom-fields/cbf-post-meta.php';

add_filter( 'carbon_fields_theme_options_container_admin_only_access', '__return_false' );
