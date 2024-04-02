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

defined('ABSPATH') || exit;

global $product;

// Ensure visibility.
if (empty($product) || !$product->is_visible()) {
	return;
}



$product_id =  $product->get_id();
?>

<article class="card-content">
	<?php
	/**
	 * если есть скидка - выведем её на экран
	 */
	if ($product->is_on_sale()) {
		$available_variations = $product->get_available_variations();
		$maximumper = 0;
		for ($i = 0; $i < count($available_variations); ++$i) {
			$variation_id = $available_variations[$i]['variation_id'];
			$variable_product1 = new WC_Product_Variation($variation_id);
			$regular_price = $variable_product1->get_regular_price();
			$sales_price = $variable_product1->get_sale_price();
			$percentage = round(((($regular_price - $sales_price) / $regular_price) * 100), 1);
			if ($percentage > $maximumper) {
				$maximumper = round($percentage);
			}
		}
		echo '<div class="offer_perc">-' . $maximumper . '%</div>';
	}
	?>

	<?php
	$wl = new Soo_Wishlist_List();
	if ($wl->in_wishlist($product) !== false) {
	?>
		<a href="/wishlist/" class="wishlist_card laz-wishlist add-to-wishlist-button added">
			<!-- <svg width="27" height="23" viewBox="0 0 27 23" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path fill-rule="evenodd" clip-rule="evenodd" d="M13.5691 22.3828C14.0871 22.1086 26.25 15.4688 26.25 7.26562C26.2478 5.33933 25.4816 3.49255 24.1196 2.13045C22.7575 0.768352 20.9107 0.00217118 18.9844 0C17.5799 0 16.2769 0.350511 15.1725 0.988532C14.374 1.44985 13.6792 2.06148 13.125 2.79961C12.5708 2.06148 11.876 1.44985 11.0775 0.988532C9.97307 0.350511 8.67007 0 7.26562 0C5.33933 0.00217118 3.49255 0.768352 2.13045 2.13045C0.768352 3.49255 0.00217118 5.33933 0 7.26562C0 15.4688 12.1629 22.1086 12.6809 22.3828C12.8174 22.4563 12.97 22.4947 13.125 22.4947C13.28 22.4947 13.4326 22.4563 13.5691 22.3828Z" fill="#003996" />
			</svg> -->

		</a>
	<?php
	} else {
	?>
		<a href="?add_to_wishlist=<?= $product_id; ?>" data-product_id="<?= $product_id; ?>" data-product_type="<?= $product->get_type(); ?>" class="wishlist_card laz-wishlist add-to-wishlist-button add-to-wishlist-<?= $product_id; ?>">
			<!-- <svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path fill-rule="evenodd" clip-rule="evenodd" d="M14.4441 23.1328C14.9621 22.8586 27.125 16.2188 27.125 8.01562C27.1228 6.08933 26.3566 4.24255 24.9946 2.88045C23.6325 1.51835 21.7857 0.752171 19.8594 0.75C18.4549 0.75 17.1519 1.10051 16.0475 1.73853C15.249 2.19985 14.5542 2.81148 14 3.54961C13.4458 2.81148 12.751 2.19985 11.9525 1.73853C10.8481 1.10051 9.54507 0.75 8.14062 0.75C6.21433 0.752171 4.36755 1.51835 3.00545 2.88045C1.64335 4.24255 0.877171 6.08933 0.875 8.01562C0.875 16.2188 13.0379 22.8586 13.5559 23.1328C13.6924 23.2063 13.845 23.2447 14 23.2447C14.155 23.2447 14.3076 23.2063 14.4441 23.1328ZM3.60227 8.01716C3.60404 6.81367 4.0829 5.65995 4.93392 4.80892C5.78495 3.9579 6.93867 3.47904 8.14216 3.47727C9.76436 3.4777 11.0492 4.16183 11.8191 5.18714L14 8.09184L16.1809 5.18714C16.9508 4.16183 18.2356 3.4777 19.8578 3.47727C21.0613 3.47904 22.2151 3.9579 23.0661 4.80892C23.917 5.65984 24.3958 6.81337 24.3977 8.01669C24.3972 10.8418 22.2076 13.8462 19.182 16.4958C17.167 18.2605 15.1115 19.5805 14 20.2452C12.8885 19.5805 10.833 18.2605 8.81799 16.4958C5.79259 13.8463 3.60306 10.8421 3.60227 8.01716Z" fill="#003996" />
			</svg> -->
		</a>
	<?php
	}
	?>

	<a class="productThumb-link" href="<?= get_the_permalink($product->get_id()); ?>">
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
		<div class="product-hover" <?php //echo wp_get_attachment_url( $post_thumbnail_id ); 
									?>>
			<img class="product-mainThumb" src="<?php echo wp_get_attachment_url($post_thumbnail_id); ?>" alt="">

			<?php $attachment_ids = $product->get_gallery_image_ids(); ?>

			<?php
			$i = 0;
			foreach ($attachment_ids as $attachment_id) {
				if ($i === 0) {
					// первая итерация
			?>
					<img src="<?php echo wp_get_attachment_url($attachment_id) ?>" alt="" class="product-galleryThumb">
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