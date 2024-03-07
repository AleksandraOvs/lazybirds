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

        <h1 class="title-page"><?php the_title(); ?></h1>
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
    <div class="cont">
        <?= (get_the_ID() == 8 || get_the_ID() == 9 || get_the_ID() == 539 ? '' : '<div class="text-block">'); ?>
            <?php the_content(); ?>
        <?= (get_the_ID() == 8 || get_the_ID() == 9 || get_the_ID() == 539 ? '' : '</div>'); ?>
    </div>
</section>

<?php
get_footer();