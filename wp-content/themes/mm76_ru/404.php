<?php get_header() ?>

<section>
    <div class="cont">
        <div class="error-404">
            <div class="error-404-content">
                <p>404</p>
                <span>ошибка</span>
            </div>
            <p class="error404-desc">
            Oops! Страница была удалена или её не существует. Попробуй начать главной страницы.
            </p>
            <a href="<?php echo site_url() ?>" class="btn">На главную</a>
        </div>
    </div>
</section>

<?php get_footer() ?>