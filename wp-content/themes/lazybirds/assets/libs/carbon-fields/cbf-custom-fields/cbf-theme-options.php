<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'carbon_fields_register_fields', 'cbf_attach_theme_options' ); 
function cbf_attach_theme_options() {

    Container::make( 'theme_options' , ' таблица размеров ' )
    ->set_icon( 'dashicons-carrot' )
    ->add_tab( 'таблица размеров ', array(

        Field::make( 'text', 'complex_size_table_title','Размер' )->set_width( 10 ),                
        Field::make( 'text', 'complex_size_table_title_bust' , 'Обхват груди' )->set_width( 10 ),                
        Field::make( 'text', 'complex_size_table_title_waist' , 'Обхват талии' )->set_width( 10 ),                
        Field::make( 'text', 'complex_size_table_title_hip_girth' , 'Обхват бедер' )->set_width( 10 ),                
         Field::make( 'complex', 'complex_size', 'Описание размеров' )
             ->add_fields( array(                             
                 Field::make( 'text', 'complex_size_table_descript' , '' )->set_width( 10 ),                
                 Field::make( 'text', 'complex_size_table_descript_bust' , '' )->set_width( 10 ),                
                 Field::make( 'text', 'complex_size_table_descript_waist' , '' )->set_width( 10 ),                
                 Field::make( 'text', 'complex_size_table_descript_hip_girth' , '' )->set_width( 10 ),                
        ) )
    ) )
    ->add_tab( 'Картинка на главной ', array(
        Field::make( 'image', 'cbf_banner_lage','— Картинка большая для десктопа' )
            ->set_width( 30 )
            ->help_text('Поставьте картинку  1938 на 513 пикселей'),
        Field::make( 'image', 'cbf_banner_small','— Картинка маленькая для телефона ' )
            ->set_width( 30 )
            ->help_text('Поставьте картинку  1118 на 1119 пикселей '),
     ) );
}
