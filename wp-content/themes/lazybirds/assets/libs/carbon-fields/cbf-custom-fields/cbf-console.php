<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;





add_action( 'carbon_fields_register_fields', 'crb_attach_theme_options' ); // Для версии 2.0 и выше

function crb_attach_theme_options() {

    $basic_options_container = Container::make( 'theme_options', 'pult' )

/*
* # Я. Метрика 
*/
    ->add_tab( 'yandex metrika', array(
                Field::make( 'header_scripts', 'crb_header_script_metrika', 'Установите скрипт Яндекс Метрики на сайт')
    ) )

/*
* WEBICA 
*/
	->add_tab( 'webica', array(
	            Field::make( 'header_scripts', 'crb_header_script_webica', 'Установите скрипт WEBICA на сайт')
	) )

/*
* # Pixel Facebook
*/
    ->add_tab( 'facebook pixel ', array(          
                Field::make( 'header_scripts', 'crb_header_script_pixel', 'Установите скрипт Facebook Pixel на сайт ')
    ) );


    Container::make( 'theme_options' , ' slider ' )
    ->set_page_parent($basic_options_container) // Gallery is custom post type 
    ->set_icon( 'dashicons-carrot' )
    ->add_tab( 'Слайдер клиники ', array(
         Field::make( 'complex', 'complex_slider_clinic', 'Фотки клиники' )
         ->help_text( ' Вставьте картинки ' )
         ->add_fields( array(                             
             Field::make( 'image', 'complex_slider_clinic_img' , 'Картинка миниатюра ' )->set_width( 10 ),                
           ) ),
    ) )
    ->add_tab( 'Слайдер процедуры ', array(
         Field::make( 'complex', 'complex_slider_procedures', 'Фотки процедуры' )
         ->help_text( ' Вставьте картинки ' )
         ->add_fields( array(                             
             Field::make( 'image', 'complex_slider_procedures_img' , 'Картинка миниатюра ' )->set_width( 10 ),                
           ) ),
    ) )

    ->add_tab( 'Слайдер APTOS ', array(
         Field::make( 'complex', 'complex_slider_aptos', 'Фотки процедуры' )
         ->help_text( ' Вставьте картинки ' )
         ->add_fields( array(                             
             Field::make( 'image', 'complex_slider_aptos_img' , 'Картинка миниатюра ' )->set_width( 10 ),                
           ) ),
    ) );

    // Container::make( 'theme_options' , ' Слайдер клиники ' )
    // ->set_page_parent($basic_options_container) // Gallery is custom post type 
    
    // ->add_fields(array(
    //     Field::make( 'complex', 'complex_slider_clinic', 'Фотки клиники' )
    //         ->help_text( ' Вставьте картинки ' )
    //         ->add_fields( array(                             
    //             Field::make( 'image', 'crb_route_thumb' , 'Картинка миниатюра ' )->set_width( 10 ),                
    //           ) ),
    // ) );



}






// $basic_options_container = Container::make( 'theme_options', 'Пульт' )
        // ->add_tab( 'Слайдер большой', array(
        //       Field::make( 'complex', 'crb_slider', 'Список слайдов' )
        //               ->help_text('Используйте данный шорт-код для вставки на сайт в любом месте через админ панель : <b style="color:#D4363B;">[shcode_slider_main]</b>')
        //               ->add_fields( array(
        //                      Field::make( 'image', 'sld_main_picture', 'Картинка слайда' )->set_value_type( 'url' )->set_width( 100 )->help_text( 'Загрузите картинку для слайдера' ),
        //                      Field::make( 'text', 'sld_main_title', 'Заголовок ' )->set_width( 50 ),
        //                      Field::make( 'rich_text', 'sld_main_descr', 'Описание товара, услуги' )->set_width( 50 ),
        //                      Field::make( 'text', 'sld_main_btn', 'Название кнопки' )->set_width( 50 ),
        //                      Field::make( 'text', 'sld_main_btn_url', 'Ссылка ' )->set_width( 50 )->help_text( 'Укажите ссылку на страницу с рекламируемым товаром  ' ),
        //             ) )
        // ) )
//         ->add_tab( 'Слайдер маленький', array(
//               Field::make( 'complex', 'crb_slider_s', 'Список слайдов' )
//                       ->help_text('Используйте данный шорт-код для вставки на сайт в любом месте : <b style="color:#D4363B;">[shcode_slider_small]</b>')
//                        ->add_fields( array(
//                              Field::make( 'image', 'sld_s_pic', 'Картинка слайда' )->set_value_type( 'url' )->set_width( 100 )->help_text( 'Загрузите картинку для слайдера' ),
//                              Field::make( 'text', 'sld_s_url', 'Ссылка ' )->set_width( 50 )->help_text( 'Укажите ссылку на страницу категории или товара' ),
//                     ) )
//         ) )
//         ->add_tab( 'Таб слайдер', array(
//               Field::make( 'complex', 'crb_slider_tab', 'Список слайдов' )
//                       ->help_text('Используйте данный шорт-код для вставки на сайт в любом месте : <b style="color:#D4363B;">[shcode_slider_tab]</b>')
//                        ->add_fields( array(
//                            Field::make( 'text', 'crb_tab_title_link', 'Заголовок для блока таб' )->set_width( 50 )->help_text( 'Напишите заголовок' ),
//                            Field::make( 'image', 'crb_tab_title_link-img', 'Картинка для таб блока' )->set_value_type( 'url' )->set_width( 50 )->help_text( 'Загрузите картинку заголовка (опционально)' ),
//                            Field::make( 'rich_text', 'crb_shortcode_item', 'шорт-код товара' )->set_width( 30 )->help_text( 'Установите шорткод товара' ),
//                            Field::make( 'textarea', 'crb_slider_tab_text', ' Описание для категории таба ' )->set_width( 50 )->help_text( 'Описание для выбранного товара или услуги' ),
//                     ) )
//         ) )
//         ->add_tab( 'Социальные сети', array(
//               Field::make( 'complex', 'crb_social', 'Cоциальные сети' )
//                       ->help_text('Используйте данный шорт-код для вставки на сайт в любом месте : <b style="color:#D4363B;">[shcode_social_link]</b>')
//                        ->add_fields( array(
//                            Field::make( 'text', 'crb_social_title', 'Заголовок' )->set_width( 33 )->help_text( 'Укажите заголовок соц. сети ' ),
//                            Field::make( 'text', 'crb_social_icon', 'Иконка соц сети' )->set_width( 33 )->help_text( 'Укажите иконку для соц сети ' ),
//                            Field::make( 'text', 'crb_social_link', 'Ссылка соц сети' )->set_width( 33 )->help_text( 'Укажите ссылку для соц сети ' ),
//                     ) )
//         ) )
//         ->add_tab( 'Мессенджеры', array(
//               Field::make( 'complex', 'crb_messengers', 'Мессенджеры' )
//                       ->help_text('Используйте данный шорт-код для вставки на сайт в любом месте : <b style="color:#D4363B;">[shcode_messengers]</b>')
//                        ->add_fields( array(
//                            Field::make( 'text', 'crb_messengers_title', 'Заголовок' )->set_width( 33 )->help_text( 'Укажите название мессанджера ' ),
//                            Field::make( 'text', 'crb_messengers_icon', 'Иконка' )->set_width( 33 )->help_text( 'Укажите иконку для соц сети ' ),
//                            Field::make( 'text', 'crb_messengers_link', 'Ссылка' )->set_width( 33 )->help_text( 'Укажите ссылку для соц сети ' ),
//                     ) )
//         ) )
//         ->add_tab( 'Промо баннер (full-scren)', array(
//            Field::make( 'text','prm_banner_title','Заголовок' )->help_text('Напишите заголовок для рекламного банера'),
//            Field::make( 'textarea','prm_banner_desrpt','Краткое описание для товара или услуги' ),
//            Field::make( 'text','prm_banner_button','Кнопка' )->help_text('Текст на кнопке')->set_width(33),
//            Field::make( 'text','prm_banner_buttonlink','Ссылка' )->help_text('Укажите ссылку на страницу')->set_width(33),
//            Field::make( 'image','prm_banner_image','Картинка' )->help_text('Загрузите картику для банера')->set_width(33),
//         ) );

//         Container::make( 'theme_options' , ' Настройка сайта ' )
//         ->set_page_parent($basic_options_container) // Gallery is custom post type 
//         ->add_fields(array(
//             Field::make( 'image','crb_logo','Логотип' )->set_width(100),
//             Field::make( 'text','crb_phone','Номер телефона' )->set_width(50),
//             Field::make( 'text','crb_phone_link','Ссылка телефона' )->set_width(50),
//             Field::make( 'text','crb_mail','Впишите почту' )->set_width(50),
//             Field::make( 'text','crb_mail_link','Активная ссылка почты' )->set_width(50),
//         ) );
//         Container::make( 'theme_options' , 'Контакты и карта ' )
//         ->set_page_parent($basic_options_container) // Gallery is custom post type 
//         ->add_fields(array(
//             Field::make( 'rich_text','crb_contact_descr','Описание' )->set_width(100),
//             Field::make( 'footer_scripts','crb_map_script','Код карты' )->set_width(100)->help_text('Используйте данный шорт-код для вставки на сайт в любом месте : <b style="color:#D4363B;">[shocode_yamap]</b>'),
//         ) );
//         Container::make( 'theme_options' , 'Подписка на новости ' )
//         ->set_page_parent($basic_options_container) // Gallery is custom post type 
//         ->add_fields(array(
//             Field::make( 'image','crb_subcr_img','Картинка' )->set_width(100)->help_text('Используйте данный шорт-код для вывода на сайте  : <b style="color:#D4363B;">[shcode_subscription_news]</b>'),
//             Field::make( 'text','crb_subcr_title','Заголовок' )->set_width(33),
//             Field::make( 'text','crb_subcr_descr','Описание' )->set_width(33),
//             Field::make( 'text','crb_subcr_form','Форма захвата' )->set_width(33),
//         ) );
//         Container::make( 'theme_options', 'Медали' )
//         ->set_page_parent($basic_options_container) // Gallery is custom post type 
//         ->add_tab( 'Заслуги компании', array(
//               Field::make( 'complex', 'crb_skills', 'Список заслуг' )
//                       ->help_text('Используйте данный шорт-код для вставки на сайт в любом месте через админ панель : <b style="color:#D4363B;">[shcode_skills_company]</b>')
//                       ->add_fields( array(
//                              Field::make( 'image', 'crb_skils_img', 'Картинка' )->set_value_type( 'url' )->set_width( 100 )->help_text( 'Загрузите картинку которая будтет характиризовать заголовок' ),
//                              Field::make( 'text', 'crb_skils_title', 'Заголовок ' )->set_width( 25 ),
//                              Field::make( 'textarea', 'crb_skils_descr', 'Описание' )->set_width( 45 ),
//                              Field::make( 'text', 'crb_skils_counter', 'тут цифры' )->set_width( 10 ),
//                     ) )
//         ) );


//        Container::make( 'theme_options', __( 'Виджеты') )
//            ->set_page_file( 'theme-options-widget' )
//            ->add_fields(array(
//                Field::make( 'complex' , 'crb_widget_complex', 'Добавьте виджеты' )
//                  ->add_fields( array(
//                       Field::make( 'select', 'crb_widget_select_icon', 'Выбрать' )->set_width( 45 )->help_text('Выберите мессенджер ')
//                           ->add_options( array(
//                             '' => '',
//                             '<i class="fab fa-whatsapp"></i>' => 'whatsapp',
//                             '<i class="fab fa-viber"></i>' => 'viber',
//                             '<i class="fab fa-telegram"></i>' => 'telegram'
//                       ) ),
//                       Field::make( 'text', 'crb_widget_url_icon', 'Ссылка ' )->set_width( 100 )->help_text( '
//                         <b>Укажите ссылку на мессенджер в формате: </b> </br>  
//                         whatsapp -  https://api.whatsapp.com/send?phone=79295480820 </br> 
//                         telegram - https://teleg.run/evgen_jermes</br> 
//                         viber - viber://chat?number=79295480820</br> 
//                         ' ),
//               ) )
//            ));

       
// }



