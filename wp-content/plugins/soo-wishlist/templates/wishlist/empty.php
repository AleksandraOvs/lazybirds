<?php
/**
* Template for displaying wishlist when it is empty
*
* This template can be overridden by copying it to yourtheme/woocommerce/wishlist/empty.php.
*
* @author        SooPlugins
* @package       Soo Wishlist/Templates
* @version       1.0.0
*/
$iheart   =  get_stylesheet_directory_uri() . '/assets/img/svg/heart.svg';   /*Временая хрень пока не найду хук */
?>

<?php do_action( 'soo_wishlist_before_list', $list ); ?>

<div class="wishlist-empty">
	
<svg width="52" height="48" viewBox="0 0 52 48" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M24.2266 46.7502C20.7448 42.128 15.756 38.96 12.0664 34.4936C8.37678 29.9752 5.62256 24.7298 4.06356 19.0689C2.86833 14.7583 0.166065 1.41107 8.63661 3.59233C14.8206 5.20231 18.978 12.1097 19.9134 18.0302C20.1212 19.3286 22.1999 19.4325 22.5117 18.1341C24.1746 11.6942 29.0075 6.39682 35.1915 3.90394C38.1536 2.70944 41.5834 2.03429 44.8053 2.55364C51.1452 3.5404 48.5469 10.9671 46.4683 14.7583C42.8306 21.3021 38.1016 27.2746 32.6971 32.4681C28.5398 36.4152 24.1226 40.6738 23.603 46.6463C23.4471 48.2044 26.0454 48.516 26.2013 47.0618C26.9808 38.4926 36.1789 33.351 41.0637 27.0669C43.8699 23.4834 46.5722 19.6921 48.8067 15.7451C50.7295 12.3174 52.2885 8.16261 50.9374 4.2675C48.7028 -2.32823 37.9977 0.164629 33.2687 2.24202C26.7729 5.15038 21.7322 10.8113 19.9134 17.7706C20.7968 17.8225 21.6283 17.8744 22.5117 17.8744C21.4723 11.3306 17.4709 5.20231 11.3908 2.29396C5.20683 -0.614391 0.477865 3.28073 0.321965 9.66872C0.166065 16.7838 2.81637 24.2624 6.45403 30.2868C8.27285 33.2991 10.4554 36.1036 12.9498 38.5445C16.1718 41.6606 20.0173 44.1015 22.9274 47.5292C23.499 48.1005 24.7462 47.4773 24.2266 46.7502Z" fill="#E9CEDB"/>
</svg>


	<p>Твой список желаний пока пуст<?php //esc_html_e( 'Your wishlist is currently empty.', 'soow' ) ?></p>
</div>

<?php if ( soow_is_wishlist() ) : ?>
	<p class="return-to-shop">
		<a class="button" href="<?php echo esc_url( apply_filters( 'soo_wishlist_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
		За покупками
			<?php //esc_html_e( 'Return To Shop', 'soow' ) ?>
		</a>
	</p>
<?php endif; ?>

<?php do_action( 'soo_wishlist_after_list', $list ); ?>
