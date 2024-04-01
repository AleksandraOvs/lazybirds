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
<section class="page-section">
        <div class="cont">
<?php
if (!(is_account_page())) {
?>

    
            <div class="breads">
                <?php
                if (function_exists('yoast_breadcrumb')) {
                    yoast_breadcrumb('<p id="breadcrumbs">', '</p>');
                }
                ?>
            </div>

            <h1 class="title-page"><?php the_title(); ?></h1>
       
<?php
}else {
    ?>
    
    <div class="breads">
                <?php
                if (function_exists('yoast_breadcrumb')) {
                    yoast_breadcrumb('<p id="breadcrumbs">', '</p>');
                }
                ?>
            </div>
      
    <?php
}
?>
 
        <?php the_content(); ?>
        
</div>
    </section>

       

<?php
get_footer();
