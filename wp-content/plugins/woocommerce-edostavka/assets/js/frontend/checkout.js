( function( $ ) {

	if( 'undefined' !== edostavka_common_checkout_params && edostavka_common_checkout_params?.available_fee_payments ) {
		//Обновляем методы доставки если сменился метод оплаты.
		$( 'form.checkout' ).on( 'change', 'input[name^="payment_method"]', function() {
			$( document.body ).trigger( 'update_checkout', { update_shipping_method: true } );
		} );
	}

} )( jQuery )
