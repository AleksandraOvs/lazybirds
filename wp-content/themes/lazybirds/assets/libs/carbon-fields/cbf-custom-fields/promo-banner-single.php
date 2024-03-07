<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'carbon_fields_register_fields', 'promo_banner_single' ); // Для версии 2.0 и выше
	
	function promo_banner_single() {
	Container::make( 'theme_options' , ' Рекламный баннер ' )
	       ->set_page_parent( 'crb_carbon_fields_container.php' )

			/* ------------
			Большой баннер
			--------------*/

	       ->add_tab( 'Рекламный баннер большой', array(
				Field::make( 'text','prm_b_s__title','Заголовок' )
				   ->help_text('Напишите заголовок для рекламного банера'),
				Field::make( 'textarea','prm_b_s__descrtion','Краткое описание для товара или услуги' ),
				Field::make( 'text','prm_b_s__button','Текст на кнопке' )
				   ->set_classes('my_custom_class')
				   ->set_width(50),
				Field::make( 'text','prm_b_s__buttonlink','Ссылка на рекламируемую страницу' )
				   ->set_width(50),
				Field::make( 'image','prm_b_s__image','Картинка рекламного баннера' )
				   ->help_text('Загрузите сюда вашу картинку для рекламного банера')
				   ->set_width(30),
	       	) )
	       /* -------------
			Средний баннер
	       ---------------*/

          ->add_tab( 'Рекламный баннер средний', array(
   			Field::make( 'text','prm_b_s_middle__title','Заголовок' )
   			   ->help_text('Напишите заголовок для рекламного банера'),
   			Field::make( 'textarea','prm_b_s_middle__descrtion','Краткое описание для товара или услуги' ),
   			Field::make( 'text','prm_b_s_middle__button','Текст на кнопке' )
   			   ->set_classes('my_custom_class')
   			   ->set_width(50),
   			Field::make( 'text','prm_b_s_middle__buttonlink','Ссылка на рекламируемую страницу' )
   			   ->set_width(50),
   			Field::make( 'image','prm_b_s_middle__image','Картинка рекламного баннера' )
   			   ->help_text('Загрузите сюда вашу картинку для рекламного банера')
   			   ->set_width(30),
          	) );

	}

