<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'carbon_fields_register_fields', 'promo_banner_single_middle' ); // Для версии 2.0 и выше
	
	function promo_banner_single_middle() {
	Container::make( 'theme_options' , ' Рекламный баннер (средний) ' )
	       ->set_page_parent( 'crb_carbon_fields_container.php' )
	       ->add_tab( 'Настройка разделов рекламного банера', array(
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
	       	) );

	}

