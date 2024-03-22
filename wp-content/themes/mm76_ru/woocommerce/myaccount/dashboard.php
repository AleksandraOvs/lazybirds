<?php

/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

$allowed_html = array(
	'a' => array(
		'href' => array(),
	),
);
?>

<h1 class="title-page my-account"><?php the_title(); ?></h1>
<div class="hello_users_account">
	<?php
	printf(
		/* translators: 1: user display name 2: logout url */
		wp_kses(__('Привет, %1$s ', 'woocommerce'), $allowed_html),
		esc_html($current_user->first_name) . '<p>Рады видеть тебя снова!</p>',
		esc_url(wc_logout_url())
	);
	?>
</div>

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
	<?php //echo $fieldname_sub; ?>
	
	<?php //if( $fieldname_sub ): ?><?php //echo $fieldname_sub[0]; ?><?php //endif; ?>
</div>
<?php 
			}
?>

<?php
		}

		// Возвращаем оригинальные данные поста. Сбрасываем $post.
		wp_reset_postdata();
		?>
	</div>
</div>




<?php
/**
 * My Account dashboard.
 *
 * @since 2.6.0
 */
do_action('woocommerce_account_dashboard');

/**
 * Deprecated woocommerce_before_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action('woocommerce_before_my_account');

/**
 * Deprecated woocommerce_after_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action('woocommerce_after_my_account');

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
