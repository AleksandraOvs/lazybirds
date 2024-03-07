<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php
	if( empty( $cdek_number ) && empty( $statuses ) ) return;
?>

<h2>
	<?php echo $title;?>
</h2>

<table class="woocommerce-table shop_table wc-edostavka-table-point-details">

	<?php if( ! empty( $cdek_number ) ) : ?>
	<caption>
		<span><?php _e( 'Tracking number: ', 'woocommerce-edostavka' );?></span>
		<a href="<?php echo esc_url( add_query_arg( 'order_id', $cdek_number, 'https://www.cdek.ru/ru/tracking' ) );?>" target="_blank"><?php echo $cdek_number;?></a>
	</caption>
	<?php endif;?>
	<?php if( $statuses ) : ?>
	<thead>
		<tr>
			<th><?php _e( 'Status', 'woocommerce-edostavka' );?></th>
			<th><?php _e( 'Date', 'woocommerce-edostavka' );?></th>
			<th><?php _e( 'Location', 'woocommerce-edostavka' );?></th>
		</tr>
	</thead>
		<tbody>
		<?php foreach ( $statuses as $status ) : ?>
			<tr>
				<td scope="row">
					<?php

					$edostavka_statuses = wc_edostavka_get_order_statuses();
					if( in_array( strtoupper( $status->code ), array_keys( $edostavka_statuses ), true ) ) {
						echo $edostavka_statuses[ strtoupper( $status->code ) ];
					} else {
						echo $status->name;
					}

					?></td>
				<td>
					<?php
					$timezone = new DateTimeZone( wc_timezone_string() );
					$timestamp = strtotime( $status->date_time );
					printf( '%s at %s',
						wp_date( wc_date_format(), $timestamp, $timezone ),
						wp_date( wc_time_format(), $timestamp, $timezone )
					);
					?>
				</td>
				<td><?php esc_attr_e( $status->city );?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	<?php endif; ?>
</table>
