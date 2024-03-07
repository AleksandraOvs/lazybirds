<?php
/**
**** Разработка и продвижение сайтов *****
**** по вопросам доработки/разработки ****
**** MM76.RU *****************************
**** Tel: +7 920 650 76-76 ***************
**** WhatsApp: +7 920 650 76-76 **********
**** Tg: @mm76_ru ************************
**** Site: https://mm76.ru/ ***************
*/

get_header();
$oid = get_queried_object();
?>

<section class="mt-40">
    <div class="cont">

    <div class="breads <?= (is_product() ? 'left' : ''); ?>">
        <?php
            if ( function_exists('yoast_breadcrumb') ) {
                yoast_breadcrumb( '<p id="breadcrumbs">','</p>' );
            }
        ?>
    </div>

    <?php
        if( !is_product() ){
    ?>
            <h1 class="title-page">
                <?php 
                    if(is_shop()) echo 'Каталог';
                    elseif( is_tax() ) echo $oid->name;
                    else echo get_the_title();
                ?>    
            </h1>
    <?php
        }
    ?>

    </div>
</section>

<section class="mt-40 mb-100">
    <div class="cont">
        
        <?php woocommerce_content(); ?>

    </div>
</section>

<?php
get_footer();