<?php get_header() ?>
<section class="hero-section">
    <div class="cont">
    
            <div class="hero-section__l">
                <div class="slide-content-top">
                    <span class="desc slide-desc">Crazy at work - lazy at home</span>
                <h1 class="slide-head">Пижамы и одежда</h1>
                <h1 class="slide-head_mob">Пижамы и одежда для&nbsp;дома</h1>
                </div>
                
                <div class="hero__inner__content">
                    <div class="hero__inner__content__pic"></div>
                <div class="slide-flex">
                    <div class="slide-flex-left">
                    </div>
                    <div class="slide-flex-right">для дома</div>
                    <div class="slide-flex-desc">
                    Долгожданная новинка — коллекция Mix&Match. Теперь ты можешь сочетать футболки и шорты между собой.
                    </div>
                    <a href="<?php echo site_url('/product-category/pizhamy/mixmatch')?>" class="btn">Смотреть всю коллекцию</a>
                </div>

            </div>
            
        
            </div>
            
            <div class="hero-section__r">
                <!-- <img src="<?php //echo get_stylesheet_directory_uri().'/assets/img/second.jpg'?>" alt=""> -->
            </div>
      
        <!-- <div class="hero-left"></div>
        <div class="hero-right"></div> -->
    </div>
</section>

<section class="bestsellers mt-100">
    <div class="cont">

    <div class="section-header">
        
        <div class="section-header-desc">
            <span>Надо брать!</span>
        </div>
        
        <?php $block_sellers = get_field('blok_best_sellers'); ?>
        <div class="site-title bestsell-slider">
            <h2><?= $block_sellers['zagolovok_bloka']; ?></h2>
            <div class="swiperBestsellers-controls">
            <svg class="swiperBestsellers-control-prev" xmlns="http://www.w3.org/2000/svg" width="100" height="40" viewBox="0 0 100 40" fill="none">
                <g clip-path="url(#clip0_150_1226)">
                <path d="M98.4018 19.0318C94.9154 18.2615 91.1943 18.4774 87.6504 18.2907C83.6719 18.0784 79.6578 17.7335 75.697 17.5875C67.7577 17.2291 59.7919 17.304 51.8261 17.3789C44.0776 17.4665 36.2803 17.6383 28.523 18.2254C24.5357 18.5126 20.5662 18.866 16.6499 19.4183C13.084 19.9476 8.42695 20.1302 5.24717 21.8344C3.93889 22.5401 4.33848 24.5636 5.85981 24.6531C9.48796 24.8883 13.4085 23.5537 17.0409 23.0065C20.9572 22.4543 24.9578 21.9505 28.9273 21.597C36.6491 20.8774 44.4109 20.5731 52.2259 20.4676C59.757 20.3672 67.2572 20.4171 74.7396 20.4007C78.7136 20.3302 82.6522 20.1271 86.5774 20.1406C90.4849 20.0878 94.5522 20.6315 98.4508 20.013C98.8677 19.9723 98.872 19.1899 98.4018 19.0318Z" fill="#003996"/>
                <path d="M23.4934 13.5661C16.1907 13.7604 9.85578 18.9122 3.11381 20.9302C1.51681 21.4286 2.03435 23.8984 3.42156 24.2316C10.7789 25.8556 18.2707 27.4435 25.5143 29.4505C26.767 29.8198 26.879 27.8159 25.7785 27.4763C18.5109 24.8413 11.3986 22.5172 3.83944 20.9473C3.9196 22.0538 4.06702 23.1422 4.14719 24.2487C7.543 23.1978 10.6104 21.4594 13.5577 19.5417C16.7564 17.4862 20.6968 16.8533 23.7258 14.7023C24.2607 14.277 23.9996 13.5715 23.4934 13.5661Z" fill="#003996"/>
                </g>
                <defs>
                <clipPath id="clip0_150_1226">
                <rect width="100" height="40" fill="white"/>
                </clipPath>
                </defs>
            </svg>

            <svg class="swiperBestsellers-control-next" xmlns="http://www.w3.org/2000/svg" width="100" height="40" viewBox="0 0 100 40" fill="none">
                <g clip-path="url(#clip0_150_1232)">
                <path d="M1.6368 19.0318C5.12313 18.2615 8.84431 18.4774 12.3882 18.2907C16.3667 18.0784 20.3808 17.7335 24.3415 17.5875C32.2808 17.2291 40.2467 17.304 48.2125 17.3789C55.961 17.4665 63.7583 17.6383 71.5155 18.2254C75.5028 18.5126 79.4724 18.866 83.3887 19.4183C86.9546 19.9476 91.6116 20.1302 94.7914 21.8344C96.0997 22.5401 95.7001 24.5636 94.1788 24.6531C90.5506 24.8883 86.6301 23.5537 82.9976 23.0065C79.0814 22.4543 75.0808 21.9505 71.1112 21.597C63.3895 20.8774 55.6277 20.5731 47.8127 20.4676C40.2815 20.3672 32.7814 20.4171 25.299 20.4007C21.3249 20.3302 17.3864 20.1271 13.4612 20.1406C9.55364 20.0878 5.48633 20.6315 1.5878 20.013C1.17089 19.9723 1.16662 19.1899 1.6368 19.0318Z" fill="#003996"/>
                <path d="M76.5452 13.5661C83.8479 13.7604 90.1828 18.9122 96.9248 20.9302C98.5218 21.4286 98.0042 23.8984 96.617 24.2316C89.2597 25.8556 81.7678 27.4435 74.5242 29.4505C73.2716 29.8198 73.1596 27.8159 74.2601 27.4763C81.5277 24.8413 88.64 22.5172 96.1991 20.9473C96.119 22.0538 95.9715 23.1422 95.8914 24.2487C92.4956 23.1978 89.4282 21.4594 86.4809 19.5417C83.2822 17.4862 79.3417 16.8533 76.3128 14.7023C75.7778 14.277 76.039 13.5715 76.5452 13.5661Z" fill="#003996"/>
                </g>
                <defs>
                <clipPath id="clip0_150_1232">
                <rect width="100" height="40" fill="white"/>
                </clipPath>
                </defs>
            </svg>
            </div>
        </div>
        
    </div>

        <?php
            
            $positions = $block_sellers['vyvodimye_poziczii'];
        ?>

        
            <div class="swiper swiperBestsellers">
                <?php
                    if( $positions ) {
                        //foreach( $positions as $prod ) {
                            $q_args = [
                                'post_type' => 'product',
                                'posts_per_page' => 20,
                                'post__in' => $positions,
                            ];
                            $q_prods = new WP_Query( $q_args );

                            if( $q_prods->have_posts() ) {
                                ?>
                                <div class="swiper-wrapper">
                                <?php
                                while( $q_prods->have_posts() ) {
                                    $q_prods->the_post();

                                    $product = wc_get_product( get_the_ID() );
                                    
                                    ?>
                                    <div class="swiper-slide">
                                        <?php wc_get_template_part( 'content', 'product' ); ?>
                                    </div>
                                    <?php
                                    
                                }
                                wp_reset_query();
                                ?>
                                </div>
                                <?php
                            }
                            ?>

                            <?php
                        //}
                    }
                ?>
            </div>
            
            <!-- only mob <576px -->
            <div class="blockBestsellers">
            <?php
                    if( $positions ) {
                        //foreach( $positions as $prod ) {
                            $q_args = [
                                'post_type' => 'product',
                                'posts_per_page' => 6,
                                'post__in' => $positions,
                            ];
                            $q_prods = new WP_Query( $q_args );

                            if( $q_prods->have_posts() ) {
                                ?>
                               
                                <?php
                                while( $q_prods->have_posts() ) {
                                    $q_prods->the_post();

                                    $product = wc_get_product( get_the_ID() );
                                    
                                    ?>
                                    
                                        <?php wc_get_template_part( 'content', 'product' ); ?>
                                   
                                    <?php
                                    
                                }
                                wp_reset_query();
                                ?>
                                
                                <?php
                            }
                            ?>

                            <?php
                        //}
                    }
                ?>



            </div>


            <div class="text-align-center mt-30">
                <a href="<?= get_the_permalink( 7 ); ?>" class="btn">Перейти в каталог</a>
            </div>
    </div>
</section>

<section class="section-about mt-100">
    <div class="cont">
        <div class="section-about-cont-right__head section-header_mob">
            <div class="section-header-desc">
                <span>Кратко :&#41;</span>
            </div>
            <div class="site-title">
                <h2>О нас</h2></div>
        </div>

        <div class="section-about-cont">
            <p class="about-text mob_vis">Lazy Birds — это бренд созданный ленивыми пташками специально для ленивых пташек. Если ты так же как и мы любишь не только работать, но и отдыхать, то мы точно вместе надолго!)</p>
            <div class="section-about-cont-left">
                

                <div class="section-about-cont-leftPhotos">
                    <div class="about-photo1">
                        <img src="<?php echo get_stylesheet_directory_uri() . '/assets/img/intro-img/photo1.jpg' ?>" alt="">
                    </div>
                    <div class="about-photo2">
                        <img src="<?php echo get_stylesheet_directory_uri() . '/assets/img/intro-img/photo2.jpg' ?>" alt="">
                    </div>
                    
                </div>
            </div>

            <div class="section-about-cont-right">
                <div class="section-about-cont-right__head section-header">
                    <div class="section-header-desc">
                        <span>Кратко :&#41;</span>
                    </div>
                    <div class="site-title">
                       <h2> О нас </h2>
                    </div>
                </div>
                <p class="about-text mob_hidden">Lazy Birds — это бренд созданный ленивыми пташками специально для ленивых пташек. Если ты так же как и мы любишь не только работать, но и отдыхать, то мы точно вместе надолго!)</p>

                <ul class="section-about-cont__list">
                    <li class="aboutList"><p>из натуральных материалов (терпеть не можем синтетику. Как&nbsp;в&nbsp;ней&nbsp;можно расслабиться?)</p></li>
                    <li class="aboutList"><p>расслабленного кроя (чтобы ничего не давило и не перетягивало во время того как ты лежишь звездой на диване)</p></li>
                    <li class="aboutList"><p>отшиваются в Москве (мы обожаем все контролировать)</p></li>
                </ul>

                <a href="<?php echo site_url('/o-nas')?>" class="btn">Подробнее</a>
            </div>
        </div>
    </div>
</section>

<section class="section-feedback mt-100">
    <div class="cont">
    <div class="section-header">
        <div class="site-title title-fdb">
            <h2>Каждый отзыв мы ценим и бережно храним</h2>
        </div>
    </div>
    
    <?php if( have_rows('fdb_slides') ): ?>
        <div class="swiper swiperFeedback">
            <div class="swiper-wrapper">
            <?php while( have_rows('fdb_slides') ): the_row(); 
            $attachment_id = get_sub_field('fdb_slide_img');
            $imageurl = wp_get_attachment_image_src( $attachment_id, 'full' ); // если нужна ссылка на полный размер
            $fieldname_sub = wp_get_attachment_image_src( $attachment_id, "550x200" ); // если нужна ссылка на предустановленный размер миниатюры
            $fieldname_sub = get_sub_field('fdb_slide_img');
            ?>
            <div class="swiper-slide swiperFeedback-slide">
                <a href="<?php echo $imageurl[0]; ?>" class="swiperFeedback-slide-link" data-fancybox="gallery">
                    <img src="<?php echo $imageurl[0]; ?>" alt="">
                </a>
                <?php //echo $fieldname_sub; ?>
                
                <?php //if( $fieldname_sub ): ?><?php //echo $fieldname_sub[0]; ?><?php //endif; ?>
            </div>

            <?php endwhile; ?>
            </div>

            <div class="swiperFeedback-controls">
            <svg class="swiperFeedback-control-prev" xmlns="http://www.w3.org/2000/svg" width="100" height="40" viewBox="0 0 100 40" fill="none">
                <g clip-path="url(#clip0_150_1226)">
                <path d="M98.4018 19.0318C94.9154 18.2615 91.1943 18.4774 87.6504 18.2907C83.6719 18.0784 79.6578 17.7335 75.697 17.5875C67.7577 17.2291 59.7919 17.304 51.8261 17.3789C44.0776 17.4665 36.2803 17.6383 28.523 18.2254C24.5357 18.5126 20.5662 18.866 16.6499 19.4183C13.084 19.9476 8.42695 20.1302 5.24717 21.8344C3.93889 22.5401 4.33848 24.5636 5.85981 24.6531C9.48796 24.8883 13.4085 23.5537 17.0409 23.0065C20.9572 22.4543 24.9578 21.9505 28.9273 21.597C36.6491 20.8774 44.4109 20.5731 52.2259 20.4676C59.757 20.3672 67.2572 20.4171 74.7396 20.4007C78.7136 20.3302 82.6522 20.1271 86.5774 20.1406C90.4849 20.0878 94.5522 20.6315 98.4508 20.013C98.8677 19.9723 98.872 19.1899 98.4018 19.0318Z" fill="#003996"/>
                <path d="M23.4934 13.5661C16.1907 13.7604 9.85578 18.9122 3.11381 20.9302C1.51681 21.4286 2.03435 23.8984 3.42156 24.2316C10.7789 25.8556 18.2707 27.4435 25.5143 29.4505C26.767 29.8198 26.879 27.8159 25.7785 27.4763C18.5109 24.8413 11.3986 22.5172 3.83944 20.9473C3.9196 22.0538 4.06702 23.1422 4.14719 24.2487C7.543 23.1978 10.6104 21.4594 13.5577 19.5417C16.7564 17.4862 20.6968 16.8533 23.7258 14.7023C24.2607 14.277 23.9996 13.5715 23.4934 13.5661Z" fill="#003996"/>
                </g>
                <defs>
                <clipPath id="clip0_150_1226">
                <rect width="100" height="40" fill="white"/>
                </clipPath>
                </defs>
            </svg>

            <svg class="swiperFeedback-control-next" xmlns="http://www.w3.org/2000/svg" width="100" height="40" viewBox="0 0 100 40" fill="none">
                <g clip-path="url(#clip0_150_1232)">
                <path d="M1.6368 19.0318C5.12313 18.2615 8.84431 18.4774 12.3882 18.2907C16.3667 18.0784 20.3808 17.7335 24.3415 17.5875C32.2808 17.2291 40.2467 17.304 48.2125 17.3789C55.961 17.4665 63.7583 17.6383 71.5155 18.2254C75.5028 18.5126 79.4724 18.866 83.3887 19.4183C86.9546 19.9476 91.6116 20.1302 94.7914 21.8344C96.0997 22.5401 95.7001 24.5636 94.1788 24.6531C90.5506 24.8883 86.6301 23.5537 82.9976 23.0065C79.0814 22.4543 75.0808 21.9505 71.1112 21.597C63.3895 20.8774 55.6277 20.5731 47.8127 20.4676C40.2815 20.3672 32.7814 20.4171 25.299 20.4007C21.3249 20.3302 17.3864 20.1271 13.4612 20.1406C9.55364 20.0878 5.48633 20.6315 1.5878 20.013C1.17089 19.9723 1.16662 19.1899 1.6368 19.0318Z" fill="#003996"/>
                <path d="M76.5452 13.5661C83.8479 13.7604 90.1828 18.9122 96.9248 20.9302C98.5218 21.4286 98.0042 23.8984 96.617 24.2316C89.2597 25.8556 81.7678 27.4435 74.5242 29.4505C73.2716 29.8198 73.1596 27.8159 74.2601 27.4763C81.5277 24.8413 88.64 22.5172 96.1991 20.9473C96.119 22.0538 95.9715 23.1422 95.8914 24.2487C92.4956 23.1978 89.4282 21.4594 86.4809 19.5417C83.2822 17.4862 79.3417 16.8533 76.3128 14.7023C75.7778 14.277 76.039 13.5715 76.5452 13.5661Z" fill="#003996"/>
                </g>
                <defs>
                <clipPath id="clip0_150_1232">
                <rect width="100" height="40" fill="white"/>
                </clipPath>
                </defs>
            </svg>
            </div>
        </div>
        

    <?php endif; ?>

    </div>
</section>

<?php
    get_template_part('templates/collections');
?>

<section class="form__feed mt-100">
    <div class="cont">

        <?php /// do_shortcode('[contact-form-7 id="09a636c" title="Форма подписки"]'); ?>
        <form action="/register/" class="form__feed-form" method="post">
            <div class="l-form">
                <div class="section-header-desc">
                    <span>Не зевай!</span>   
                </div>
                <p>Мы знаем как сложно решиться на первую покупку, особенно&nbsp;когда&nbsp;бренд&nbsp;ещё не знаком.</p>

                <p class="accent-text">
                Чтобы наше знакомство прошло легко, мы дарим тебе 1000 рублей на первую покупку. 
                </p>

                <p>Сумма появится в твоём личном кабинете сразу после регистрации на сайте. </p>

                <p>Уверены, что это начало долгой и крепкой дружбы.</p>
                <!-- <picture>
                    <?php //echo wp_get_attachment_image( 692, 'large' )?>
                </picture> -->
            </div>
            <div class="r-form">
                
                <!-- <div class="f-descr">Регистрируйся на сайте и <span>получи 1000 бонусов на первую покупку *.</span></div> -->
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
                    <label><input name="have_news" type="checkbox" id="have_news" value="have_news">
                    <p class="input-row__desc">Первой узнавать об акция, скидках и новинках в рассылке!</p>
                    </label>
                </div>
                <div class="f-btn__inner">
                    <input class="btn f-btn" type="submit" value="Получить бонусы">
                    <div class="f-ddescr">*1 бонус = 1 рубль</div>
                </div>
                
                <div class="f-small_descr">Личные данные используются в целях, указанных в политике конфиденциальности.</div>
                
                <div class="section-header-desc">
                    <span>Всё в дом!</span>
                </div>
            </div>
            
        </form>
</section>

<section class="section-faq mb-100 mt-100">
    <div class="cont section-faq__inner">   
        <div class="section-faq-l">
             <div class="site-title">
            Частые вопросы
            </div>
            <p>Несколько строчек, подзаголовок</p>
            <?php
                    $faqs = get_field('faq_items');
                    if( $faqs ) {  
                    ?>
                    <div id="faq">
                    <?php
                        foreach($faqs as $faq) {                       
                           ?>

                           <div class="faq-question-head" class="">
                            <h3><?php echo $faq['faq-item_question'] ?></h3>
                            <div class="faq-question-head__button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50" fill="none">
                                    <path d="M20.6547 14.9061C21.7312 20.9676 20.2524 27.3936 21.1867 33.5265C21.3248 34.5192 23.0609 35.0447 23.243 33.8025C23.7176 30.289 23.1649 26.3892 22.859 22.8787C22.6551 20.5384 23.0609 16.4577 21.1875 14.7267C21.0111 14.4791 20.6205 14.587 20.6547 14.9061Z" fill="#003996"/>
                                    <path d="M15.0933 24.6776C19.5333 25.0346 24.0834 24.9287 28.5945 25.2851C30.6902 25.4468 32.7506 25.5732 34.8463 25.7349C36.9062 25.9324 38.8924 26.4859 40.917 26.6481C41.7695 26.7127 42.0278 25.4668 41.3913 25.0451C38.3161 22.8295 32.8749 23.2621 29.18 23.0771C24.5615 22.8281 19.3749 22.5123 14.8177 23.5423C14.0699 23.7258 14.5604 24.6461 15.0933 24.6776Z" fill="#003996"/>
                                    <path d="M35.648 15.1168C32.7003 10.6092 25.438 7.81402 20.1029 8.64787C17.029 9.11941 14.6905 11.1063 12.9501 13.6203C11.3161 16.028 9.29061 19.1788 9.13729 22.1454C9.02029 24.8647 9.71241 28.3222 11.0093 30.6827C11.7452 32.0568 12.8705 33.1821 13.8188 34.414C14.9078 35.7867 15.9256 37.3008 17.3689 38.3895C21.5225 41.5505 28.8013 40.3203 32.9785 37.6904C43.1385 31.258 41.9646 16.0444 31.109 11.2159C25.9632 8.90648 18.3362 8.93754 14.5744 13.6137C10.5995 18.5732 8.74068 24.1597 10.9046 30.3653C13.138 36.8532 19.5094 41.6646 26.4653 41.6716C33.4213 41.6786 39.2314 36.9234 41.0239 30.278C41.2392 29.4297 39.8286 29.0117 39.5778 29.8955C36.3873 41.6665 20.5258 43.5319 14.1402 33.53C11.3332 29.1631 10.7572 23.1981 13.2831 18.5623C14.4214 16.5097 16.3044 13.642 18.4276 12.5034C20.8339 11.2225 24.4701 11.3842 27.0813 11.7973C37.3152 13.2386 41.8554 25.5078 35.185 33.4443C32.0982 37.0937 27.5015 38.6661 22.7706 38.5441C19.4166 38.4518 17.5871 36.835 15.6552 34.3359C13.6882 31.8017 11.8604 29.7612 11.1675 26.5155C10.5437 23.6227 10.5174 21.3983 11.8701 18.7093C13.2583 15.9848 14.7888 12.9773 17.4793 11.2714C22.4351 8.21465 31.5678 11.2494 34.7631 15.6501C35.2199 16.1778 36.0339 15.7155 35.648 15.1168Z" fill="#003996"/>
                                </svg>
                            </div>
                            </div>
                           
                            <div class="faq-answer">
                                <p><?php echo $faq['faq-item_answer'] ?></p>
                            </div>
                           
                            <?php
                        }
                    ?>
                    </div>
                    <?php
                        
                    }    
                ?>
        </div>   
        <div class="section-faq-r">
            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/img/faq-section-pic.png' ?>" alt="">
        </div>   
       
    </div>
</section>

<?php get_footer() ?>