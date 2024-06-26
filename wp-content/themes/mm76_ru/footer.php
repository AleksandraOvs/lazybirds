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
	<svg width="22" height="19" viewBox="0 0 22 19" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M4.7681 6.6051C7.7965 8.72006 9.8954 12.0738 12.8938 14.2794C13.3736 14.6419 14.333 14.1283 13.8833 13.524C12.594 11.8321 10.705 10.412 9.08582 9.0524C8.00639 8.14599 6.44721 6.24254 4.91802 6.30297C4.73812 6.27275 4.61818 6.48425 4.7681 6.6051Z" fill="#FF81C9"/>
<path d="M6.55347 13.1102C8.58865 11.378 10.4742 9.40261 12.5393 7.63997C13.497 6.81943 14.4248 5.99889 15.3826 5.17835C16.3403 4.3882 17.4178 3.78039 18.3456 2.99024C18.7346 2.65594 18.3156 2.01774 17.8667 2.10892C15.622 2.4736 13.497 4.96561 11.8509 6.45474C9.78582 8.30856 7.45135 10.3751 5.95489 12.7456C5.71546 13.1406 6.31404 13.323 6.55347 13.1102Z" fill="#FF81C9"/>
<path d="M11.2182 0.333301C8.05522 -0.328537 3.78817 1.56672 1.87844 4.18399C0.774377 5.68817 0.625187 7.52327 0.953422 9.32828C1.28166 11.043 1.75907 13.2391 2.95266 14.5628C4.05672 15.7662 5.81727 16.9394 7.36892 17.3907C8.26411 17.6614 9.21897 17.6614 10.144 17.7818C11.1884 17.9021 12.2626 18.1127 13.3368 17.9623C16.4401 17.5411 19.0063 13.9311 19.6628 11.043C21.2443 4.00349 14.2917 -1.95305 7.63747 0.604051C4.47448 1.80739 1.25181 5.05641 1.63973 8.63635C2.05748 12.4269 3.63897 15.5857 7.18987 17.3004C10.89 19.1054 15.6345 18.4436 18.5886 15.4954C21.5427 12.5472 21.9903 8.06477 19.9314 4.48483C19.6628 4.03357 18.887 4.45474 19.1555 4.93608C22.796 11.2837 16.8579 18.8046 9.90528 17.2703C6.86165 16.6085 4.08657 14.3222 3.19139 11.2837C2.80347 9.92995 2.3857 7.91435 2.80346 6.53051C3.28089 4.96616 4.89224 3.49207 6.17534 2.55948C11.1287 -1.17088 18.2603 2.10823 18.7975 8.30544C19.0362 11.1634 17.7531 13.7806 15.6941 15.7361C14.232 17.1199 12.7699 17.2102 10.89 16.9695C8.98025 16.7288 7.33908 16.6386 5.66806 15.5556C4.17609 14.5929 3.22121 13.6603 2.65426 11.9455C2.08731 10.2007 1.46069 8.27535 1.87844 6.41017C2.68411 3.01073 7.84634 0.423552 11.069 0.934972C11.4868 0.965056 11.636 0.423552 11.2182 0.333301Z" fill="#FF81C9"/>
</svg>

    </div>
    <div class="rb-title">Таблица размеров</div>
    <div class="rb-content">

		<div class="size-table">
			<ul class="size-table_head">
				<li class="table-head_item"><p>Размер</p></li>
				<li class="table-head_item"><p>Обхват груди</p></li>
				<li class="table-head_item"><p>Обхват талии</p></li>
				<li class="table-head_item"><p>Обхват бёдер</p></li>
			</ul>

			<ul class="size-table_s">
				<li class="table-item-s"><p>S (42-44)</p></li>
				<li class="table-item-s"><p>86-90</p></li>
				<li class="table-item-s"><p>62-70</p></li>
				<li class="table-item-s"><p>90-98</p></li>
			</ul>

			<ul class="size-table_m">
				<li class="table-item-m"><p>M (44-46)</p></li>
				<li class="table-item-m"><p>90-96</p></li>
				<li class="table-item-m"><p>70-76</p></li>
				<li class="table-item-m"><p>98-105</p></li>
			</ul>

			<ul class="size-table_l">
				<li class="table-item-l"><p>L (46-48)</p></li>
				<li class="table-item-l"><p>96-106</p></li>
				<li class="table-item-l"><p>76-82</p></li>
				<li class="table-item-l"><p>105-115</p></li>
			</ul>
		<p class="size-table_desc">Наши пижамы и костюмы свободного кроя, поэтому мы рекомендуем выбирать твой привычный размер.</p>
		</div>





        
    </div>
</div>

<script>
    $ajax = '<?php echo admin_url( "admin-ajax.php" ) ?>';
</script>

<div class="overlay"></div>

<?php wp_footer(); ?>
