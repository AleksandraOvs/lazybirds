<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
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

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>

	<?php
	/**
	 * Hook: woocommerce_before_single_product_summary.
	 *
	 * @hooked woocommerce_show_product_sale_flash - 10
	 * @hooked woocommerce_show_product_images - 20
	 */
	do_action( 'woocommerce_before_single_product_summary' );
	?>

	<div class="summary entry-summary">

		<?php
		/**
		 * Hook: woocommerce_single_product_summary.
		 *
		 * @hooked woocommerce_template_single_title - 5
		 * @hooked woocommerce_template_single_rating - 10
		 * @hooked woocommerce_template_single_price - 10
		 * @hooked woocommerce_template_single_excerpt - 20
		 * @hooked woocommerce_template_single_add_to_cart - 30
		 * @hooked woocommerce_template_single_meta - 40
		 * @hooked woocommerce_template_single_sharing - 50
		 * @hooked WC_Structured_Data::generate_product_data() - 60
		 */
		do_action( 'woocommerce_single_product_summary' );
		?>

		<?php get_template_part('template-parts/product/info'); ?>

		<?php
                    $tabs = get_field('product_tabs');
                    if( $tabs ) {  
                    ?>
                    <div id="woocommerce-tabs">
                    <?php
                        foreach($tabs as $tab) {                       
                           ?>

                           <div class="tab-head" class="">
                            <h3><?php echo $tab['product-tab-heading'] ?></h3>
                            <div class="tab-head__button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50" fill="none">
                                    <path d="M20.6547 14.9061C21.7312 20.9676 20.2524 27.3936 21.1867 33.5265C21.3248 34.5192 23.0609 35.0447 23.243 33.8025C23.7176 30.289 23.1649 26.3892 22.859 22.8787C22.6551 20.5384 23.0609 16.4577 21.1875 14.7267C21.0111 14.4791 20.6205 14.587 20.6547 14.9061Z" fill="#003996"/>
                                    <path d="M15.0933 24.6776C19.5333 25.0346 24.0834 24.9287 28.5945 25.2851C30.6902 25.4468 32.7506 25.5732 34.8463 25.7349C36.9062 25.9324 38.8924 26.4859 40.917 26.6481C41.7695 26.7127 42.0278 25.4668 41.3913 25.0451C38.3161 22.8295 32.8749 23.2621 29.18 23.0771C24.5615 22.8281 19.3749 22.5123 14.8177 23.5423C14.0699 23.7258 14.5604 24.6461 15.0933 24.6776Z" fill="#003996"/>
                                    <path d="M35.648 15.1168C32.7003 10.6092 25.438 7.81402 20.1029 8.64787C17.029 9.11941 14.6905 11.1063 12.9501 13.6203C11.3161 16.028 9.29061 19.1788 9.13729 22.1454C9.02029 24.8647 9.71241 28.3222 11.0093 30.6827C11.7452 32.0568 12.8705 33.1821 13.8188 34.414C14.9078 35.7867 15.9256 37.3008 17.3689 38.3895C21.5225 41.5505 28.8013 40.3203 32.9785 37.6904C43.1385 31.258 41.9646 16.0444 31.109 11.2159C25.9632 8.90648 18.3362 8.93754 14.5744 13.6137C10.5995 18.5732 8.74068 24.1597 10.9046 30.3653C13.138 36.8532 19.5094 41.6646 26.4653 41.6716C33.4213 41.6786 39.2314 36.9234 41.0239 30.278C41.2392 29.4297 39.8286 29.0117 39.5778 29.8955C36.3873 41.6665 20.5258 43.5319 14.1402 33.53C11.3332 29.1631 10.7572 23.1981 13.2831 18.5623C14.4214 16.5097 16.3044 13.642 18.4276 12.5034C20.8339 11.2225 24.4701 11.3842 27.0813 11.7973C37.3152 13.2386 41.8554 25.5078 35.185 33.4443C32.0982 37.0937 27.5015 38.6661 22.7706 38.5441C19.4166 38.4518 17.5871 36.835 15.6552 34.3359C13.6882 31.8017 11.8604 29.7612 11.1675 26.5155C10.5437 23.6227 10.5174 21.3983 11.8701 18.7093C13.2583 15.9848 14.7888 12.9773 17.4793 11.2714C22.4351 8.21465 31.5678 11.2494 34.7631 15.6501C35.2199 16.1778 36.0339 15.7155 35.648 15.1168Z" fill="#003996"/>
                                </svg>
                            </div>
                            </div>
                           
                            <div class="tab-content">
                                <p><?php echo $tab['product-tab-content'] ?></p>
                            </div>
                           
                            <?php
                        }
                    ?>
                    </div>
                    <?php
                        
                    }    
                ?>
<?php
	/**
	 * Hook: woocommerce_after_single_product_summary.
	 *
	 * @hooked woocommerce_output_product_data_tabs - 10
	 * @hooked woocommerce_upsell_display - 15
	 * @hooked woocommerce_output_related_products - 20
	 */
	do_action( 'woocommerce_after_single_product_summary' );
	?>
	
	
	</div>
	
</div>

<section class="section-featured">
<?php
$args = array(
	'post_type' => 'product',
	'tax_query' => array(
		array(
			'taxonomy' => 'product_visibility',
			'field'    => 'name',
			'terms'    => 'featured',
		),
	),
);
$featured = new WP_Query($args);
//print_r($featured);
?>


		<?php
		if ($featured->have_posts()) {
			?>
			<div class="featured-desc">
				<span>Интересное для тебя</span>
			</div>
<div class="swiper swiperFeatured">
	<div class="swiper-wrapper">
			<?php
			while ($featured->have_posts()) {
				$featured->the_post();
				?>
			
<div class="swiper-slide swiperFeatures-slide">
	<a href="<?php echo the_permalink() ?>" class="swiperFeatures-slide-link">
		<?php //the_title() ?>
		<?php
		global $product;
		$post_thumbnail_id = $product->get_image_id();
		if ( $post_thumbnail_id ) {
			//$html = wc_get_gallery_image_html( $post_thumbnail_id, true );
			?>
			
			
				<?php echo wp_get_attachment_image( $post_thumbnail_id, 'full' ); ?>
			
			
			<?php
		}
		?>
	</a>
</div>
<?php 
			}
?>

<?php
		}

		// Возвращаем оригинальные данные поста. Сбрасываем $post.
		wp_reset_postdata();
		?>
	
	</section>

<?php do_action( 'woocommerce_after_single_product' ); ?>
