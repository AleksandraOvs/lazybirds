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
?>

<section class="mt-40">
    <div class="cont">

        <h1 class="title-page">Поиск: <?php echo $_GET['s']; ?></h1>
        <div class="breads">
            <?php
                if ( function_exists('yoast_breadcrumb') ) {
                    yoast_breadcrumb( '<p id="breadcrumbs">','</p>' );
                }
            ?>
        </div>

    </div>
</section>

<section class="mt-40">
    <div class="cont__mini">
        <div class="container__three mt-40">
            <?php
                if( have_posts() ) {
                    while( have_posts() ) {
                        the_post();
                    
                        $product = wc_get_product( get_the_ID() );
                                
                        wc_get_template_part( 'content', 'product' );

                    }
                }
            ?>
        </div>
    </div>
</section>

<?php
get_footer();