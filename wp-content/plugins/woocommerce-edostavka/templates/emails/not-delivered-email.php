<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php echo wptexturize( wpautop( $content ) ); ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
