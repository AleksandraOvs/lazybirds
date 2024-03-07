<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;



add_action( 'carbon_fields_register_fields', 'crb_attach_slider' ); // Для версии 2.0 и выше

function crb_attach_slider() {

$basic_options_container = Container::make( 'theme_options', 'Пульт' )
               ->add_tab( 'Слайдер большой', array(
                      Field::make( 'complex', 'crb_slider', 'Список слайдов' )
                              ->help_text('Используйте данный шорт-код для вставки на сайт в любом месте через админ панель : <b style="color:#D4363B;">[shcode_slider_main]</b>')
                              ->add_fields( array(
                                     Field::make( 'image', 'sld_main_picture', 'Картинка слайда' )->set_value_type( 'url' )->set_width( 100 )->help_text( 'Загрузите картинку для слайдера' ),
                                     Field::make( 'text', 'sld_main_title', 'Заголовок ' )->set_width( 50 ),
                                     Field::make( 'textarea', 'sld_main_descr', 'Описание товара, услуги' )->set_width( 50 ),
                                     Field::make( 'text', 'sld_main_btn', 'Название кнопки' )->set_width( 50 ),
                                     Field::make( 'text', 'sld_main_btn__url', 'Ссылка ' )->set_width( 50 )->help_text( 'Укажите ссылку на страницу с рекламируемым товаром  ' ),
                            ) )
               ) )
               ->add_tab( 'Слайдер маленький', array(
                      Field::make( 'complex', 'crb_slider_s', 'Список слайдов' )
                              ->help_text('Используйте данный шорт-код для вставки на сайт в любом месте : <b style="color:#D4363B;">[shcode_slider_small]</b>')
                               ->add_fields( array(
                                     Field::make( 'image', 'sld_s_pic', 'Картинка слайда' )->set_value_type( 'url' )->set_width( 100 )->help_text( 'Загрузите картинку для слайдера' ),
                                     Field::make( 'text', 'sld_s_url', 'Ссылка ' )->set_width( 50 )->help_text( 'Укажите ссылку на страницу категории или товара' ),
                            ) )
               ) )
               ->add_tab( 'Таб слайдер', array(
                      Field::make( 'complex', 'crb_slider_tab', 'Список слайдов' )
                              ->help_text('Используйте данный шорт-код для вставки на сайт в любом месте : <b style="color:#D4363B;">[shcode_slider_small]</b>')
                               ->add_fields( array(
                                     Field::make( 'image', 'sld_s_pic', 'Картинка слайда' )->set_value_type( 'url' )->set_width( 100 )->help_text( 'Загрузите картинку для слайдера' ),
                                     Field::make( 'text', 'sld_s_url', 'Ссылка ' )->set_width( 50 )->help_text( 'Укажите ссылку на страницу категории или товара' ),
                            ) )
               ) )
               ->add_tab( 'Промо баннер (full-scren)', array(
                     Field::make( 'text','prm_banner_title','Заголовок' )->help_text('Напишите заголовок для рекламного банера'),
                     Field::make( 'textarea','prm_banner_desrpt','Краткое описание для товара или услуги' ),
                     Field::make( 'text','prm_banner_button','Кнопка' )->help_text('Текст на кнопке')->set_width(50),
                     Field::make( 'text','prm_banner_buttonlink','Ссылка' )->help_text('Укажите ссылку на страницу')->set_width(50),
                     Field::make( 'image','prm_banner_image','Картинка' )->help_text('Загрузите картику для банера')->set_width(100),

               ) );

Container::make( 'theme_options' , ' Настройка сайта ' )
              ->set_page_parent($basic_options_container) // Gallery is custom post type 
                ->add_fields(array(
                    Field::make( 'text','crb_phone','Номер телефона' ),
                    Field::make( 'text','crb_phone_link','Ссылка телефона' ),
                    Field::make( 'text','crb_mail','Впишите почту' ),
                    Field::make( 'text','crb_mail_link','Активная ссылка почты' ),
                ) );

       
}



