</main>

<footer>
    <div class="cont footer-inner">
		<?php
	    	wp_nav_menu(
	        	array(
			    	'theme_location' => 'menu-footer',
			    	'container' => 'nav',
			    	'menu_class' => 'footer__menu',
		    	)
        	);
    	?>

		<div class="footer-inner__block">
			<a href="<?php site_url(); ?>" class="logo_footer">
  				<?php
  					$footer_logo = get_theme_mod('footer_logo');
  					$img = wp_get_attachment_image_src($footer_logo, 'full');
  					if ($img) :
    			?>
    				<img src="<?php echo $img[0]; ?>" alt="">
  				<?php endif; ?>
	    	</a>

			<?php if ( is_active_sidebar( 'footer-contacts-widget' ) ){ ?>    
			<div class="footer-contacts__widget">	
				<div class="footer-desc">
					<span>Стань частью нашего сообщества!</span>
				</div>
				<?php dynamic_sidebar( 'footer-contacts-widget' ); ?>
			</div>
			<?php } ?> 
		</div>
       
		<?php if ( is_active_sidebar( 'footer-docs-widget' ) ){ ?>    
			<div class="footer-docs__widget">
				<div class="footer-docs__widget__info">
					<p>ИП&nbsp;Артемьева Юлия Андреевна</p>
					<p>ОГРН&nbsp;320774600326343 / ИНН&nbsp;502982626269</p>
				</div>	
				<?php dynamic_sidebar( 'footer-docs-widget' ); ?>
			</div>
		<?php } ?>
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

<script>
    $ajax = '<?php echo admin_url( "admin-ajax.php" ) ?>';
</script>

<div class="overlay"></div>

<?php wp_footer(); ?>
