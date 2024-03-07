<?php
use Carbon_Fields\Widget;
use Carbon_Fields\Field;


add_action( 'carbon_fields_register_fields', 'crb_attach_vidgets_meta' ); // Для версии 2.0 и выше
function crb_attach_vidgets_meta() {
	
	    class AboutMeWidget extends Widget {
		//Создание контейнера (виджета) и полей  
		function construct() {
			$this -> setup(
				'Обо мне',
				'Расскажите о себе добавив фотографию, заголовок, описание или любой контент',
				array(
					Field::make('text','title','Заголовок'),
					Field::make('image','photo','Фотография'),
					Field::make('rich_text','content','Контент')
				),
				'my_css_class_for_widget'
			);
		}	
	}





}

		// //Отображение информации во фронт-энде
		function front_end( $args, $instance ) {

			if( ! empty ( $instance['title'] ) ) {
				echo $args['before_title'] . $instance['title'] . $args['after_title']; 
			}

			if( ! empty ( $instance['photo'] ) ) {
				$photo_url = wp_get_attachment_image_url( $instance['photo'], 'full' );
				echo "<img class='post-thumbnail' src='$photo_url' alt='' />";
			}
			if ( ! empty ( $instance['content'] ) ) {
				echo wpautop( $instance['content'] );
			}
		} 


		// // Сообщаем о нашем виджете движку 
		add_action( 'widgets_init','load_widgets');
			function load_widgets() {
				register_widget('AboutMeWidget');
		}
