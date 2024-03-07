<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;


add_action( 'carbon_fields_register_fields', 'crb_attach_tabs_container' ); // Для версии 2.0 и выше
// add_action( 'carbon_register_fields', 'crb_attach_theme_options' ); // Для версии 1.6 и ниже
function crb_attach_tabs_container() {
Container::make( 'theme_options' , ' Заголовок контейнера ' )
       ->add_tab( 'Цветовая схема', array(
       		Field::make('color','my_color_1','Цвет 1 '),
       		Field::make('color','my_color_2','Цвет 2 '),
       		Field::make('color','my_color_3','Цвет 3 ')
       	) )
       ->add_tab( 'Напишите цитаты', array(
       		Field::make('textarea','my_textarea_1','Цитата 1 '),
       		Field::make('textarea','my_textarea_2','Цитата 2 '),
       		Field::make('textarea','my_textarea_3','Цитата 3 ')
       	) )
       ->add_tab( 'Файлы для скачивания', array(
       		Field::make('file','my_file_1','Файл 1 '),
       		Field::make('file','my_file_2','Файл 2 '),
       		Field::make('file','my_file_3','Файл 3 ')
       	) );
}