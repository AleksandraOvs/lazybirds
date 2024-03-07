<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;


add_action( 'carbon_fields_register_fields', 'crb_attach_term_meta' ); // Для версии 2.0 и выше
function crb_attach_term_meta() {


  



  /*
  * # Слафдер на страницах с проектами 
  */

  Container::make( 'post_meta','gallery')->set_context( 'normal' )
     ->add_fields( [            
       Field::make( 'complex', 'cbf_complex_carouselprohects', 'Галлерея на страницах проекта' )
           ->help_text( ' Загрузите картинки которые будут отображаться в галлерее на страницах с проектом ' )
           ->add_fields( array(
                 Field::make( 'image', 'cbf_carouselprohect_img','Картинка' )->set_width( 50 ),                 
             ) ),
      ] );



}


  /* 
  * Добавить логотипы к брендам 
  * 
  */

  // Container::make( 'term_meta', __( 'add_logo_brand' ) )
  //     ->where( 'term_taxonomy', '=', 'brand' )
  //     ->add_fields( array(
  //         Field::make( 'image', 'crb_logo_brand','Логотип для бренда' )->set_width( 50 ),  
  //         // Field::make( 'color', 'crb_title_color', __( 'Title Color' ) ),
  //         // Field::make( 'image', 'crb_thumb', __( 'Thumbnail' ) ),
  // ) );


  /* 
  * Выбрать адрес, метро клининики 
  * 
  */

/*    Container::make( 'post_meta','metro_clinic')->set_context( 'normal' )
      ->show_on_category( 'address')
      ->add_fields( [            
   
        Field::make( 'complex', 'crb_complex_clinics', 'Адреса клиник' )
            ->help_text( ' Укажите адрес где принимает врач ' )
            ->add_fields( array(                 
                  // Field::make( 'select', 'crb_clinic_name', 'Название клиники' )->set_width( 50 )
                  //     ->add_options( array(
                  //       '  ' => '  ',                                                               
                  //       'Dr. Эстетик Каширское' => 'Dr. Эстетик Каширское ',                                                               
                  //       'Dr. Эстетик Подсосенский' => 'Dr. Эстетик Подсосенский ',                                                               
                  // ) ),
                  Field::make( 'select', 'crb_metro_name' , 'название метро' )->set_width( 100 )
                      ->add_options( array(                                                 
                        'Домодедовская' => 'Домодедовская ',                                                               
                        'Курская / Чкаловская' => 'Курская / Чкаловская ',                                                               
                  ) ),           
              ) ),
   ] );
*/


    /* 
    * Слайдер маршрут 
    * 
    */

     //  Container::make( 'post_meta','route clinic')->set_context( 'normal' )
     //    // ->show_on_category( 'address')
     //    ->add_fields( [            
     //      Field::make( 'complex', 'crb_complex_route', 'Фото клиники' )
     //          ->help_text( ' Вставьте картинки ' )
     //          ->add_fields( array(                             
     //              Field::make( 'image', 'crb_route_thumb' , 'картинка миниатюра ' )->set_width( 10 ),                
     //            ) ),
     // ] );


      /* 
      * Адрес клиники 
      * 
      */

 /*       Container::make( 'post_meta','address clinic')->set_context( 'normal' )
          ->show_on_category( 'address')
          ->add_fields( [            
                Field::make( 'rich_text', 'crb_address_city' , 'Адрес ' )
                  ->help_text( 'Укажите адрес клиники ' )
                  ->set_width( 100 ),                 
                // Field::make( 'rich_text', 'crb_work_time' , 'Время работы ' )->set_width( 10 ),                 
                // Field::make( 'rich_text', 'crb_clinic_tel' , 'Телефон ' )->set_width( 10 ),                 
                // Field::make( 'rich_text', 'crb_clinic_email' , 'Электронная почта ' )->set_width( 10 ),                 
     
     ] );*/





  /* 
  * Настройки параметров 
  * Цены  
  */



   /* Container::make( 'post_meta','price')->set_context( 'normal' )
      ->show_on_category( 'tseny')
      ->add_fields( [            
        Field::make( 'complex', 'crb_complex_procedures', 'Процедуры' )
          ->help_text( ' Создайте список процедур которые относятся к данной услуге ' )
          ->add_fields( array(                             
              Field::make( 'text', 'crb_complex_procedures_article' , 'Артикул' )->set_width( 10 ),
              Field::make( 'text', 'crb_complex_procedures_title' , 'Название процедуры' )->set_width( 30 ),
              Field::make( 'text', 'crb_complex_procedures_time' , 'Время процедуры' )->set_width( 20 ),
              Field::make( 'text', 'crb_complex_procedures_old_price' , 'Стоимость ' )->set_width( 20 ),
              Field::make( 'text', 'crb_complex_procedures_new_price' , 'Со скидкой ' )->set_width( 20 ),              
            


              Field::make( 'select', 'crb_logic_address_clinic', 'Адреса клиник' )->help_text( ' Показать или скрыть адрес.  ' )->set_width( 50 )
                  ->add_options( array(
                      'no' => 'Скрыть',
                      'yes' => 'Показать ',
                  ) ),

              Field::make( 'select', 'crb_complex_procedures_clinic' , 'Клиника в которой данная процедура проводится' )->set_width( 50 )
                ->add_options( array(
                    'Dr. Эстетик Каширское Домодедовская ' => 'Dr. Эстетик Каширское ',                                                               
                    'Dr. Эстетик Подсосенский Курская / Чкаловская' => 'Dr. Эстетик Подсосенский '                                                                                                              
                  ) )
                ->set_conditional_logic( array(
                      'relation' => 'AND', // Optional, defaults to "AND"
                      array(
                          'field' => 'crb_logic_address_clinic',
                          'value' => 'yes', // Optional, defaults to "". Should be an array if "IN" or "NOT IN" operators are used.
                          'compare' => '=', // Optional, defaults to "=". Available operators: =, <, >, <=, >=, IN, NOT IN
                      )
                  ) ),

            ) ),
	] );
*/

  /* 
  * Настройки параметров 
  * Магазин 
  */
 /* Container::make( 'post_meta','Вкладки в магазине')->set_context( 'normal' )
    ->show_on_post_type( 'product')
    ->add_fields( [       
      Field::make( 'rich_text', 'crb_tab_indications_for_use',' Показания к применению' )->set_width( 100 ),           
      Field::make( 'rich_text', 'crb_tab_application_methods',' Способы применения' )->set_width( 100 ),           
      Field::make( 'rich_text', 'crb_tab_composition',' Состав' )->set_width( 100 ),           	      

		Field::make( 'complex', 'crb_all_specifications', 'Характеристики' )
  		->help_text('Укажите необходимые характеристики товара')
  		->add_fields( array(                                                    
  		    Field::make( 'text', 'crb_specifications_volume' , 'Объём' )->set_width( 45 ),
  		    Field::make( 'text', 'crb_specifications_country' , 'Страна бренда' )->set_width( 45 ),                  
  		 ) ),


		Field::make( 'select', 'crb_logic_consultation', 'Необходима ли консультация врача ' )->help_text( ' Показать или скрыть уведомление.  ' )->set_width( 100 )
		->add_options( array(
		  'yes' => 'Да ',
		  'no' => 'Нет',
		) ),

		Field::make( 'text', 'crb_notice_consultation',' Описание ' )
			->set_default_value('Данный товар возможно приобрести только после бесплатной консультации нашего специалиста')
			->help_text('Укажите текст уведомления который будет доступен для данного товара ')        
			->set_conditional_logic( array(
			  'relation' => 'AND', // Optional, defaults to "AND"
			  array(
			      'field' => 'crb_logic_consultation',
			      'value' => 'yes', // Optional, defaults to "". Should be an array if "IN" or "NOT IN" operators are used.
			      'compare' => '=', // Optional, defaults to "=". Available operators: =, <, >, <=, >=, IN, NOT IN
			  )
		) ),


   ] );*/


  /* 
  * Настройки параметров 
  * цен
  */

 /* Container::make( 'post_meta','price_id')->set_context( 'normal' )
    // ->where( 'post_term', '=', array(
    //       'field' => 'slug',
    //       'value' => 'uslugi',
    //       'taxonomy' => 'category',
    //   ) )
    // ->where( 'post_term', '=', array(
    //       'field' => 'slug',
    //       'value' => 'sale',
    //       'taxonomy' => 'category',
    //   ) )
    
    // ->where('category',' = ', '')
    // $employees_labels = array('uslugi','sale');
    // ->show_on_category(array() )
    ->add_fields( [       
      Field::make( 'text', 'crb_price_id',' Артикул ' )->set_width( 25 ), 
      Field::make( 'text', 'crb_price_id2',' Артикул ' )->set_width( 25 ), 
      Field::make( 'text', 'crb_price_id3',' Артикул ' )->set_width( 25 ), 
      Field::make( 'text', 'crb_price_id4',' Артикул ' )->set_width( 25 ), 
      Field::make( 'html', 'crb_price_information_text' )   
      ->help_text('Если необходимо подгрузить цены из нужной категрию , просто укажите артикул в данной категории. Прайс будет подгружен в конец поста <a style="color:red;" target="_blank" href="https://dr-estetic.ru/wp-content/uploads/wp-admin-notice/notice-1.png">Пример </a> -> <a style="color:green;" target="_blank" href="https://dr-estetic.ru/uslugi/picocare-unikalnyj-pikosekundnyj-lazer-poslednego-pokoleniya/">Результат </a> '),

   ] );
*/
/** 
 * # Сладйер 
 * ## Заголовки  
 * ## Текст 
 **/
 /*   Container::make( 'post_meta',' slider main ')->set_context( 'normal' )
      ->show_on_category( 'sale')
    // ->set_context('advanced')
    // ->where( 'post_term', '=', array(
    //       'field' => 'slug',
    //       'value' => 'uslugi',
    //       'taxonomy' => 'category',
    //   ) )
    // ->where( 'post_term', '=', array(
    //       'field' => 'slug',
    //       'value' => 'sale',
    //       'taxonomy' => 'category',
    //   ) )
      // ->where( 'taxonomy', '=', 'slider-intro' )
      // ->where( 'type', '=', 'term' )
      // ->or_where( function( $condition ) {
      //        $condition->where( 'type', '=', 'term' );
      //        $condition->where( 'taxonomy', '=', 'slider-intro' );
      //    } )

      ->add_fields( [
	        Field::make( 'image', 'crb_meta_slider_lage_img','Картинка большая' )->set_width( 50 ),
	        Field::make( 'image', 'crb_meta_slider_small_img','Картинка маленькая' )->set_width( 50 ),
    ] );
*/


    /* 
    * Настройки параметров 
    * к записям на странице технологии  
    */
/*      Container::make( 'post_meta','technology')->set_context( 'normal' )
        ->show_on_category( 'tehnologii')
        ->add_fields( [

          Field::make( 'complex', 'crb_technology', 'Информация' )
              ->help_text( ' Укажите информацию по технологиям' )
              ->add_fields( array(       
                    Field::make( 'image', 'crb_technology_img','Картинка для блока ' )->set_width( 20 ),
                    Field::make( 'rich_text', 'crb_technology_text','Далее по тексту' )->set_width( 80 ),
                    Field::make( 'rich_text', 'crb_technology_certificate','Сертификаты аппарата ' )->set_width( 100 ),
              ) ),
    ] );*/

  /* 
  * Настройки параметров 
  * к записям доктор 
  */
  /*  Container::make( 'post_meta','Doctors')->set_context( 'normal' )
      ->show_on_category( 'doctor')
      ->add_fields( [
        /*Соц сети доктора*/ 
        // Field::make( 'complex', 'crb_social', 'Соц сети' )
        //         ->help_text( ' Укажите социальные сети врача ' )
        //         ->add_fields( array(                             
                  
	       //            Field::make( 'select', 'social_icon', 'Социальные сети гида' )->set_width( 45 )
	       //                ->add_options( array(
	       //                  '' => '',
	       //                  '<i class="fab fa-facebook-square"></i>' => 'facebook',
	       //                  '<i class="fab fa-vk"></i>' => 'vk',
	       //                  '<i class="fab fa-linkedin"></i>' => 'linkedin',
	       //                  '<i class="fab fa-odnoklassniki-square"></i>' => 'odnoklassniki',
	       //                  '<i class="fab fa-instagram"></i>' => 'instagram'                                    
	       //                ) ),           
	       //            Field::make( 'text', 'social_link','ссылка на аккаунт' )->set_width( 45 )
        //          ) ),
        // Field::make( 'html', 'crb_doctors_information_text' )
        // 	->help_text('<b>Структура подзаголовков</b> </br> О враче </br> Сертификаты </br>Примеры работ '),
         /* Таб загловки */         
        // Field::make( 'complex', 'crb_tub_title_doctor', 'Подзаголовки' )
        //         ->help_text( 'Укажите информацию по необходимым критериям' )
        //         ->add_fields( array(       
        //               Field::make( 'text', 'crb_tub_item_doctor','Укажите название' )->set_width( 100 ),
        //         ) ),
        /* Таб контент */         
        // Field::make( 'complex', 'crb_tub_content_doctor', 'Описание в подзаголовках ' )
        //         ->help_text( 'Укажите информацию по необходимым критериям' )
        //         ->add_fields( array(       
        //               Field::make( 'rich_text', 'crb_tub_content_item_shortcode_doctor','Укажите описание ' )
        //         ) ),


        // Field::make( 'complex', 'crb_doctors', 'doctor_info' )
        //         ->help_text( ' Укажите информацию по доктору' )
        //         ->add_fields( array(       
        //               Field::make( 'text', 'crb_price_article','Артикул' ) ->set_default_value('00')->set_width( 100 ),
        //               Field::make( 'text', 'crb_price_title','Название услуги' )->set_width( 100 ),
        //               Field::make( 'text', 'crb_price_total','Итого цена ' )->set_width( 100 ),

        //              // старая цена  
        //               Field::make( 'select', 'crb_price_old', '__БЛОК__ старая цена' )->help_text( ' Показать или скрыть блок.  ' )->set_width( 30 )
        //                   ->add_options( array(
        //                       'no' => 'Скрыть',
        //                       'yes' => 'Показать ',
        //                   ) ),
                      
        //               Field::make( 'text', 'crb_price_old_option','Выберите опцию для данного блока ' )->set_width( 100 )
        //                   ->help_text( ' Если необходимо указать дополнительную цену, то напишите её сюда ' )
        //                   ->set_conditional_logic( array(
        //                       'relation' => 'AND', // Optional, defaults to "AND"
        //                       array(
        //                           'field' => 'crb_price_old',
        //                           'value' => 'yes', // Optional, defaults to "". Should be an array if "IN" or "NOT IN" operators are used.
        //                           'compare' => '=', // Optional, defaults to "=". Available operators: =, <, >, <=, >=, IN, NOT IN
        //                       )
        //                   ) ),              
        //         ) ),

                
//         ] );

// }


// */

  /* 
  * Настройки параметров 
  * к записям на странице отзывы  
  */
//     Container::make( 'post_meta','Настройки параметров записи на странице с отзывами ')->set_context( 'normal' )
//       ->show_on_category( 'reviews')
//       ->add_fields( [

//         Field::make( 'select', 'crb_tag_reviews_options', 'Теги врачи' )->set_width( 33 )->help_text( ' Выберите врача, которому оставляется отзыв  ' )
//             ->add_options( array(
//               '' => '',
//               'charikov' => 'Чариков Вадим Андреевич',
//               'kryuchkova' => 'Крючкова Светлана Николаевна'
                     
//             ) ),

//         // Field::make( 'complex', 'crb_technology', 'Информация' )
//         //         ->help_text( ' Укажите информацию по технологиям' )
//         //         ->add_fields( array(       
//         //               Field::make( 'image', 'crb_technology_img','Картинка для блока ' )->set_width( 20 ),
//         //               Field::make( 'rich_text', 'crb_technology_text','Далее по тексту' )->set_width( 80 ),
//         //               Field::make( 'rich_text', 'crb_technology_certificate','Сертификаты аппарата ' )->set_width( 100 ),
//         //         ) ),


// ] );



/* 
* Настройки параметров 
* цен
*/
  // Container::make( 'post_meta','Настройки параметров записи цен')->set_context( 'normal' )
  //   // ->show_on_category( 'price-post')
  //   ->add_fields( [
      
  //     Field::make( 'text', 'crb_price_link_post','Ссылка на стараницу с данными услугами' )->set_width( 100 ),

  //    // Прайс лист 

  //      Field::make( 'complex', 'crb_price', 'Контактная информация по услугам' )
  //                     ->help_text( ' Заполните информацию ' )
  //                     ->add_fields( array(                             

  //                              Field::make( 'text', 'crb_price_heading','Категории' )->help_text( ' Укажите название категории ' )->set_width( 100 ),

  //                              Field::make( 'complex', 'crb_price_item', 'Подробная информация по процедурам' )
  //                               ->help_text( ' Укажите информацию по услугам  что и сколько стоит' )
  //                               ->add_fields( array(       
  //                                     Field::make( 'text', 'crb_price_article','Артикул' ) ->set_default_value('')->set_width( 100 ),
  //                                     Field::make( 'text', 'crb_price_title','Название услуги' )->set_width( 100 ),
  //                                     Field::make( 'text', 'crb_price_total','Итого цена ' )->set_width( 100 ),

  //                                    // старая цена  
  //                                     Field::make( 'select', 'crb_price_old', '__БЛОК__ старая цена' )->help_text( ' Показать или скрыть блок.  ' )->set_width( 30 )
  //                                         ->add_options( array(
  //                                             'no' => 'Скрыть',
  //                                             'yes' => 'Показать ',
  //                                         ) ),
                                      
  //                                     Field::make( 'text', 'crb_price_old_option','Выберите опцию для данного блока ' )->set_width( 100 )
  //                                         ->help_text( ' Если необходимо указать дополнительную цену, то напишите её сюда ' )
  //                                         ->set_conditional_logic( array(
  //                                             'relation' => 'AND', // Optional, defaults to "AND"
  //                                             array(
  //                                                 'field' => 'crb_price_old',
  //                                                 'value' => 'yes', // Optional, defaults to "". Should be an array if "IN" or "NOT IN" operators are used.
  //                                                 'compare' => '=', // Optional, defaults to "=". Available operators: =, <, >, <=, >=, IN, NOT IN
  //                                           )
  //                                     ) ),            
  //                               ) ),

  //       ) ),
  //  ] );

  /* 
  * Настройки параметров 
  * на странице с услугами  
  */
    // Container::make( 'post_meta','Настройки параметров записи на странице с услугами')->set_context( 'normal' )
      // ->show_on_category( 'uslugi')
      // ->add_fields( [

        /* Текст для описания на странице с данной услугой */
        // Field::make( 'image', 'crb_service_page_img','Картинка для блока на странице с данной услугами' )->set_width( 30 ),
        // Field::make( 'textarea', 'crb_service_page_content',' Текст на странице с услугой ' )->set_width( 70 ),
        /* Выводим нужный пост с ценами для данной услуги */
        // Field::make( 'image', 'crb_service_top_img','Картинка в верху страницы' )->set_width( 100 ),
        // Field::make( 'rich_text', 'crb_service_top_content','Контент в верху страницы' )->set_width( 100 ),


        // Field::make( 'text', 'crb_service_price_post','Выводим прайс для данной услуги по ID поста с ценами' )
        // ->set_width( 100 ),


        // Field::make( 'complex', 'crb_tub_title', 'Таб заголовки на сайте' )
        //         ->help_text( 'Укажите информацию по необходимым критериям' )
        //         ->add_fields( array(       
        //               Field::make( 'text', 'crb_tub_title_item','таб-заголовок  ' )->set_width( 100 ),
        //         ) ),

        // Field::make( 'complex', 'crb_tub_content', 'Таб контент на сайте' )
        //         ->help_text( 'Укажите информацию по необходимым критериям' )
        //         ->add_fields( array(       
        //               Field::make( 'rich_text', 'crb_tub_content_item','таб-контент  ' )->set_width( 100 ),
        //               Field::make( 'text', 'crb_tub_content_item_shortcode','таб-контент шорткод  ' )
        //                           ->set_width( 30 )
        //                           ->help_text( ' Укажите шорткод [show_elements_price]' ),
        //         ) ),



      /* Укажите какой аппарат необходим для оказания услуги если такой нужен на старнице */
        // Field::make( 'complex', 'crb_service_technology', 'Как необходим аппарат для оказания услуги' )
        //         ->help_text( ' Укажите какой аппарат необходим для оказания услуги если такой нужен на старнице' )
        //         ->add_fields( array(       
        //               Field::make( 'image', 'crb_service_technology_img','Картинка для блока ' )->set_width( 20 ),
        //               Field::make( 'rich_text', 'crb_service_technology_text','Далее по тексту' )->set_width( 80 ),


        //         ) ),


// ] );



      // Field::make( 'rich_text', 'crb_price_doptex_2','Доп текст' )->set_width( 100 ),



                      // Field::make( 'complex', 'crb_price_option', 'Описание' )
                      //        ->help_text( ' Укажите подробное описание что где и как' )
                      //        ->add_fields( array(                             

                      //            Field::make( 'text', 'crb_price_article','артикул' )->set_width( 33 ),
                      //            Field::make( 'text', 'crb_price_descr','Описание' )->set_width( 33 ),
                      //            Field::make( 'text', 'crb_price_total','Итого цена ' )->set_width( 33 ),

                      //      ) ),                            


                      // Field::make( 'select', 'crb_tour_options', 'Инфа' )->set_width( 33 )->help_text( ' Выберите условие для данного прелдожения  ' )
                      //     ->add_options( array(
                      //       '' => '',
                      //       '<i class="fas fa-map-marked-alt"></i>' => 'регион',
                      //       '<i class="fas fa-calendar-alt"></i>' => 'На сколько дней ',
                      //       '<i class="fas fa-spinner"></i>' => 'Расстояние ',
                      //       '<i class="fas fa-coins"></i>' => 'Стоимость тура  ',
                      //       '<i class="fas fa-tram"></i>' => 'Сложность тура  ',
                      //       '<i class="fas fa-user-clock"></i>' => 'Сколько осталось мест  ',
                      //       '<i class="fas fa-user-check"></i>' => 'Статус тура  ',                           
                      //     ) ),
                          // Field::make( 'select', 'crb_tour_options_notice', 'Инф окно' )->set_width( 33 )->help_text( ' Выберите условие для данного прелдожения  ' )
                          //     ->add_options( array(
                          //       '' => '',
                          //       'timetable-region' => 'регион',
                          //       'timetable-distance' => 'Растояние ',
                          //       'timetable-time' => 'На сколько дней  ',
                          //       'timetable-price' => 'Стоимость тура  ',
                          //       'timetable-hard' => 'Сложность тура  ',
                          //       'timetable-place' => 'Сколько осталось мест  ',
                          //       'timetable-status' => 'Статус тура  ',                           
                          //     ) ),


                        // Field::make( 'select', 'crb_tour_color_shema', 'Цвет схема' )->set_width( 33 )->help_text( ' Выберите условие для данного прелдожения  ' )
                        //     ->add_options( array(
                        //         '' => '',
                        //       'timetable-items__content_place' => 'Сколько осталось мест  ',
                        //       'timetable-items__content_status-ok' => 'Статус тура идет набор ',                           
                        //       'timetable-items__content_status-end' => 'Статус тура набор закончен ',                           
                        //     ) ),

// //Иконки передвижения 
//         Field::make( 'complex', 'crb_movement', 'Иконки передвижения' )
//                 ->help_text( ' Вставьте иконки передвижения по туру' )
//                 ->add_fields( array(                               
//                       Field::make( 'image', 'crb_movement_icons','Укажите картинку' )->set_width( 45 ),
//                       Field::make( 'text', 'crb_movement_icons_titile','Заголовок для картинки' )->set_width( 45 )
//               ) ),
// //Иконки данные на странице с туром 
//         Field::make( 'complex', 'crb_tour_page_info', 'Иконки на странице с туром' )
//                 ->help_text( ' Вставьте иконки согласно описанию по туру' )
//                 ->add_fields( array(                             

//                   Field::make( 'select', 'crb_tourpg_icon', 'Картинка' )->set_width( 33 )->help_text( ' Выберите условие для данного прелдожения  ' )
//                       ->add_options( array(
//                         '' => '',
//                         'tour-hardway' => 'Сложность',
//                         'tour-distance' => 'Расстояние ',
//                         'tour-time' => 'На сколько',
//                         'tour-world' => 'Заграничный поход'                     
//                       ) ),
//                       Field::make( 'text', 'crb_tourpg_title','Заголовок' )->set_width( 45 ),
//                       Field::make( 'text', 'crb_tourpg_descr','Описание' )->set_width( 45 ),
//               ) ),
// //Статус тура 
//         Field::make( 'select', 'crb_tourpg_status', 'Статус' )->set_width( 33 )->help_text( ' Укажите идет набор в группу или уже закончился  ' )
//             ->add_options( array(

//               '' => '',
//               '<i class="fas fa-user-plus"></i>' => 'идет набор',
//               '<i class="fas fa-user-check"></i>' => 'группа набрана ',
                 
//             ) ),
//         Field::make( 'select', 'crb_tourpg_status_class', 'Класс для элемента' )->set_width( 33 )->help_text( ' Укажите идет набор в группу или уже закончился  ' )
//             ->add_options( array(

//               '' => '',
//               'status-yes' => 'идет набор',
//               'status-no' => 'группа набрана ',
                 
//             ) ),
//         Field::make( 'text', 'crb_tourpg_status_title','Заголовок' )->set_width( 45 ),
// //Инструктора на странице с туром 
//         Field::make( 'complex', 'crb_guide_page_info', 'Гиды на странице с  туром' )
//                 ->help_text( ' Заполните информацию по гиду' )
//                 ->add_fields( array(                             
                  

//                   Field::make( 'text', 'crb_guidepg_date_work','Время работы гида' )->set_width( 45 )->help_text( ' Укажите с какое по какое число гид может работать с группой напримнер c 12 мая — 21 июня ' ),
//                   Field::make( 'image', 'crb_guidepg_photo','Фотография гида' )->set_width( 45 ),
//                   Field::make( 'text', 'crb_guidepg_name','Как зовут гида ' )->set_width( 45 )->help_text( ' Например: Матвийко Максим ' ),

//                   Field::make( 'complex', 'crb_guide_social_icon', 'Соц сети гида' )
//                           ->help_text( ' Укажите социальные сети ребят ' )
//                           ->add_fields( array(                             
                            
//                             Field::make( 'select', 'guide_social', 'Социальные сети гида' )->set_width( 45 )
//                                 ->add_options( array(
//                                   '' => '',
//                                   '<i class="fab fa-facebook-square"></i>' => 'facebook',
//                                   '<i class="fab fa-vk"></i>' => 'vk',
//                                   '<i class="fab fa-linkedin"></i>' => 'linkedin',
//                                   '<i class="fab fa-odnoklassniki-square"></i>' => 'odnoklassniki'
                                               
//                                 ) ),
                          
//                                 Field::make( 'text', 'guide_social_link','ссылка на аккаунт' )->set_width( 45 ),
//               ) ),

// //Инструктора заслуги 
//         Field::make( 'text', 'crb_guide_skills_title','Заголовок' )->help_text( ' Тут можно указать профильные направления гида ' ),
                      
//         Field::make( 'complex', 'crb_guide_portfolio', 'Заслуги' )
//                 ->help_text( ' Укажите подробное описание что где и как  ' )
//                 ->add_fields( array(                             
                  
//                   Field::make( 'select', 'crb_guide_portfolio_option', 'Выберите направление' )->set_width( 45 )
//                       ->add_options( array(
//                          '' => '',
//                         '<i class="fas fa-map-marked-alt"></i>' => 'Полярные маршруты:',
//                         '<i class="fab fa-envira"></i>' => 'Виды тура:',
//                         '<i class="fas fa-award"></i>' => 'Общий опыт:'
                                    
//                       ) ),

//                 Field::make( 'select', 'crb_guide_portfolio_title', 'Заголовки' )->set_width( 45 )
//                     ->add_options( array(
//                       '' => '',
//                       'Полярные маршруты' => 'Полярные маршруты:',
//                       'Виды тура' => 'Виды тура:',
//                       'Общий опыт' => 'Общий опыт:'
                                  
//                     ) ),
                
//                     Field::make( 'rich_text', 'crb_guide_portfolio_descr','Описание' )->set_width( 45 ),

//               ) ),
//       ) ),

// //Таб-вкладка список вещей  
//         Field::make( 'rich_text', 'crb_list_things','Список необхоимых вещей ' ),
// //Таб-вкладка стоимость тура  
//         Field::make( 'rich_text', 'crb_price_tour','Стоимость тура' ),
// //Таб-вкладка рекоммендации
//         Field::make( 'rich_text', 'crb_recommendations','Рекомендации' ),

// // Блок как добраться до места назначения 
//         Field::make( 'complex', 'crb_howtoget_tuda', 'Как добраться туда' )
//                 ->help_text( ' Укажите подробное описание что где и как  ' )
//                 ->add_fields( array(                             
                  
//                       Field::make( 'select', 'crb_howtoget_tuda_option_title', 'Выберите направление' )->set_width( 33 )
//                       ->add_options( array(
//                         '' => '',
//                         'Туда' => 'Туда',
//                         'Обратно' => 'Обратно'                                                
//                       ) ),
//                   Field::make( 'select', 'crb_howtoget_tuda_transport_icon', 'Укажите транспорт' )->set_width( 33 )
//                     ->add_options( array(
//                       '' => '',
//                       '<i class="fas fa-train"></i>' => 'Поездом',
//                       '<i class="fas fa-plane"></i>' => 'Самолетом'
//                     ) ),
//                   Field::make( 'select', 'crb_howtoget_tuda_transport_name', 'Укажите название транспорта' )->set_width( 33 )
//                     ->add_options( array(
 
//                       '' => '',
//                       'Поездом' => 'Поездом',
//                       'Самолетом' => 'Самолетом'                                              
//                     ) ),
//                   Field::make( 'complex', 'crb_howtoget_tuda_description', 'Описание' )
//                         ->help_text( ' Укажите подробное описание что где и как  ' )
//                         ->add_fields( array(                             
                          
//                             Field::make( 'text', 'crb_howtoget_tuda_description_item','Можете указать подробное описание маршрута если таковое есть' )->set_width( 45 ),

//                       ) ),                            

//       ) ),


