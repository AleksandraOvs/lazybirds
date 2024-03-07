<?php
/**
 * Template name: Главная страница
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

<section class="top__section">
    <div class="cont">
        
        <div class="cont__mini">
            <span class="elem-nav-console nav-console-left"><i class="fas fa-chevron-left"></i></span>
            <div class="two__blocks_main">
                <?php
                    if( have_rows('kategorii_dlya_bloka_1') ) {
                        while( have_rows('kategorii_dlya_bloka_1') ) {
                            the_row();
                            
                            $term = get_term( get_sub_field('kategoriya'), 'product_cat' );
                            $img = get_sub_field('izobrazhenie');
                            ?>
                                <article class="card-cat">
                                    <a href="<?= get_term_link( $term->term_id ); ?>" class="back__image">
                                        <picture>
                                            <img src="<?= $img['url']; ?>" alt="<?= $img['alt']; ?>">
                                        </picture>
                                    </a>
                                    <a href="<?= get_term_link( $term->term_id ); ?>" class="b-title"><?= $term->name; ?></a>
                                </article>
                            <?php
                        }
                    }
                ?>
            </div>
            <span class="elem-nav-console nav-console-right"><i class="fas fa-chevron-right"></i></span>
        </div>

    </div>
</section>

<section class="bests mt-40">
    <div class="cont">

        <?php
            $block_sellers = get_field('blok_best_sellers');
            $positions = $block_sellers['vyvodimye_poziczii'];
        ?>

        <div class="site-title center"><?= $block_sellers['zagolovok_bloka']; ?></div>

        <div class="cont__mini">
            <div class="container__four mt-30">
                <?php
                    if( $positions ) {
                        //foreach( $positions as $prod ) {
                            $q_args = [
                                'post_type' => 'product',
                                'posts_per_page' => 4,
                                'post__in' => $positions,
                            ];
                            $q_prods = new WP_Query( $q_args );

                            if( $q_prods->have_posts() ) {
                                while( $q_prods->have_posts() ) {
                                    $q_prods->the_post();

                                    $product = wc_get_product( get_the_ID() );
                            
                                    wc_get_template_part( 'content', 'product' );

                                }
                                wp_reset_query();
                            }
                        //}
                    }
                ?>
            </div>
            <div class="text-align-center mt-30">
                <a href="<?= get_the_permalink( 7 ); ?>" class="btn">Смотреть все</a>
            </div>
        </div>

    </div>
</section>

<?php /* ?><section class="mt-40">
    <div class="cont">
        
        <div class="cont__mini">
            <div class="two__blocks_main">
                <!--card-->
                <article class="card-cat">
                    <a class="back__image">
                        <picture>
                            <img src="<?= get_field('fotografiya_bloka')['url']; ?>" alt="<?= get_field('fotografiya_bloka')['alt']; ?>">
                        </picture>
                    </a>
                </article>
                <!--card-->
                <div class="right_text-block">
                    <?= get_field('opisanie_bloka'); ?>
                </div>
            </div>
        </div>

    </div>
</section><? */ ?>

<section class="form__feed for-pc mt-70 mb-100">
    <div class="cont">

        <?php /// do_shortcode('[contact-form-7 id="09a636c" title="Форма подписки"]'); ?>
        <form action="/register/" class="form__feed-form" method="post">
            <div class="l-form">
                <picture>
                    <?php echo wp_get_attachment_image( 692, 'large' )?>
                </picture>
            </div>
            <div class="r-form">
                <div class="f-title">Давай дружить, пташка!</div>
                <div class="f-descr">Регистрируйся на сайте и <span>получи 1000 бонусов на первую покупку *.</span></div>
                <div class="input-row">
                    <input type="text" name="uname" id="uname" required placeholder="Имя">
                </div>
                <div class="input-row">
                    <input type="text" name="surname" id="surname" required placeholder="Фамилия">
                </div>
                <div class="input-row">
                    <input type="email" name="email" id="email" required placeholder="E-mail">
                </div>
                <div class="input-row">
                    <label><input name="have_news" type="checkbox" id="have_news" value="have_news"> Первой узнавать об акция, скидках и новинках в рассылке!</label>
                </div>
                <div class="f-btn">
                    <input type="submit" value="Получить бонусы">
                </div>
                <div class="f-ddescr">*1 бонус = 1 рубль</div>
                <div class="f-small_descr">Личные данные используются в целях, указанных в политике конфиденциальности.</div>
            </div>
        </form>

    </div>
</section>

<section class="feed_section for-mobile mt-70 mb-100">
    <div class="cont">
        <div class="ff-title">Давай дружить, пташка!</div>
        <div class="container__mobile_form_main">
            <div class="l">
                <div class="text">Регистрируйся на сайте и <span>получи 1000 бонусов* на первый заказ.</span> <br> Воспользуйся всеми преимуществами бонусной программы.<br><br>* 1 бонус = 1 рубль</div>
                <div class="t-btn">
                    <a href="/register/">Получить бонусы</a>
                </div>
            </div>
            <div class="r">
                <picture>
                    <?php echo wp_get_attachment_image( 693, 'large' )?>
                </picture>
            </div>
        </div>
    </div>
</section>

<section class="mt-40">
    <div class="cont">

        <h2 class="site-title center">Наши коллекции</h2>

        <div class="cont__mini">
            <div class="container__three mt-30">
                <?php
                    $taxes = get_field('vyberite_kollekcziyu');
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
                                    <div class="btn"><?= $term->name; ?></div>
                                </div>
                            </a>
                            <?php
                        }
                    }
                ?>
            </div>
            <div class="text-align-center mt-30">
                <a href="<?= get_the_permalink( 7 ); ?>" class="btn">Смотреть все</a>
            </div>
        </div>

    </div>
</section>

<?php
get_footer();