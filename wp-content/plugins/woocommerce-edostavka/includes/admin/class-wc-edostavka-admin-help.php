<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Edostavka_Admin_Help', false ) ) {
	return new WC_Edostavka_Admin_Help();
}

class WC_Edostavka_Admin_Help {

	public function __construct() {
		add_action( 'current_screen', array( $this, 'add_tabs' ) );
	}

	public function add_tabs() {

		$screen = get_current_screen();

		if ( ! $screen || ! in_array( $screen->id, array( 'woocommerce_page_wc-settings', 'woocommerce_page_wc_edostavka_orders' ), true ) ) {
			return;
		}

		if( $screen->id == 'woocommerce_page_wc_edostavka_orders' ) {

			$screen->add_help_tab( array(
				'id'      => 'edostavka_orders_support_tab',
				'title'   => 'Работа с заказами',
				'content' => '<h2>Работа с заказами СДЭК</h2><p>В данном разделе вы можете отслеживать все заказы вашего магазина которые созданы с методом доставки СДЭК.</p>'
			) );

			$screen->set_help_sidebar(
				'<p><strong>Дополнительная информация:</strong></p>' .
				'<p><a href="https://woodev.ru/shop" target="_blank">Все плагины Woodev</a></p>' .
				'<p><a href="https://woodev.ru/my-account" target="_blank">Личный кабинет</a></p>' .
				'<p><a href="https://woodev.ru/support" target="_blank">Поддежка пользователей Woodev</a></p>'
			);
		}

		if( $screen->id == 'woocommerce_page_wc-settings' && wc_edostavka_shipping()->is_plugin_settings() && wc_edostavka_shipping()->get_documentation_url() ) {

			$screen->add_help_tab( array(
				'id'        => 'edostavka_integration_settings_tab',
				'title'     => 'Настройки СДЭК',
				'content'   => sprintf(
					'<p>Перед началом работы плагина <strong>%s</strong> обязательно <a href="%s" target="_blank">ознакомьтесь с документацией плагина</a>. Если у вас вознилкли трудности в настройке или использовании плагина, вы всегда можете <a href="%s" target="_blank">обратиться в службу поддрежки</a>.</p>',
					wc_edostavka_shipping()->get_plugin_name(),
					esc_url( wc_edostavka_shipping()->get_documentation_url() ),
					esc_url( wc_edostavka_shipping()->get_support_url() )
				)
			) );
		}
	}
}

return new WC_Edostavka_Admin_Help();
