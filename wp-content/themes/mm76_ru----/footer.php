

</main>

<footer>
    <div class="cont">

        <div class="container__footer">
            <a href="<?= get_home_url(); ?>" class="logo_footer">
                <picture>
                    <img src="<?= get_field('logo_footer', 'option')['url']; ?>" alt="<?= get_field('logo_footer', 'option')['alt']; ?>">
                </picture>
            </a>
            <div class="col">
                <p><strong>Стань частью нашего сообщества</strong></p>
                <?php
                    if( have_rows('kontakty', 'option') ) {
                        while( have_rows('kontakty', 'option') ) {
                            the_row();
                            ?>
                                <p><a target="_blank" rel="nofollow" href="<?= get_sub_field('ssylka_kontakta'); ?>"><?= get_sub_field('zagolovok_kontakta'); ?></a></p>
                            <?php
                        }
                    }
                ?>
            </div>
            <div class="col">
                <p><?= get_field('nazvanie_ip', 'option'); ?></p>
                <p>ОГРН <?= get_field('ogrn', 'option'); ?> / ИНН <?= get_field('inn', 'option'); ?></p>
                <p><a href="<?= get_the_permalink( get_field('ssylka_na_politiku_konfidenczialnosti', 'option') ); ?>">Политика конфиденциальности</a></p>
                <p><a href="<?= get_the_permalink( get_field('ssylka_na_ofertu', 'option') ); ?>">Оферта</a></p>
            </div>
        </div>

    </div>
</footer>

<div class="moda-right right__box_hidden" data-action="sizes_grid" id="sizes_grid">
    <div class="rb-close">
        <svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6.67184 6.67187C7.08206 6.26178 7.63836 6.0314 8.2184 6.0314C8.79845 6.0314 9.35475 6.26178 9.76497 6.67187L17.5 14.4069L25.235 6.67187C25.6475 6.2734 26.2001 6.05291 26.7737 6.0579C27.3472 6.06288 27.8959 6.29294 28.3014 6.69852C28.707 7.1041 28.9371 7.65275 28.9421 8.22631C28.9471 8.79986 28.7266 9.35243 28.3281 9.765L20.5931 17.5L28.3281 25.235C28.7266 25.6476 28.9471 26.2001 28.9421 26.7737C28.9371 27.3472 28.707 27.8959 28.3014 28.3015C27.8959 28.7071 27.3472 28.9371 26.7737 28.9421C26.2001 28.9471 25.6475 28.7266 25.235 28.3281L17.5 20.5931L9.76497 28.3281C9.3524 28.7266 8.79983 28.9471 8.22628 28.9421C7.65272 28.9371 7.10407 28.7071 6.69849 28.3015C6.29291 27.8959 6.06285 27.3472 6.05787 26.7737C6.05288 26.2001 6.27337 25.6476 6.67184 25.235L14.4068 17.5L6.67184 9.765C6.26175 9.35478 6.03137 8.79848 6.03137 8.21844C6.03137 7.63839 6.26175 7.08209 6.67184 6.67187Z" fill="#98BBEA"/>
        </svg>
    </div>
    <div class="rb-title">Таблица размеров</div>
    <div class="rb-content">
        <ul class="size-table"><li class="size-table__title">Размер</li><li class="size-table__title">Обхват груди</li><li class="size-table__title">Обхват талии</li><li class="size-table__title">Обхват бедер</li><li class="size-table__body">S (42-44)</li><li class="size-table__body">86-90</li><li class="size-table__body">62-70</li><li class="size-table__body">90-98</li><li class="size-table__body">M (44-46)</li><li class="size-table__body">90-96</li><li class="size-table__body">70-76</li><li class="size-table__body">98-105</li><li class="size-table__body">L (46-48)</li><li class="size-table__body">96-106</li><li class="size-table__body">76-82</li><li class="size-table__body">105-115</li></ul>
        <p>Наши пижамы и костюмы свободного кроя, поэтому мы рекомендуем выбирать твой привычный размер.</p>
    </div>
</div>

<div class="moda-right left__box_hidden" data-action="menu_left" id="menu_left">
    <div class="rb-close">
        <svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6.67184 6.67187C7.08206 6.26178 7.63836 6.0314 8.2184 6.0314C8.79845 6.0314 9.35475 6.26178 9.76497 6.67187L17.5 14.4069L25.235 6.67187C25.6475 6.2734 26.2001 6.05291 26.7737 6.0579C27.3472 6.06288 27.8959 6.29294 28.3014 6.69852C28.707 7.1041 28.9371 7.65275 28.9421 8.22631C28.9471 8.79986 28.7266 9.35243 28.3281 9.765L20.5931 17.5L28.3281 25.235C28.7266 25.6476 28.9471 26.2001 28.9421 26.7737C28.9371 27.3472 28.707 27.8959 28.3014 28.3015C27.8959 28.7071 27.3472 28.9371 26.7737 28.9421C26.2001 28.9471 25.6475 28.7266 25.235 28.3281L17.5 20.5931L9.76497 28.3281C9.3524 28.7266 8.79983 28.9471 8.22628 28.9421C7.65272 28.9371 7.10407 28.7071 6.69849 28.3015C6.29291 27.8959 6.06285 27.3472 6.05787 26.7737C6.05288 26.2001 6.27337 25.6476 6.67184 25.235L14.4068 17.5L6.67184 9.765C6.26175 9.35478 6.03137 8.79848 6.03137 8.21844C6.03137 7.63839 6.26175 7.08209 6.67184 6.67187Z" fill="#98BBEA"/>
        </svg>
    </div>
    <div class="rb-title for-pc">Меню</div>
    <div class="rb-content for-pc">
        <?php wp_nav_menu([
            'menu' => 'MainMenu',
        ]); ?>
    </div>
    <div class="rb-content for-mobile">
        <div class="search-mobile">
            <form action="<?= get_home_url(); ?>">
                <input type="text" name="s" placeholder="Поиск..">
                <button class="btn-left" type="submit">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </button>
            </form>
        </div>
        <?php wp_nav_menu([
            'menu' => 'MainMenu',
        ]); ?>
        <hr>
        <ul class="lk_mobile">
            <li>
                <a href="/my-account/">
                    <span>Личный кабинет</span>
                    <div class="icon">
                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.9898 11.9032C12.1208 12.7117 13.4468 13.131 14.826 13.116C15.1845 13.1122 15.5475 13.0777 15.9053 13.0147C18.8438 12.495 21.0383 10.0222 21.2415 7.00124C21.4358 4.11449 19.5563 1.3725 16.773 0.480745C16.4325 0.371995 16.0823 0.305995 15.7388 0.241495C15.5933 0.214495 15.4433 0.185995 15.2955 0.154495L14.1488 0.158995L14.0768 0.170995C14.0558 0.176995 14.0348 0.183745 14.013 0.190495C13.9958 0.195745 13.9763 0.202495 13.965 0.204745C12.201 0.428245 10.6418 1.33125 9.57525 2.74875C8.517 4.155 8.082 5.88449 8.352 7.61924C8.62275 9.35924 9.5595 10.881 10.9898 11.904V11.9032ZM11.0138 6.65625C11.004 5.661 11.3843 4.72049 12.0833 4.00724C12.7808 3.29625 13.7145 2.89799 14.7113 2.88749C16.7843 2.86874 18.489 4.52475 18.5115 6.57975C18.5228 7.5975 18.1448 8.54999 17.4473 9.26174C16.7498 9.97424 15.8018 10.3725 14.7788 10.3837C13.7805 10.3935 12.8408 10.0125 12.1313 9.31049C11.421 8.60774 11.0243 7.66499 11.0145 6.65625H11.0138Z" fill="#8EBCEE"/>
                            <path d="M28.188 25.788C27.6278 22.6552 25.944 19.842 23.4457 17.8665C20.871 15.8295 17.6325 14.7705 14.0752 14.8042C9.95475 14.988 6.30075 17.1412 3.78525 20.8673C2.3235 23.0325 1.58325 25.5285 1.58475 28.2848C1.58475 29.0565 2.0415 29.6917 2.74725 29.9032C2.91675 29.9542 3.08775 29.979 3.2565 29.9768C3.5715 29.973 3.87525 29.8755 4.13475 29.6933C4.569 29.3888 4.83675 28.8795 4.86975 28.2968C4.87575 28.1835 4.88175 28.0703 4.88775 27.957C4.91475 27.4178 4.9425 26.8597 5.04525 26.3317C6.09225 20.9617 10.9215 17.4547 16.281 18.1732C20.4323 18.7305 23.2597 21.0983 24.6862 25.212C24.96 26.001 25.1048 26.901 25.1423 28.0447C25.1588 28.542 25.3493 28.9852 25.6793 29.2912C25.9898 29.5792 26.4083 29.727 26.8575 29.7105C27.2985 29.6918 27.699 29.508 27.9848 29.1938C28.2848 28.8638 28.4363 28.4153 28.4108 27.9308C28.38 27.3405 28.326 26.5635 28.1872 25.788H28.188Z" fill="#8EBCEE"/>
                        </svg>
                    </div>
                </a>
            </li>
            <li>
                <a href="/wishlist//">
                    <span>Список желаний</span>
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="34" viewBox="0 0 40 34" fill="none">
                            <path d="M21.116 31.0276C20.504 31.2242 19.496 31.2242 18.884 31.0276C13.664 29.4049 2 22.6356 2 11.1623C2 6.09762 6.482 2 12.008 2C15.284 2 18.182 3.44236 20 5.67147C21.818 3.44236 24.734 2 27.992 2C33.518 2 38 6.09762 38 11.1623C38 22.6356 26.336 29.4049 21.116 31.0276Z" stroke="#8EBCEE" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </a>
            </li>
        </ul>
    </div>
</div>

<script>
    $ajax = '<?php echo admin_url( "admin-ajax.php" ) ?>';
</script>

<div class="overlay"></div>

<?php wp_footer(); ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script src="https://kit.fontawesome.com/66298a2362.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="<?= get_template_directory_uri(); ?>/assets/js/jquery.maskedinput.min.js"></script>
<script src="<?= get_template_directory_uri(); ?>/assets/js/konte_js_notify.min.js"></script>
<script src="<?= get_template_directory_uri(); ?>/assets/js/scripts.js"></script>

</body>
</html>