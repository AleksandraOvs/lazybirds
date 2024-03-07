<?php
/**
 * Template name: Страница Коллекций
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

    <h1 class="title-page">
        <?php 
            echo get_the_title();
        ?>    
    </h1>

    </div>
</section>

<section class="mt-40 mb-100">
    <div class="cont">
        <div class="cont__mini">
            
            <div class="collections-grid mt-40">

                <?php
                    $taxes = get_field('vyberite_kollekczii', 'option');
                    if( $taxes ) {
                        foreach($taxes as $tax) {

                            $term = get_term($tax, 'product_cat');
                            $image_id = get_woocommerce_term_meta( $term->term_id, 'thumbnail_id', true );
                            ?>
                            <a href="<?= get_term_link($tax, 'product_cat'); ?>" class="card-collection">
                                <picture>
                                    <?= wp_get_attachment_image( $image_id, 'full' ); ?>
                                </picture>
                                <div class="btn-abs">
                                    <div class="cat-name"><?= $term->name; ?></div>
                                </div>
                            </a>
                            <?php
                        }
                    }
                ?>

            </div>

        </div>
    </div>
</section>

<?php
get_footer();