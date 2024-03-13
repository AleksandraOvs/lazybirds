<?php

/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.8.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_cart'); ?>

<form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
    <?php do_action('woocommerce_before_cart_table'); ?>
    <div class="cart woocommerce-cart-form__contents">
        <ul class="woocommerce-cart-form__contents__head">
            <li class="product-thumbnail">Наименование товара<?php //esc_html_e('Product', 'woocommerce'); ?></li>
            <li class="product-name">Цена</li>
            <li class="product-price">Количество<?php //esc_html_e('Price', 'woocommerce'); ?></li>
            <li class="product-quantity">Сумма<?php //esc_html_e('Quantity', 'woocommerce'); ?></li>
            <li class="product-subtotal"><?php //esc_html_e('Subtotal', 'woocommerce'); ?></li>
            <!-- <li class="product-remove">&nbsp;</th> -->
        </ul>

        <?php do_action('woocommerce_before_cart_contents'); ?>
        <ul class="woocommerce-cart-form__contents__list">

            <?php
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
                if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                    $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
            ?>
                    <li class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">

                        <!-- <div class="product-main"> -->
                            <!-- <div class="product-thumbnail"> -->
                            <?php
                            $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);

                            if (!$product_permalink) {
                                echo '' . $thumbnail; // PHPCS: XSS ok.
                            } else {
                                printf('<a class="product-thumb" href="%s">%s</a>', esc_url($product_permalink), $thumbnail); // PHPCS: XSS ok.
                            }
                            ?>
                            <!-- </div> -->

                            <div class="product-name" data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
                                <?php
                                if (!$product_permalink) {
                                    echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;');
                                } else {
                                    echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                                }

                                do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);

                                // Meta data.
                                echo wc_get_formatted_cart_item_data($cart_item); // PHPCS: XSS ok.

                                // Backorder notification.
                                if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                                    echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woocommerce') . '</p>', $product_id));
                                }
                                ?>
                            </div>
                        <!-- </div> -->


                        <div class="product-price" data-title="<?php esc_attr_e('Price', 'woocommerce'); ?>">
                            <?php
                            echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                            ?>
                        </div>

                        <div class="product-quantity" data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
                            <div class="cart__counter">

                                <button type="button" class="cart__counter-prev counter-prev minus">
                                    <svg width="11" height="1" viewBox="0 0 11 1" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.5 0.5H0.5" stroke="#17181D" stroke-linecap="round" />
                                    </svg>
                                </button>
                                <?php
                                if ($_product->is_sold_individually()) {
                                    $min_quantity = 1;
                                    $max_quantity = 1;
                                } else {
                                    $min_quantity = 0;
                                    $max_quantity = $_product->get_max_purchase_quantity();
                                }

                                $product_quantity = woocommerce_quantity_input(
                                    array(
                                        'input_name'   => "cart[{$cart_item_key}][qty]",
                                        'input_value'  => $cart_item['quantity'],
                                        'max_value'    => $max_quantity,
                                        'min_value'    => $min_quantity,
                                        'product_name' => $_product->get_name(),
                                    ),
                                    $_product,
                                    false
                                );
                                echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item); // PHPCS: XSS ok.
                                ?>
                                <button type="button" class="cart__counter-next counter-next plus">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16 10H6" stroke="#17181D" stroke-linecap="round" />
                                        <path d="M11 5V15" stroke="#17181D" stroke-linecap="round" />
                                    </svg>

                                </button>


                            </div><!-- ./cart__product-counter -->

                        </div>

                        <div class="product-subtotal" data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>">
                            <?php
                            echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                            ?>
                        </div>

                        <div class="product-remove">
                            <?php
                            echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                'woocommerce_cart_item_remove_link',
                                sprintf(
                                    '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">%s</a>',
                                    esc_url(wc_get_cart_remove_url($cart_item_key)),
                                    esc_html__('Remove this item', 'konte'),
                                    esc_attr($product_id),
                                    esc_attr($_product->get_sku()),
                                    '<svg width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M19.5652 3.47826H14.7826V2.17391C14.7826 1.59736 14.5536 1.04441 14.1459 0.636724C13.7382 0.229037 13.1853 0 12.6087 0H7.3913C6.81475 0 6.2618 0.229037 5.85412 0.636724C5.44643 1.04441 5.21739 1.59736 5.21739 2.17391V3.47826H0.434783C0.319471 3.47826 0.208883 3.52407 0.127345 3.60561C0.0458074 3.68714 0 3.79773 0 3.91304C0 4.02835 0.0458074 4.13894 0.127345 4.22048C0.208883 4.30202 0.319471 4.34783 0.434783 4.34783H1.73913V20.4348C1.73913 20.7807 1.87655 21.1125 2.12116 21.3571C2.36578 21.6017 2.69754 21.7391 3.04348 21.7391H16.9565C17.3025 21.7391 17.6342 21.6017 17.8788 21.3571C18.1234 21.1125 18.2609 20.7807 18.2609 20.4348V4.34783H19.5652C19.6805 4.34783 19.7911 4.30202 19.8727 4.22048C19.9542 4.13894 20 4.02835 20 3.91304C20 3.79773 19.9542 3.68714 19.8727 3.60561C19.7911 3.52407 19.6805 3.47826 19.5652 3.47826ZM6.08696 2.17391C6.08696 1.82798 6.22438 1.49621 6.46899 1.2516C6.7136 1.00699 7.04537 0.869565 7.3913 0.869565H12.6087C12.9546 0.869565 13.2864 1.00699 13.531 1.2516C13.7756 1.49621 13.913 1.82798 13.913 2.17391V3.47826H6.08696V2.17391ZM17.3913 20.4348C17.3913 20.5501 17.3455 20.6607 17.264 20.7422C17.1824 20.8238 17.0718 20.8696 16.9565 20.8696H3.04348C2.92817 20.8696 2.81758 20.8238 2.73604 20.7422C2.6545 20.6607 2.6087 20.5501 2.6087 20.4348V4.34783H17.3913V20.4348ZM7.82609 9.13043V16.087C7.82609 16.2023 7.78028 16.3129 7.69874 16.3944C7.6172 16.4759 7.50662 16.5217 7.3913 16.5217C7.27599 16.5217 7.1654 16.4759 7.08387 16.3944C7.00233 16.3129 6.95652 16.2023 6.95652 16.087V9.13043C6.95652 9.01512 7.00233 8.90453 7.08387 8.823C7.1654 8.74146 7.27599 8.69565 7.3913 8.69565C7.50662 8.69565 7.6172 8.74146 7.69874 8.823C7.78028 8.90453 7.82609 9.01512 7.82609 9.13043ZM13.0435 9.13043V16.087C13.0435 16.2023 12.9977 16.3129 12.9161 16.3944C12.8346 16.4759 12.724 16.5217 12.6087 16.5217C12.4934 16.5217 12.3828 16.4759 12.3013 16.3944C12.2197 16.3129 12.1739 16.2023 12.1739 16.087V9.13043C12.1739 9.01512 12.2197 8.90453 12.3013 8.823C12.3828 8.74146 12.4934 8.69565 12.6087 8.69565C12.724 8.69565 12.8346 8.74146 12.9161 8.823C12.9977 8.90453 13.0435 9.01512 13.0435 9.13043Z" fill="#003996"/>
									</svg>'
                                ),
                                $cart_item_key
                            );
                            ?>
                        </div>
                    </li>
            <?php
                }
            }
            ?>

        </ul>

        <?php do_action('woocommerce_cart_contents'); ?>

        <div class="woocommerce-cart-form__contents__bottom">
            <ul class="woocommerce-cart-form__contents__summary__links">
                <li><a href="/">Условия доставки</a></li>
                <li><a href="/">Способы оплаты</a></li>
                <li><a href="/">Возврат товара</a></li>
            </ul>

            <?php if (wc_coupons_enabled()) { ?>
                <div class="coupon">
                    <label for="coupon_code"><?php esc_html_e('Coupon:', 'woocommerce'); ?></label>

                    <div class="coupon-apply">
                        <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e('Coupon code', 'woocommerce'); ?>" />

                        <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>"><?php esc_html_e('Apply coupon', 'woocommerce'); ?></button>
                    </div>

                    <?php do_action('woocommerce_cart_coupon'); ?>
                </div>
            <?php } ?>

            <div class="cart-totals">
                <div class="cart-subtotal">
                    <p><?php _e('Subtotal', 'konte'); ?></p>
                    <p data-title="<?php esc_attr_e('Subtotal', 'konte'); ?>"><?php wc_cart_totals_subtotal_html(); ?></p>
                </div>

                <div class="order-total">
                    <p><?php _e('Total', 'konte'); ?></p>
                    <p data-title="<?php esc_attr_e('Total', 'konte'); ?>"><?php wc_cart_totals_order_total_html(); ?></p>
                </div>
            </div>
        </div>

        <button type="submit" class="button" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>"><?php esc_html_e('Update cart', 'woocommerce'); ?></button>


        <?php do_action('woocommerce_cart_actions'); ?>

        <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>


        <?php do_action('woocommerce_after_cart_contents'); ?>
    </div>

    <?php do_action('woocommerce_after_cart_table'); ?>

</form>

<?php do_action('woocommerce_before_cart_collaterals'); ?>

<div class="cart-collaterals">
    <?php
    /**
     * Cart collaterals hook.
     *
     * @hooked woocommerce_cross_sell_display
     * @hooked woocommerce_cart_totals - 10
     */
    do_action('woocommerce_cart_collaterals');
    ?>
</div>

<?php do_action('woocommerce_after_cart'); ?>