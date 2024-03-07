<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'carbon_fields_register_fields', 'crb_radio_check' ); // Для версии 2.0 и выше
	
	function crb_radio_check() {
	
	Container::make( 'theme_options' , ' Рекламный баннер ' )
	       ->add_fields( [
	       		Field::make( 'radio','love-eat-night','Любите покушать ночью?' )
	       		   ->add_options(array(
	       		   		'yes' => 'Да',
	       		   		'no' => 'Нет',
	       		    ) ),
	       		Field::make('text','night-meals','Какие блюда? ')
	       			->set_conditional_logic( array(
	       				'relation' =>' AND',
	       				array (
	       					'field' 	=> ' love-eat-night ',	
	       					'value' 	=> ' yes ',	
	       					'compare' 	=> ' = ',	
	       				)	
	       			) ),   
       			Field::make('text','night-meals','Почему? ')
       				->set_conditional_logic( array(
       					'relation' =>' AND',
       					array (
       						'field' 	=> ' love-eat-night ',	
       						'value' 	=> ' no ',	
       						'compare' 	=> ' = ',	
       					)	
       				) ),  
       		]);


	}

