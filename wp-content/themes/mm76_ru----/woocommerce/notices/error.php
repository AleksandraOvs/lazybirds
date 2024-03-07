<?php
/**
 * Show error messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/error.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $notices ) {
	return;
}

?>
<ul class="woocommerce-error" role="alert">
	<?php foreach ( $notices as $notice ) : ?>
		<li<?php echo wc_get_notice_data_attr( $notice ); ?>>
			<span class="svg-icon icon-success size-normal message-icon">
				<i class="fa-solid fa-circle-exclamation"></i>
			</span>
			<?= $notice['notice']; ?>
			<span class="svg-icon icon-close size-normal close-message">
				<i class="fa-solid fa-xmark"></i>
			</span>
		</li>
	<?php endforeach; ?>
</ul>