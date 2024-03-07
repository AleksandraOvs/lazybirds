<?php
/**
 * The template for displaying product category thumbnails within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product-cat.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$image_id = get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true );
?>
<a href="<?= get_term_link($category, 'product_cat'); ?>" class="card-collection">
	<picture>
		<?= wp_get_attachment_image( $image_id, 'full' ); ?>
	</picture>
	<div class="btn-abs">
		<div class="btn"><?= $category->name; ?></div>
	</div>
</a>