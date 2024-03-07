<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'carbon_fields_register_fields', 'crb_attach_logo' ); // Для версии 2.0 и выше
// add_action( 'carbon_register_fields', 'crb_attach_theme_options' ); // Для версии 1.6 и ниже
function crb_attach_logo() {
	Container::make( 'post_meta','Настройки записи')

	/*---------
	
	Параметры 
	контейнера 	
	
	----------*/

		->show_on_post_type(array('post','page'))//тип записи post для страницы записей 
		// ->show_on_post_type('page')//тип записи page для страниц  
		// ->show_on_page( 231 )//Данный метод позволяет отобразить произвольные поля записей для конкретных страниц по ID;  
		// ->show_on_page( 'kontaktnaya-forma' )//Данный метод позволяет отобразить произвольные поля записей для конкретных страниц через слак;  
		// ->show_on_page_children( 'kontaktnaya-forma' )//Данный метод позволяет отобразить произвольные поля записей только у дочерних страниц;  
		// ->show_on_post_type(array('page','post')  )//Данный метод позволяет отобразить произвольные поля записей в шаблонах страниц как записей так и страниц;  
		// ->show_on_template( 'sale-page.php' )//Данный метод позволяет отобразить произвольные поля записей в шаблонах страниц поста и принимает один параметр;  
		// ->show_on_template( 'sale-page.php','demo-page.php' )//Если необходимо передать несколько шаблонов то просто передаем их в массиве;  
		// ->hide_on_template( 'sale-page.php' )//Данный метод отображает обратную логику, тоесть при выбранной записи контейнер с произволными полями просто будет скрываться ;  
	    //->show_on_post_format( 'image')//Данный метод ползволяет отображать указанные форматы поста  
	    //->show_on_post_format(array('page','post')  )//Данный метод ползволяет отображать указанные форматы нескольких постов в виде массива  
	    // ->show_on_category( 'nauka-i-zhizn')//Данный метод ползволяет отображать произвольные типы записей в выбранных категориях
	    // ->show_on_category( 'tshirts')//Данный метод ползволяет отображать произвольные типы записей в выбранных категориях
	    // ->show_on_taxonomy_term( 'tehnologii','category')//Данный метод ползволяет отображать произвольные типы записей в выбранных таксономиях и их категориях
       // ->set_page_parent( 'crb_carbon_fields_container.php' )// Данный метод указывает то что блок будет дочерним элементом 
 	/*---------

	Позиционирование 
	контейнера

	----------*/
			// ->set_context( ' normal' ) // Состояние контейнера по умолчанию 
			// ->set_context('advanced') // Выделяет блок отдельно от других частей админ-панели 
			// ->set_context('side') //Должен отображаться в правой части админ панели 
	    	->add_fields( array(
			  Field::make( 'text', 'mood', 'Настроение' ),
			  Field::make( 'text', 'impression', 'Впечателение' )
	) );
}

// add_action( 'carbon_fields_register_fields', 'crb_attach_phone' ); // Для версии 2.0 и выше
// // add_action( 'carbon_register_fields', 'crb_attach_theme_options' ); // Для версии 1.6 и ниже
// function crb_attach_phone() {
// 	Container::make('post_meta', 'Настройки записи')
// 		->add_fields(array(
// 			Field::make('text','my_text_1','Текст 1'),
// 			Field::make('text','my_text_2','Текст 2'),
// 			Field::make('textarea','my_text_3','Текст 3'),
// 	) );

// }






/* Выводим сохоаненные данные  во фронтенд  */
/*<p>Настроение: <?php echo carbon_get_the_post_meta('mood'); ?></p> */
// Данная функия возвращает значения 

/*<p>Настроение: <?php echo carbon_get_post_meta($post->ID, '_mood', true); ?></p>
<p>Эмоции: <?php echo carbon_get_post_meta($post->ID, '_impression', true); ?></p>

