<tr valign="top" id="woocommerce_edostavka_cron_update" style="display: none;">
	<th scope="row" class="titledesc">Следующее обновление:</th>
	<td class="forminp">
		<p><?php

			/** @var string $timestamp */
			$next_date = wp_date( wc_date_format(), $timestamp, new DateTimeZone( wc_timezone_string() ) );
			$next_time = wp_date( wc_time_format(), $timestamp, new DateTimeZone( wc_timezone_string() ) );

			if( $next_date && $next_time ) {
				printf( __( '%s at %s', 'woocommerce-edostavka' ), $next_date, $next_time );
			} else {
				echo 'Пока ещё не запланировано.';
			}
			?></p>
		<p>
			<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'update_orders' ) ) ); ?>" class="button">
				<?php _e( 'Update Orders', 'woocommerce-edostavka' ); ?>
			</a>
		</p>
	</td>
</tr>
