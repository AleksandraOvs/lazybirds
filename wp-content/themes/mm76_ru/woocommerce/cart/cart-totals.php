<?php
/**
 * Cart totals
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-totals.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see           https://docs.woocommerce.com/document/template-structure/
 * @author        WooThemes
 * @package       WooCommerce/Templates
 * @version       2.3.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="cart_totals <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>


	<!-- <table cellspacing="0" class="shop_table shop_table_responsive order-total-table"> -->
		<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

		<!-- <tr class="order-total">
			<th><?php _e( 'Total', 'konte' ); ?></th>
			<td data-title="<?php esc_attr_e( 'Total', 'konte' ); ?>"><?php //wc_cart_totals_order_total_html(); ?></td>
		</tr> -->
		
	<!-- </table> -->

	<div class="wc-proceed-to-checkout">
		<div class="cashback-text">
			<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>
		</div>
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
	</div>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>
