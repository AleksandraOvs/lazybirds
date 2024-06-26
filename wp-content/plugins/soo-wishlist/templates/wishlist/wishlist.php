<?php
/**
 * Template for displaying wishlist.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wishlist/wishlist.php.
 *
 * @author        SooPlugins
 * @package       Soo Wishlist/Templates
 * @version       1.0.0
 */

global $product, $post;
?>

<?php do_action( 'soo_wishlist_before_list', $list ); ?>

<ul class="shop_table shop_table_responsive wishlist_table" cellspacing="0">
	<!-- <thead>
	<tr>
		<?php //if ( Soo_Wishlist()->can_edit( $list ) ) : ?>
			<th class="product-remove">&nbsp;</th>
		<?php //endif; ?>
		<th class="product-thumbnail">&nbsp;</th>
		<th class="product-name"><?php //esc_html_e( 'Product', 'soow' ); ?></th>
		<?php //if ( $options['show_price'] == 'yes' ) : ?>
			<th class="product-price"><?php //esc_html_e( 'Price', 'soow' ); ?></th>
		<?php //endif; ?>
		<?php //if ( $options['show_stock_status'] == 'yes' ) : ?>
			<th class="product-stock-status"><?php //esc_html_e( 'Stock status', 'soow' ); ?></th>
		<?php //endif; ?>
		<?php //if ( $options['show_button'] == 'yes' ) : ?>
			<th class="product-add-to-cart">&nbsp;</th>
		<?php //endif; ?>
	</tr>
	</thead> -->

	<?php foreach ( $list->items as $key => $item ) : ?>
		<?php
		$item_id = soow_translated_object_id( $item['id'], 'product' );
		$product = wc_get_product( $item_id );

		if ( ! $product || ! $product->is_visible() ) {
			continue;
		}
		?>

		<li class="product-item">
			<?php if ( Soo_Wishlist()->can_edit( $list ) ) : ?>
				<td class="product-remove">
					<?php
					echo apply_filters(
						'soo_wishlist_item_remove_link',
						sprintf(
							'<a href="%s" class="remove" title="%s" data-product_id="%s"><span>remove</span></a>',
							esc_url( Soo_Wishlist()->get_remove_url( $item['id'] ) ),
							esc_html__( 'Remove this item', 'soow' ),
							esc_attr( $product->get_id() )
						),
						$product
					);
					?>
				</td>
			<?php endif; ?>
			<div class="product-thumbnail">
				<a href="<?php echo $product->get_permalink() ?>"><?php echo $product->get_image('medium') ?></a>
			</div>

			<div class="wish-product-summary">
<div class="product-name">
				<a href="<?php echo $product->get_permalink() ?>"><?php echo $product->get_title() ?></a>
				</div>

				<div class="wish-product-summary__price">
						<?php if ( $options['show_price'] == 'yes' ) : ?>
				<div class="product-price">
					<?php echo $product->get_price_html() ?>
						</div>

						<?php if ( $options['show_stock_status'] == 'yes' ) : ?>
				<div class="product-stock-status">
					<?php
					if ( $product->is_in_stock() ) {
						esc_html_e( 'In stock', 'soow' );
					} else {
						esc_html_e( 'Out of stock', 'soow' );
					}
					?>
				</div>
			<?php endif; ?>


			<?php endif; ?>
				</div>

				<?php if ( $options['show_button'] == 'yes' ) : ?>
				<div class="product-add-to-cart">
					<?php
					if ( $product->is_in_stock() && $product->is_purchasable() ) {
						woocommerce_template_loop_add_to_cart();
					}
					?>
				</div>
			<?php endif; ?>

			</div>

			

			
				<?php
// 			$attr = $product->get_attributes() ; // Получаем атрибуты товара
  
//   foreach ($attr as $key => $value) {
// 	   echo wc_attribute_label( $value['name'] ) . ": "; // Выводим наименование атрибута
// 	   foreach ( $value->get_terms() as $pa ) { // Выборка значения заданного атрибута
// 			echo '<b> '.$pa->name.'</b> '; // Выводим значение атрибута
// 	   }
//   }
  ?>
			

		

			

			
				</li>
	<?php endforeach; ?>
				</ul>

<?php do_action( 'soo_wishlist_after_list', $list ); ?>

<?php
wc_setup_product_data( $post );