<tr valign="top" id="packing_options">
	<th scope="row" class="titledesc">Размеры коробок</th>
	<td class="forminp">
		<style type="text/css">
			.wc_edostavka_boxes td {
				vertical-align: middle;
				padding: 4px 7px;
			}
			.wc_edostavka_boxes td > span {
				padding: 7px 5px;
				display: inline-block;
			}
			.wc_edostavka_boxes th {
				padding: 9px 7px;
			}
			.wc_edostavka_boxes td input {
				margin-right: 4px;
			}
			.wc_edostavka_boxes .check-column {
				vertical-align: middle;
				text-align: left;
				padding: 0 7px;
			}
			.wc_edostavka_boxes td .toggle-checkbox {
				display: none;
			}
		</style>
		<table class="wc_edostavka_boxes widefat">
			<thead>
				<tr>
					<th class="check-column"><input type="checkbox" /></th>
					<th>Название коробки</th>
					<th>Длина <?php echo wc_help_tip( 'Максимальная длина корбки в сантиметрах.' );?></th>
					<th>Ширина <?php echo wc_help_tip( 'Максимальная ширина корбки в сантиметрах.' );?></th>
					<th>Высота <?php echo wc_help_tip( 'Максимальная высота корбки в сантиметрах.' );?></th>
					<th>Максимальный вес <?php echo wc_help_tip( 'Максимально допустимый вес корбки в килограмах.' );?></th>
					<th>Стоимость <?php echo wc_help_tip( 'Стоимость коробки, которая будет прибавляться к стоимости доставки. Для коробок СДЭК, цена назначается автоматически. Для своих коробок можно указать как фиксированную цену, так и в процентах (например 10%). Если указать в процентах, то стоимость будет расчитана из стоимости товаров в этой коробке.' );?></th>
					<th>Включено <?php echo wc_help_tip( 'Использовать данную коробку для упаковки товаров.' );?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th colspan="3">
						<a href="#" class="button plus insert">Добавить упаковку</a>
						<a href="#" class="button minus remove">Удалить выделеные</a>
					</th>
					<th colspan="6">
						<!-- Возможно сюда добавить какую то информацию-->
					</th>
				</tr>
			</tfoot>
			<tbody id="boxes">
				<?php

					foreach ( wc_edostavka_get_carton_boxes() as $key => $box ) {
						$box_is_enabled = ! isset( $boxes[ $key ]['enabled'] ) || $boxes[ $key ]['enabled'] == 1;
						$box_is_cost = ! isset( $boxes[ $key ]['cost'] ) || $boxes[ $key ]['cost'] == 1;

						$toggle_class = $toggle_cost_class = array( 'woocommerce-input-toggle' );

						if( $box_is_enabled ) {
							$toggle_class[] = 'woocommerce-input-toggle--enabled';
						} else {
							$toggle_class[] = 'woocommerce-input-toggle--disabled';
						}

						if( $box_is_cost ) {
							$toggle_cost_class[] = 'woocommerce-input-toggle--enabled';
						} else {
							$toggle_cost_class[] = 'woocommerce-input-toggle--disabled';
						}
						?>
						<tr>
							<td class="check-column"></td>
							<td><?php echo $box['name']; ?></td>
							<td><input type="text" size="5" readonly value="<?php echo esc_attr( $box['length'] ); ?>" /><span>см</span></td>
							<td><input type="text" size="5" readonly value="<?php echo esc_attr( $box['width'] ); ?>" /><span>см</span></td>
							<td><input type="text" size="5" readonly value="<?php echo esc_attr( $box['height'] ); ?>" /><span>см</span></td>
							<td><input type="text" size="5" readonly value="<?php echo esc_attr( $box['weight'] ); ?>" /><span>кг</span></td>
							<td>
								<input type="checkbox" class="toggle-checkbox" name="boxes_cost[<?php echo $key; ?>]" <?php checked( $box_is_cost, true ); ?> />
								<a href="#" class="checkbox-toggle-enabled">
									<span class="<?php echo implode( ' ',  $toggle_cost_class ); ?>"></span>
								</a>
							</td>
							<td>
								<input type="checkbox" class="toggle-checkbox" name="boxes_enabled[<?php echo $key; ?>]" <?php checked( $box_is_enabled, true ); ?> />
								<a href="#" class="checkbox-toggle-enabled">
									<span class="<?php echo implode( ' ',  $toggle_class ); ?>"></span>
								</a>
							</td>
						</tr>
						<?php
					}

					foreach ( $boxes as $key => $box ) {
						if ( ! is_numeric( $key ) )
							continue;
						$box_name = isset( $box['name'] ) ? esc_attr( $box['name'] ) : '';
						?>
						<tr>
							<td class="check-column"><input type="checkbox" /></td>
							<td><input type="text" size="30" name="boxes_name[<?php echo $key; ?>]" value="<?php echo $box_name; ?>" /></td>
							<td><input type="text" size="5" name="boxes_length[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['length'] ); ?>" /><span>см</span></td>
							<td><input type="text" size="5" name="boxes_width[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['width'] ); ?>" /><span>см</span></td>
							<td><input type="text" size="5" name="boxes_height[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['height'] ); ?>" /><span>см</span></td>
							<td><input type="text" size="5" name="boxes_weight[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['weight'] ); ?>" /><span>кг</span></td>
							<td><input type="text" size="5" name="boxes_cost[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['cost'] ); ?>" /></td>
							<td>
								<input type="checkbox" class="toggle-checkbox" name="boxes_enabled[<?php echo $key; ?>]" <?php checked( $box['enabled'], true ); ?> />
								<a href="#" class="checkbox-toggle-enabled">
									<span class="woocommerce-input-toggle <?php echo $box['enabled'] ? 'woocommerce-input-toggle--enabled' : 'woocommerce-input-toggle--disabled'; ?>"></span>
								</a>
							</td>
						</tr>
						<?php
					}

				?>
			</tbody>
		</table>
	</td>
</tr>
