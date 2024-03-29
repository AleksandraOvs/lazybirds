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
	<img width=80 src='<?php echo $iheart ?>'>
	<p><?php esc_html_e( 'Your wishlist is currently empty.', 'soow' ) ?></p>
</div>

<?php if ( soow_is_wishlist() ) : ?>
	<p class="return-to-shop">
		<a class="button" href="<?php echo esc_url( apply_filters( 'soo_wishlist_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
			<?php esc_html_e( 'Return To Shop', 'soow' ) ?>
		</a>
	</p>
<?php endif; ?>

<?php do_action( 'soo_wishlist_after_list', $list ); ?>
