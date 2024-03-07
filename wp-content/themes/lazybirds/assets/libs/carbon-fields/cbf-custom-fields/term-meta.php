<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;


add_action( 'carbon_fields_register_fields', 'crb_attach_term_meta' ); // Для версии 2.0 и выше
// add_action( 'carbon_register_fields', 'crb_attach_theme_options' ); // Для версии 1.6 и ниже
function crb_attach_term_meta() {
Container::make( 'term_meta' , ' Настройки Term' )
        // ->show_on_taxonomy('category')
        ->show_on_level( 1 ) // указываем вложенность при которой необходимо отображать контейнер 
        //->show_on_taxonomy(['post_tag','category']) //Теперь поле видно и в метках и в рубриках 
		->add_fields( [
			Field::make('color','title_color','Цвет заголовка'),
			Field::make('image','thumb','Миниатюра'),
		] );

}