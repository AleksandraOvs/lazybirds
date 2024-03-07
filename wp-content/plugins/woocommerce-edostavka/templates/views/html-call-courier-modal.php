<?php

if ( ! defined( 'ABSPATH' ) ) exit;

?>

<script type="text/template" id="tmpl-wc-edostavka-modal-call-courier">
	<div class="wc-backbone-modal">
		<div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
				<header class="wc-backbone-modal-header">
					<h1><?php _e( 'Call a courier', 'woocommerce-edostavka' ); ?></h1>
					<button class="modal-close modal-close-link dashicons dashicons-no-alt">
						<span class="screen-reader-text"><?php _e( 'Close modal panel', 'woocommerce-edostavka' ); ?></span>
					</button>
				</header>
				<article class="wc-edostavka-call-courier-content">
					<form action="" method="post">
						<div>
							<p><?php _e( 'Conditions for creating a courier invitation:', 'woocommerce-edostavka' );?></p>
							<ul>
								<li><?php _e( 'a courier cannot be invited to the same address on the same day', 'woocommerce-edostavka' );?></li>
								<li><?php _e( 'the recommended minimum time interval for courier visits is three hours', 'woocommerce-edostavka' );?></li>
							</ul>

							<?php /** @var array $orders */
							if( count( $orders ) > 1 ) : ?>

							<p class="form-row validate-required">
								<label for="orders"><?php _e( 'Orders', 'woocommerce-edostavka' );?></label>
								<span class="woocommerce-input-wrapper">
									<select name="orders[]" id="orders" class="select wc-enhanced-select-nostd" multiple required data-placeholder="<?php esc_html_e( 'Choose orders', 'woocommerce-edostavka' );?>">
										<?php foreach ( $orders as $order_id => $order ) : ?>
										<option value="<?php echo esc_attr( $order_id );?>"><?php echo $order;?></option>
										<?php endforeach; ?>
									</select>
								</span>
							</p>

							<?php else : ?>

							<input type="hidden" name="orders[]" value="<?php echo current( array_keys( $orders ) );?>" >

							<?php endif; ?>
							<?php

							woocommerce_form_field( 'date', array(
								'type'	=> 'date',
								'label'	=> __( 'Date to awaiting', 'woocommerce-edostavka' ),
								'class'	=> array( 'one-third' ),
								'required'	=> true,
								'default'	=> date( 'Y-m-d', current_time( 'timestamp' ) )
							) );

							woocommerce_form_field( 'time_from', array(
								'type'	=> 'time',
								'label'	=> __( 'Time from', 'woocommerce-edostavka' ),
								'class'	=> array( 'one-third' ),
								'required'	=> true
							) );

							woocommerce_form_field( 'time_to', array(
								'type'	=> 'time',
								'label'	=> __( 'Time to', 'woocommerce-edostavka' ),
								'class'	=> array( 'one-third' ),
								'required'	=> true
							) );

							woocommerce_form_field( 'comment', array(
								'type'	=> 'textarea',
								'label'	=> __( 'Comment', 'woocommerce-edostavka' ),
								'required'	=> false
							) );

							?>
						</div>
					</form>
				</article>
				<footer>
					<div class="inner">
						<button id="create-call-courier-order" class="button button-primary button-large"><?php _e( 'Add order to call a courier', 'woocommerce-edostavka' ); ?></button>
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class="wc-backbone-modal-backdrop modal-close"></div>
</script>
