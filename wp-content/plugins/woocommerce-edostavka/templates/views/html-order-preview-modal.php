<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<script type="text/template" id="tmpl-wc-edostavka-modal-view-order">
	<div class="wc-backbone-modal wc-edostavka-order-preview">
		<div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
				<header class="wc-backbone-modal-header">
					<mark class="order-status status-cdek-{{ data.status }}"><span>{{ data.status_name }}</span></mark>
					<h1><?php echo esc_html( sprintf( __( 'Order #%s', 'woocommerce' ), '{{ data.order_number }}' ) ); ?></h1>
					<button class="modal-close modal-close-link dashicons dashicons-no-alt">
						<span class="screen-reader-text"><?php esc_html_e( 'Close modal panel', 'woocommerce' ); ?></span>
					</button>
				</header>
				<article>
					<?php do_action( 'wc_edostavka_before_order_preview_table' ); ?>
					<table class="wp-list-table widefat striped table-view-list">
						<tbody>
							<tr>
								<td><strong>Имя покупателя:</strong></td>
								<td>{{ data.customer_name }}</td>
							</tr>
							<tr>
								<td><strong>Email покупателя:</strong></td>
								<td>{{ data.data.billing.email }}</td>
							</tr>
							<tr>
								<td><strong>Телефон покупателя:</strong></td>
								<td>{{ data.data.billing.phone }}</td>
							</tr>
							<tr>
								<td><strong>Адрес доставки:</strong></td>
								<td>{{ data.address }}</td>
							</tr>
							<tr>
								<td><strong>Метод оплаты:</strong></td>
								<td>{{ data.payment_via }}</td>
							</tr>
							<tr>
								<td><strong>Сумма к оплете:</strong></td>
								<td>{{ data.payment_cost }}</td>
							</tr>
							<tr>
								<td><strong>Статус Woocommerce:</strong></td>
								<td>{{ data.wc_status }}</td>
							</tr>
							<tr>
								<td><strong>Тариф доставки:</strong></td>
								<td>{{ data.shipping_data[0].tariff_data.name }}</td>
							</tr>
							<tr>
								<td><strong>Тип доставки:</strong></td>
								<td>{{ data.shipping_data[0].tariff_data.type_name }}</td>
							</tr>
							<# if ( data.cdek_number ) { #>
							<tr>
								<td><strong>Номер отслеживания:</strong></td>
								<td><a href="https://www.cdek.ru/ru/tracking?order_id={{ data.cdek_number }}" target="_blank">{{ data.cdek_number }}</a></td>
							</tr>
							<# } #>
							<# if ( data.data.customer_note ) { #>
							<tr>
								<td><strong>Примечание покпателя:</strong></td>
								<td>{{ data.data.customer_note }}</td>
							</tr>
							<# } #>
						</tbody>
					</table>
					<?php do_action( 'wc_edostavka_after_order_preview_table' ); ?>
				</article>
			</section>
		</div>
	</div>
	<div class="wc-backbone-modal-backdrop modal-close"></div>
</script>
