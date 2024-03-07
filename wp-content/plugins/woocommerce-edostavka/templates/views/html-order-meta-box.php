<?php

/** @var WC_Edostavka_Shipping_Order $order */
/** @var string $actions */


$history_statuses = $order->get_order_meta( 'history_statuses' );
$latest_update_time = $order->get_order_meta( 'latest_order_update_time' );
$format_date = wp_date( wc_date_format(), $latest_update_time, new DateTimeZone( wc_timezone_string() ) );
$format_time = wp_date( wc_time_format(), $latest_update_time, new DateTimeZone( wc_timezone_string() ) );
?>

<style>
	.wc-edostavka-order-table tr td {
		vertical-align: middle;
	}

	.wc-edostavka-order-table__status-history {
		margin: 0;
		list-style: circle;
	}

	.wc-edostavka-order-actions {
		margin-left: -12px;
		margin-right: -12px;
		margin-top: 6px;
		padding: 12px 12px 0;
		border-top: 1px solid #c3c4c7;
	}

	.wc-edostavka-order-actions .wc-action-button {
		text-indent: 9999px;
		margin: 2px 0 2px 4px;
		position: relative;
		display: inline-block;
		padding: 0;
		height: 2em;
		width: 2em;
		overflow: hidden;
		vertical-align: middle;
	}

	.wc-edostavka-order-actions .wc-action-button::after {
		font-family: Dashicons;
		speak: never;
		font-weight: 400;
		font-variant: normal;
		text-transform: none;
		margin: 2px 0 0;
		text-indent: 0;
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		text-align: center;
		line-height: 1.85;
	}

	.wc-edostavka-order-actions .wc-action-button.edostavka-export {
		text-indent:0;
		width: 100%;
		padding: 0 15px;
		margin: 0;
		text-align: center;
	}

	.wc-edostavka-order-actions .wc-action-button.edostavka-export::after {
		content: "";
	}

	.wc-edostavka-order-actions .wc-action-button.edostavka-update::after {
		content: "\f463";
	}

	.wc-edostavka-order-actions .wc-action-button.edostavka-waybill::after,
	.wc-edostavka-order-actions .wc-action-button.edostavka-waybill-downloaded::after {
		content: "\f121";
	}

	.wc-edostavka-order-actions .wc-action-button.edostavka-barcode::after,
	.wc-edostavka-order-actions .wc-action-button.edostavka-barcode-downloaded::after {
		content: "\f193";
	}

	.wc-edostavka-order-actions .wc-action-button.edostavka-waybill-downloaded,
	.wc-edostavka-order-actions .wc-action-button.edostavka-barcode-downloaded {
		border-color: var(--wc-highlight);
	}

	.wc-edostavka-order-actions .wc-action-button.edostavka-waybill-downloaded::before,
	.wc-edostavka-order-actions .wc-action-button.edostavka-barcode-downloaded::before {
		position: absolute;
		content: "";
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background-color: var(--wc-highlight);
		top: -6px;
		right: -6px;
	}

	.wc-edostavka-order-actions .wc-action-button.edostavka-cancel {
		background-color: var(--wc-orange);
	}

	.wc-edostavka-order-actions .wc-action-button.edostavka-cancel::after {
		content: "\f14f";
	}
</style>

<?php if( ! $order->is_exported() ) : ?>

<?php _e( 'This order is not exported to CDEK yet.', 'woocommerce-edostavka' ); ?>

<?php else : ?>

<table class="wc-edostavka-order-table widefat fixed striped">
	<tr class="wc-edostavka-order-table__row wc-edostavka-order-table__row--status">
		<td><?php _e( 'Status:', 'woocommerce-edostavka' ); ?></td>
		<td>
			<mark class="order-status status-cdek-<?php echo strtolower( $order->get_order_status() ); ?>">
				<span><?php echo $order->get_order_status_name();?></span>
			</mark>
		</td>
	</tr>
	<?php if( $tracking_code = wc_edostavka_get_tracking_code( $order ) ) : ?>
	<tr class="wc-edostavka-order-table__row wc-edostavka-order-table__row--tracking">
		<td><?php _e( 'Tracking code:', 'woocommerce-edostavka' ); ?></td>
		<td>
			<a href="<?php echo esc_url( add_query_arg( 'order_id', $tracking_code, 'https://cdek.ru/ru/tracking' ) );?>" target="_blank">
				<?php echo $tracking_code;?>
			</a>
		</td>
	</tr>
	<?php endif;?>
	<tr class="wc-edostavka-order-table__row wc-edostavka-order-table__row--update">
		<td><?php _e( 'Latest update:', 'woocommerce-edostavka' ); ?></td>
		<td>
			<time datetime="<?php echo esc_attr( date( 'c', $latest_update_time ) );?>" title="<?php echo esc_html( $format_date . ' ' . $format_time );?>">
				<?php printf( __( '%s at %s', 'woocommerce-edostavka' ), $format_date, $format_time ); ?>
			</time>
		</td>
	</tr>
	<?php if( $history_statuses ) : ?>
	<tr class="wc-edostavka-order-table__row wc-edostavka-order-table__row--history">
		<td><?php _e( 'Status history:', 'woocommerce-edostavka' ); ?></td>
		<td>
			<ul class="wc-edostavka-order-table__status-history">
				<?php foreach ( $history_statuses as $history_status ) : ?>
					<li>
						<span class="datetime"><?php echo wp_date( wc_date_format(), strtotime( $history_status->date_time ), new DateTimeZone( wc_timezone_string() ) ); ?></span>
						&ndash;
						<span class="status-name"><?php echo esc_html( $history_status->name );?></span>
					</li>
				<?php endforeach;?>
			</ul>
		</td>
	</tr>
	<?php endif;?>
</table>

<?php endif;?>

<div class="wc-edostavka-order-actions">
	<?php echo $actions;?>
</div>
