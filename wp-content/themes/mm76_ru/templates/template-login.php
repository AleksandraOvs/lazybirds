<?php

/**
 * Template name: Страница логина
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

<section class="login_form_container">
    <div class="cont">
        <div class="breads">
            <?php
            if (function_exists('yoast_breadcrumb')) {
                yoast_breadcrumb('<p id="breadcrumbs">', '</p>');
            }
            ?>
        </div>

        <h1 class="titles"><?php the_title(); ?></h1>

        <?php the_content(); ?>
    </div>


</section>

<?php
get_footer();
