<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;


add_action( 'carbon_fields_register_fields', 'crb_info_option' ); // Для версии 2.0 и выше	
	function crb_info_option() {
	Container::make( 'theme_options' , ' Настройка сайта ' )
	       ->set_icon( 'dashicons-welcome-view-site' )
	       ->add_tab( 'Телефон', array(
		       	Field::make( 'text','crb_phone','Номер телефона' ),
		       	Field::make( 'text','crb_phone_link','Ссылка телефона' ),
		       	Field::make( 'text','crb_mail','Впишите почту' ),
		       	Field::make( 'text','crb_mail_link','Активная ссылка почты' ),
	    ) );

	}

