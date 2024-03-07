<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'carbon_fields_register_fields', 'crb_attach_theme_settings' ); // Для версии 2.0 и выше
// add_action( 'carbon_register_fields', 'crb_attach_theme_options' ); // Для версии 1.6 и ниже
function crb_attach_theme_settings() {
	Container::make( 'theme_options','Мои настройки')
			// ->set_page_parent('themes.php')// Перестили настройки темы на страницу "Внешний вид" и сделали страницу второго уровня
	    	// ->set_page_permissions( 'edit_themes' )
	    	->set_icon( 'dashicons-welcome-view-site' )
	    	->add_fields( [
			  Field::make( 'text','crb_phone','Номер телефона' ),
			  Field::make( 'text','crb_phone_link','Ссылка телефона' ),
			  Field::make( 'text','crb_mail','Впишите почту' ),
			  Field::make( 'text','crb_mail_link','Активная ссылка почты' ),
			  
	] );

	Container::make( 'theme_options','Рекламный баннер')
			->set_page_parent( 'crb_carbon_fields_container__1.php' )
	    	->add_fields( [
			  Field::make( 'text','crb_promo_banner_title_1','Заголовок для рекламного баннера' ),
			  Field::make( 'textarea','crb_promo_banner_descrpt_1','Краткое описание для товара или услуги' ),
			  Field::make( 'text','crb_promo_banner_btn_1','Название кнопки' ),
			  Field::make( 'text','crb_promo_banner_btn_link_1','ссылка на страницу с рекламируемым товаром ' ),
			  Field::make( 'image','crb_promo_banner_img_1','Загрузите картинку ' )

			  
	] );


}


