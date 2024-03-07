<?php
/**
 * Single Product Thumbnails
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-thumbnails.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.5.1
 */

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $product;

$attachment_ids = $product->get_gallery_image_ids();
$post_thumbnail_id = $product->get_image_id();

echo '<div thumbsSlider="" class="gallery-thumbs_mm76 swiper swiperGallery_1"><div class="swiper-wrapper">';
if ( $attachment_ids && $product->get_image_id() ) {

	if ( $post_thumbnail_id ) {
		//$html = wc_get_gallery_image_html( $post_thumbnail_id, true );
		?>
		<a href="<?= wp_get_attachment_image_url( $post_thumbnail_id, 'full' ); ?>" data-fancybox="gallery-product" class="swiper-slide">
			<?php echo wp_get_attachment_image( $post_thumbnail_id, 'large' ); ?>
		</a>
		<?php
	}

	foreach ( $attachment_ids as $attachment_id ) {
		//echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', wc_get_gallery_image_html( $attachment_id ), $attachment_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
		?>
			<div class="swiper-slide">
				<?php echo wp_get_attachment_image( $attachment_id, 'large' ); ?>
			</div>
		<?php
	}
}
echo '</div></div>';