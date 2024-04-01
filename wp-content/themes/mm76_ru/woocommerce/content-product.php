<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}



$product_id =  $product->get_id();
?>

<article class="card-content">
	<?php
		/**
		 * если есть скидка - выведем её на экран
		 */
		if ($product->is_on_sale()  ) {
			$available_variations = $product->get_available_variations();
			$maximumper = 0;
			for ($i = 0; $i < count($available_variations); ++$i) {
				$variation_id=$available_variations[$i]['variation_id'];
				$variable_product1= new WC_Product_Variation( $variation_id );
				$regular_price = $variable_product1 ->get_regular_price();
				$sales_price = $variable_product1 ->get_sale_price();
				$percentage= round((( ( $regular_price - $sales_price ) / $regular_price ) * 100),1) ;
				if ($percentage > $maximumper) {
					$maximumper = round($percentage);
				}
			}
			echo '<div class="offer_perc">-'. $maximumper . '%</div>';
		}
	?>

		<?php
		$wl = new Soo_Wishlist_List();
		if( $wl->in_wishlist( $product ) !== false ) {
			?>
			<a href="/wishlist/" class="wishlist_card laz-wishlist add-to-wishlist-button">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M12.012 5.572L10.925 4.485C10.4155 3.96866 9.80897 3.5582 9.14022 3.27725C8.47146 2.9963 7.75374 2.85041 7.02837 2.84798C6.30301 2.84555 5.58432 2.98663 4.9137 3.26309C4.24308 3.53955 3.63378 3.94594 3.12086 4.45886C2.60795 4.97177 2.20156 5.58108 1.92509 6.2517C1.64863 6.92232 1.50755 7.64101 1.50998 8.36637C1.51241 9.09174 1.6583 9.80946 1.93925 10.4782C2.2202 11.147 2.63067 11.7535 3.14701 12.263L11.986 21.102L11.988 21.1L12.014 21.126L20.853 12.287C21.3693 11.7775 21.7798 11.171 22.0608 10.5022C22.3417 9.83346 22.4876 9.11574 22.49 8.39037C22.4925 7.66501 22.3514 6.94632 22.0749 6.2757C21.7985 5.60508 21.3921 4.99577 20.8792 4.48286C20.3662 3.96994 19.7569 3.56355 19.0863 3.28709C18.4157 3.01063 17.697 2.86955 16.9716 2.87198C16.2463 2.87441 15.5285 3.0203 14.8598 3.30125C14.191 3.5822 13.5845 3.99266 13.075 4.509L12.012 5.572ZM11.988 18.272L16.924 13.335L18.374 11.935H18.376L19.439 10.873C20.0954 10.2166 20.4642 9.32631 20.4642 8.398C20.4642 7.4697 20.0954 6.57941 19.439 5.923C18.7826 5.26659 17.8923 4.89783 16.964 4.89783C16.0357 4.89783 15.1454 5.26659 14.489 5.923L12.013 8.4L12.006 8.393L9.51101 5.9C8.8546 5.24359 7.96431 4.87483 7.03601 4.87483C6.1077 4.87483 5.21742 5.24359 4.56101 5.9C3.9046 6.55641 3.53583 7.4467 3.53583 8.375C3.53583 9.30331 3.9046 10.1936 4.56101 10.85L7.10101 13.39L7.10201 13.387L11.988 18.273V18.272Z" fill="white"/>
					<path d="M2 8.5C5.5 0.5 10.1667 5.33333 12 7.5C16.4 1.5 20.1667 4.66667 21.5 7C21.9 9.8 15.3333 16.8333 12 20C4.4 12 1.83333 9.5 2 8.5Z" fill="white"/>
				</svg>
			</a>
			<?php
		} else {
			?>
			<a href="?add_to_wishlist=<?= $product_id; ?>" data-product_id="<?= $product_id; ?>" data-product_type="<?= $product->get_type(); ?>" class="wishlist_card laz-wishlist add-to-wishlist-button add-to-wishlist-<?= $product_id; ?>">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M12.012 5.572L10.925 4.485C10.4155 3.96866 9.80897 3.5582 9.14022 3.27725C8.47146 2.9963 7.75374 2.85041 7.02837 2.84798C6.30301 2.84555 5.58432 2.98663 4.9137 3.26309C4.24308 3.53955 3.63378 3.94594 3.12086 4.45886C2.60795 4.97177 2.20156 5.58108 1.92509 6.2517C1.64863 6.92232 1.50755 7.64101 1.50998 8.36637C1.51241 9.09174 1.6583 9.80946 1.93925 10.4782C2.2202 11.147 2.63067 11.7535 3.14701 12.263L11.986 21.102L11.988 21.1L12.014 21.126L20.853 12.287C21.3693 11.7775 21.7798 11.171 22.0608 10.5022C22.3417 9.83346 22.4876 9.11574 22.49 8.39037C22.4925 7.66501 22.3514 6.94632 22.0749 6.2757C21.7985 5.60508 21.3921 4.99577 20.8792 4.48286C20.3662 3.96994 19.7569 3.56355 19.0863 3.28709C18.4157 3.01063 17.697 2.86955 16.9716 2.87198C16.2463 2.87441 15.5285 3.0203 14.8598 3.30125C14.191 3.5822 13.5845 3.99266 13.075 4.509L12.012 5.572ZM11.988 18.272L16.924 13.335L18.374 11.935H18.376L19.439 10.873C20.0954 10.2166 20.4642 9.32631 20.4642 8.398C20.4642 7.4697 20.0954 6.57941 19.439 5.923C18.7826 5.26659 17.8923 4.89783 16.964 4.89783C16.0357 4.89783 15.1454 5.26659 14.489 5.923L12.013 8.4L12.006 8.393L9.51101 5.9C8.8546 5.24359 7.96431 4.87483 7.03601 4.87483C6.1077 4.87483 5.21742 5.24359 4.56101 5.9C3.9046 6.55641 3.53583 7.4467 3.53583 8.375C3.53583 9.30331 3.9046 10.1936 4.56101 10.85L7.10101 13.39L7.10201 13.387L11.988 18.273V18.272Z" fill="white"/>
				</svg>
			</a>
			<?php
		}
	?>
	
	<a class="productThumb-link" href="<?= get_the_permalink( $product->get_id() ); ?>">
	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item_title.
	 *
	 * @hooked woocommerce_show_product_loop_sale_flash - 10
	 * @hooked woocommerce_template_loop_product_thumbnail - 10
	 */
	//do_action( 'woocommerce_before_shop_loop_item_title' );
	?>

	<!-- вывести несколько фото товаров -->
	<?php 
	
$post_thumbnail_id = $product->get_image_id();
?>
<div class="product-hover"
	<?php //echo wp_get_attachment_url( $post_thumbnail_id ); ?>>
	<img class="product-mainThumb" src="<?php echo wp_get_attachment_url ( $post_thumbnail_id ); ?>" alt="">

	<?php $attachment_ids = $product->get_gallery_image_ids(); ?>

	<?php 
		$i = 0;
		foreach ($attachment_ids as $attachment_id) {
			if ($i === 0) {	
				// первая итерация
				?>
				<img src="<?php echo wp_get_attachment_url( $attachment_id )?>" alt="" class="product-galleryThumb">
				<?php
			}
			++$i;
			?>
				
			<?php
		}
	?>
</div>
	</a>
<div class="p-title">
	<?= $product->get_title(); ?>
</div>
	
<div class="p-price">
	<?= $product->get_price_html(); ?>
</div>
	
</article>