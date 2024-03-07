<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<style>
	.wc-edostavka-table-point-details td[scope="row"] {
		font-weight: bold;
	}
	.wc-edostavka-table-point-details .wc-edostavka-table-point-details__working-hours {
		margin-left:0;
		list-style:none;
	}
</style>

<h2><?php echo $title;?></h2>

<table class="woocommerce-table shop_table wc-edostavka-table-point-details">

	<tbody>
	<tr>
		<td scope="row"><?php _e( 'Name:', 'woocommerce-edostavka' );?></td>
		<td><?php printf( '%s [%s]', $point['name'], $point['code'] );?></td>
	</tr>
	<tr>
		<td scope="row"><?php _e( 'Address:', 'woocommerce-edostavka' );?></td>
		<td>
			<?php
				$map_url = add_query_arg( array( 'whatshere' => array( 'point' => sprintf( '%s,%s', $point['location']['longitude'], $point['location']['latitude'] ), 'zoom' => 16 ) ), 'https://yandex.ru/maps/' );
				printf( '<a href="%s" target="_blank" title="%s">%s</a>', esc_url( $map_url ), esc_attr__( 'Show address on the Yandex map', 'woocommerce-edostavka' ), $point['location']['address'] );
			?>
		</td>
	</tr>
	<?php if( isset( $point['address_comment'] ) ) : ?>
		<tr>
			<td scope="row"><?php _e( 'Description of location:', 'woocommerce-edostavka' );?></td>
			<td><?php echo $point['address_comment'];?></td>
		</tr>
	<?php endif;?>
	<?php if( isset( $point['note'] ) && ( isset( $point['address_comment'] ) && $point['note'] !== $point['address_comment'] ) ) : ?>
		<tr>
			<td scope="row"><?php _e( 'Pickup points note:', 'woocommerce-edostavka' );?></td>
			<td><?php echo $point['note'];?></td>
		</tr>
	<?php endif;?>
	<?php if( isset( $point['phones'] ) ) : ?>
		<tr>
			<td scope="row"><?php echo _n( 'Phone:', 'Phones:',count( $point['phones'] ), 'woocommerce-edostavka' );?></td>
			<td>
				<?php
					$phones = array();
					foreach ( $point['phones'] as $phone ) {
						$phones[] = sprintf( '%s%s', $phone['number'], isset( $phone['additional'] ) ? sprintf( ' (%s)', $phone['additional'] ) : '' );
					}
					echo Woodev_Helper::list_array_items( $phones, __( 'and', 'woocommerce-edostavka' ) );
				?>
			</td>
		</tr>
	<?php endif;?>
	<?php if( isset( $point['work_time_list'] ) ) : ?>
		<tr>
			<td scope="row"><?php _e( 'Office schedule:', 'woocommerce-edostavka' );?></td>
			<td>
				<ul class="wc-edostavka-table-point-details__working-hours">
					<?php
					global $wp_locale;

					foreach( ( array ) $point['work_time_list'] as $working ) {

						if ( 7 == $working['day'] ) {
							$weekday = $wp_locale->get_weekday( 0 );
						} else {
							$weekday = $wp_locale->get_weekday( $working['day'] );
						}

						list( $time_from, $time_to ) = explode( '/', $working['time'] );

						printf( '<li>%s: %s &mdash; %s</li>', $wp_locale->get_weekday_abbrev( $weekday ), $time_from, $time_to );
					}
					?>
				</ul>
			</td>
		</tr>
	<?php endif;?>
	</tbody>

</table>
