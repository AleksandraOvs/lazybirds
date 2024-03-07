<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;


add_action( 'carbon_fields_register_fields', 'cbf_attach_post_meta' ); // Для версии 2.0 и выше
function cbf_attach_post_meta() {


  /** 
   * # Вывод вкладки Уход
   */
  Container::make( 'post_meta',__('Уход'))
    ->set_context( 'normal' )
    ->show_on_post_type( 'product')
    ->add_fields( [
        Field::make( 'rich_text', 'cbf_product_care',  'Текст' ), 
  ] );
}

