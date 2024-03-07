<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Edostavka Integration Class
 *
 * @class    WC_Edostavka_Integration
 * @extends  WC_Integration
 * @version  2.2.0
 * @package  Woodev\Edostavka
 */
class WC_Edostavka_Integration extends WC_Integration {

	public function __construct() {

		$this->id                 = wc_edostavka_shipping()->get_method_id();
		$this->method_title       = sprintf( 'СДЭК доставка (v%s)', WC_CDEK_SHIPPING_VERSION );
		$this->method_description = sprintf( 'Основные настройки плагина %s', wc_edostavka_shipping()->get_plugin_name() );

		$this->init_form_fields();
		$this->init_settings();

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'woocommerce_update_options_integration_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_update_options_integration_' . $this->id, array( $this, 'clear_transients' ) );
	}

	public function enqueue_scripts() {
		if ( wc_edostavka_shipping()->is_plugin_settings() ) {
			wp_enqueue_script( 'edostavka-admin-integration-script', wc_edostavka_shipping()->get_plugin_url() . '/assets/js/admin/integration.js', array(
				'jquery',
				'selectWoo'
			), WC_CDEK_SHIPPING_VERSION, true );
			wp_localize_script( 'edostavka-admin-integration-script', 'edostavka_integration_params', $this->get_localize_params() );
		}
	}

	public function process_admin_options() {

		parent::process_admin_options();

		$webhook_ids = get_option( 'wc_edostavka_webhook_ids', array() );

		// if we have API credentials but haven't generated webhooks yet, create them
		if ( $this->is_configured() && empty( $webhook_ids ) ) {
			wc_edostavka_shipping()->get_webhook_handler()->reset_webhooks();
		}

		wp_clear_scheduled_hook( 'wc_edostavka_orders_update' );
	}

	public function admin_options() {
		wc_edostavka_shipping()->get_admin_instance()->message_handler->show_messages();
		parent::admin_options();
	}

	/**
	 * @return array
	 */
	private function get_localize_params() {
		$params = array(
			'ajax_url'      => admin_url( 'admin-ajax.php', 'relative' ),
			'lang'          => wc_edostavka_get_locale(),
			'customer_city' => null
		);

		$customer_default_city = $this->get_option( 'customer_default_city', null );
		if ( is_numeric( $customer_default_city ) ) {
			$params['customer_city'] = wc_edostavka_get_location_cities_select( array(
				'code' => $customer_default_city,
				'size' => 1,
				'lang' => wc_edostavka_get_locale()
			) );
		}

		return apply_filters( 'wc_edostavka_integration_params', $params, $this );
	}

	/**
	 * @return void
	 * @see WC_Settings_API::init_form_fields()
	 */
	public function init_form_fields() {

		$form_fields = array(
			'api_settings' => array(
				'title'       => 'Данные авторизации',
				'description' => implode( '', array(
					sprintf( '<p>Для работы плагина <strong>%s</strong> обязательно требуются боевые учётные данные от API СДЭК. Не путайте их с логином и паролем от личного кабинета СДЭК.</p>', wc_edostavka_shipping()->get_plugin_name() ),
					sprintf( '<p>Ключи можно получить во вкладке Интеграция в <a href="%s" target="_blank">вашем личном кабинете</a>, который создается автоматически при подписании договора со СДЭК.</p>', esc_url_raw( 'https://lk.cdek.ru/' ) )
				) ),
				'type'        => 'title'
			),
			'api_login'    => array(
				'title'             => 'ID клиента API',
				'type'              => 'text',
				'desc_tip'          => 'Идентификатор клиента можно получить в личном кабинете СДЭК',
				'custom_attributes' => array( 'required' => 'required' )
			),
			'api_password' => array(
				'title'             => 'Секретный ключ API',
				'type'              => 'text',
				'desc_tip'          => 'Пароль клиента можно получить в личном кабинете СДЭК',
				'custom_attributes' => array( 'required' => 'required' )
			),
		);

		if ( $this->is_configured() ) {

			$form_fields['auth_status'] = array(
				'type' => 'authorization'
			);

			$form_fields['sender_settings'] = array(
				'title' => 'Данные отправителя',
				'type'  => 'title'
			);

			$form_fields['sender_company'] = array(
				'title'       => 'Название компании.',
				'type'        => 'text',
				'placeholder' => 'ООО Рога и Копыта',
				'desc_tip'    => 'Укажите название компании которая будет производить откгрузку товаров.'
			);

			$form_fields['sender_name'] = array(
				'title'       => 'ФИО отправителя.',
				'type'        => 'text',
				'placeholder' => 'Иванов Иван Иванович',
				'desc_tip'    => 'Укажите ФИО отправителя. Отчество необязательно.'
			);

			$form_fields['sender_email'] = array(
				'title'       => 'Адрес электронной почты',
				'type'        => 'email',
				'placeholder' => 'email@mail.com',
				'desc_tip'    => 'Укажите email отправителя.',
				'default'     => get_option( 'admin_email' )
			);

			$form_fields['sender_phone'] = array(
				'title'       => 'Номер телефона',
				'type'        => 'text',
				'placeholder' => '+79001234567',
				'desc_tip'    => 'Укажите телефон отправителя.'
			);

			$form_fields['sender_address'] = array(
				'title'       => 'Адрес отправителя',
				'type'        => 'text',
				'placeholder' => 'г.Москва, ул. Цветной бульвар, 22 стр. 6',
				'desc_tip'    => 'Адрес грузоотправителя. Нужно для международных заказов.'
			);

			$form_fields['seller_settings'] = array(
				'title' => 'Данные продавца',
				'type'  => 'title'
			);

			$form_fields['seller_name'] = array(
				'title'       => 'Наименование истинного продавца.',
				'type'        => 'text',
				'placeholder' => 'ООО Рога и Копыта',
				'desc_tip'    => 'Укажите наименование продавца.'
			);

			$form_fields['seller_inn'] = array(
				'title'       => 'ИНН продавца.',
				'type'        => 'text',
				'placeholder' => 'Введите ИНН от 10 до 12 цифр',
				'desc_tip'    => 'Укажите ИНН компании продавца.'
			);

			$form_fields['seller_phone'] = array(
				'title'       => 'Номер телефона продавца',
				'type'        => 'text',
				'placeholder' => '+79001234567',
				'desc_tip'    => 'Укажите телефон продавца.'
			);

			$form_fields['seller_ownership_form'] = array(
				'title'    => 'Форма собственности',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'desc_tip' => 'Выберите тип формы собственности продавца.',
				'options'  => array(
					'9'   => 'Акционерное общество',
					'61'  => 'Закрытое акционерное общество',
					'63'  => 'Индивидуальный предприниматель',
					'119' => 'Открытое акционерное общество',
					'137' => 'Общество с ограниченной ответственностью',
					'147' => 'Публичное акционерное общество'
				)
			);

			$form_fields['seller_address'] = array(
				'title'       => 'Адрес продавца',
				'type'        => 'text',
				'placeholder' => 'г.Москва, ул. Цветной бульвар, 22 стр. 6',
				'desc_tip'    => 'Адрес истинного продавца. Используется при печати инвойсов для отображения адреса настоящего продавца товара, либо торгового названия.'
			);

			$form_fields['fields_settings'] = array(
				'title' => 'Настройки полей',
				'type'  => 'title'
			);

			$form_fields['customer_default_city'] = array(
				'title'             => 'Город получатель по умолчанию',
				'desc_tip'          => 'Укажите город получатель по умолчанию. Данный город будет использован для пользователей которые ещё не выбирали свой населённый пункт.',
				'type'              => 'select',
				'class'             => 'wc-edostavka-default-city',
				'placeholder'       => 'Выберите город получателя',
				'options'           => array(),
				'custom_attributes' => array(
					'data-placeholder' => 'Выберите город получателя'
				)
			);

			$form_fields['enable_dropdown_city_field'] = array(
				'title'             => 'Выпадающий список городов',
				'desc_tip'          => 'Включить выпадающий список городов для поля "Населённый пункт".',
				'type'              => 'select',
				'class'             => 'wc-enhanced-select',
				'placeholder'       => 'Выбирите тип отображения',
				'default'           => 'enable',
				'options'           => array(
					'enable' => 'Включить для всех стран',
					'zone'   => 'Включить только для используемых зон доставки',
					'none'   => 'Не использовать'
				),
				'custom_attributes' => array(
					'data-placeholder' => 'Выбирите тип отображения'
				)
			);

			$form_fields['enable_custom_city'] = array(
				'title'    => 'Разрешить города не из списка',
				'desc_tip' => 'Включив данную опцию, вы разрешите выбирать "несуществующие" города из списка. Не рекомендуется вкючать, так как если города нету в списке, значит СДЭК не производит доставку в этот НП.',
				'type'     => 'checkbox',
				'label'    => 'Да',
				'default'  => 'no'
			);

			$form_fields['hide_single_country'] = array(
				'title'    => 'Скрыть поле "Страна"',
				'type'     => 'checkbox',
				'desc_tip' => 'Скрыть поле "Страна" если магазин использует только одну страну для доставки.',
				'label'    => 'Скрыть',
				'default'  => 'no'
			);

			$form_fields['disable_state_field'] = array(
				'title'    => 'Отключить поле "Область/Район"',
				'desc_tip' => 'Данная опция отключает поле "Область/Район". Рекомендуется включить эту опцию, так как данное поле не нужно для СДЭК.',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'default'  => 'always',
				'options'  => array(
					'none'   => 'Не отключать',
					'always' => 'Отключить совсем',
					'only'   => 'Отключить только для методов СДЭК'
				)
			);

			$form_fields['disable_address_field'] = array(
				'title'    => 'Отключить поле "Адрес"',
				'desc_tip' => 'Данная опция отключает поле "Адрес" если выбран метод доставки СДЭК "до ПВЗ" или "до постамата".',
				'type'     => 'checkbox',
				'default'  => 'yes',
				'label'    => 'Отключить',
			);

			$form_fields['clean_address_field'] = array(
				'title'    => 'Очищать поле "Адрес"',
				'desc_tip' => 'Данная опция очищает поле "Адрес" если покупатель выбрал другой город получатель.',
				'type'     => 'checkbox',
				'default'  => 'yes',
				'label'    => 'Отключить',
			);

			$form_fields['disable_postcode_field'] = array(
				'title'    => 'Отключить поле "Почтовый индекс"',
				'desc_tip' => 'Данная опция отключает поле "Почтовый индекс". Рекомендуется включить эту опцию, так как данное поле не нужно для СДЭК.',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'default'  => 'none',
				'options'  => array(
					'none'   => 'Не отключать',
					'always' => 'Отключить совсем',
					'only'   => 'Отключить только для методов СДЭК'
				)
			);

			$form_fields['popup_map_settings'] = array(
				'title' => 'Настройки карты выбора ПВЗ',
				'type'  => 'title'
			);

			$form_fields['map_button_position'] = array(
				'title'    => 'Где отображать кнопку',
				'desc_tip' => 'Выберите где должна отображаться кнопка вызова карты ПВЗ',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'default'  => 'under-methods',
				'options'  => array(
					'under-methods' => 'Под методами доставки',
					'inline-method' => 'В самом методе доставки'
				)
			);

			$form_fields['action_button_color'] = array(
				'title'    => 'Цвет кнопки вызова карты',
				'type'     => 'color',
				'css'      => 'width:6em;',
				'default'  => '#00bc4c',
				'autoload' => false,
				'desc_tip' => 'Цвет кнопки для вызова вплывающей карты выбора ПВЗ',
			);

			$form_fields['choose_button_color'] = array(
				'title'    => 'Цвет кнопки выбора ПВЗ',
				'type'     => 'color',
				'css'      => 'width:6em;',
				'default'  => '#00bc4c',
				'autoload' => false,
				'desc_tip' => 'Цвет кнопки для выбора ПВЗ',
			);

			$form_fields['enable_fill_postcode_filed'] = array(
				'title'    => 'Заменять индекс в поле "Почтовый индекс"',
				'desc_tip' => 'Заменять значение поля "Почтовый индекс" индексом выбранного ПВЗ.',
				'type'     => 'checkbox',
				'default'  => 'no',
				'label'    => 'Включить',

			);

			$form_fields['show_search_field_on_map'] = array(
				'title'    => 'Показывать поле поиска на карте',
				'desc_tip' => 'Опция включает отображение поля для поиска ПВЗ на карте',
				'type'     => 'checkbox',
				'label'    => 'Да',
				'default'  => 'no'
			);

			$form_fields['package_settings'] = array(
				'title'       => 'Габариты и вес товара поумолчанию.',
				'type'        => 'title',
				'description' => 'Данные параметры будут применяться к товарам у которых не указаны значения габаритов и веса.'
			);

			$form_fields['default_weight'] = array(
				'title'       => 'Масса по умолчанию, (грамм)',
				'type'        => 'decimal',
				'css'         => 'width:75px;',
				'default'     => 100,
				'placeholder' => 'Вес',
				'desc_tip'    => 'Укажите массу одного товара по умолчанию.'
			);

			$form_fields['default_height'] = array(
				'title'       => 'Высота по умолчанию, (см.)',
				'type'        => 'decimal',
				'css'         => 'width:75px;',
				'default'     => 10,
				'placeholder' => 'Высота',
				'desc_tip'    => 'Укажите высоту одного товара по умолчанию.'
			);

			$form_fields['default_width'] = array(
				'title'       => 'Ширина по умолчанию, (см.)',
				'type'        => 'decimal',
				'css'         => 'width:75px;',
				'default'     => 15,
				'placeholder' => 'Ширина',
				'desc_tip'    => 'Укажите ширину одного товара по умолчанию.'
			);

			$form_fields['default_length'] = array(
				'title'       => 'Длина по умолчанию, (см.)',
				'type'        => 'decimal',
				'css'         => 'width:75px;',
				'default'     => 10,
				'placeholder' => 'Длина',
				'desc_tip'    => 'Укажите длину одного товара по умолчанию.'
			);

			$form_fields['packing_settings'] = array(
				'title'       => 'Параметры упаковки товаров',
				'type'        => 'title',
				'description' => 'Параметры ниже будут определять как будет формировать упаковка товаров.',
			);

			$form_fields['packing_method'] = array(
				'title'   => 'Метод упаковки',
				'type'    => 'select',
				'default' => 'per_item',
				'class'   => 'packing_method',
				'options' => wc_edostavka_get_box_packing_methods()
			);

			$form_fields['boxes'] = array(
				'type' => 'box_packing'
			);

			$form_fields['unpacking_item_method'] = array(
				'type'        => 'select',
				'description' => 'Как упаковывать товары которые не поместились в коробки?',
				'default'     => 'per_item',
				'options'     => array(
					'per_item'   => 'Каждый товар индивидуально (по умолчанию)',
					'single_box' => 'Упаковывать все товары в одну коробку'
				)
			);

			$form_fields['name_single_box'] = array(
				'title'       => 'Название коробки',
				'type'        => 'text',
				'default'     => __( 'Common box', 'woocommerce-edostavka' ),
				'placeholder' => 'Укажите название коробки',
				'desc_tip'    => 'Укажите название котрое будет применяться к коробке.'
			);

			$form_fields['name_item_box'] = array(
				'title'       => 'Название коробки',
				'type'        => 'text',
				'default'     => sprintf( '%s {product_name}', _x( 'Box', 'Name of item box', 'woocommerce-edostavka' ) ),
				'placeholder' => 'Укажите название коробки',
				'desc_tip'    => 'Укажите название котрое будет применяться к коробке. Можно использовать теги {product_name} - название товара, {product_sku} - артикул товара, {product_id} - ID товара'
			);

			$form_fields['order_settings'] = array(
				'title' => 'Настройки заказов',
				'type'  => 'title'
			);

			$form_fields['order_prefix'] = array(
				'title'    => 'Префикс для заказов',
				'type'     => 'text',
				'desc_tip' => 'Укажите префикс который будет добавляться к номеру заказа при отправке в СДЭК.'
			);

			$form_fields['auto_export_orders'] = array(
				'title'    => 'Автоматически экспортировать заказы',
				'desc_tip' => 'Включить автоматический экспорт заказов в ЛК СДЭК.',
				'default'  => 'no',
				'label'    => 'Вкл/Выкл',
				'type'     => 'checkbox'
			);

			$form_fields['export_statuses'] = array(
				'title'             => 'Статусы заказ для экспорта&hellip;',
				'type'              => 'multiselect',
				'options'           => array_diff_key(
					wc_get_order_statuses(),
					array_flip( apply_filters( 'wc_edostavka_disabled_statuses_for_export', array(
						'wc-completed',
						'wc-cancelled',
						'wc-refunded',
						'wc-failed'
					) ) )
				),
				'class'             => 'chosen_select',
				'css'               => 'width: 400px;',
				'description'       => 'Выберите статусы заказов при достижении которых заказ будет автоматически экспортироваться в ЛК СДЭК',
				'desc_tip'          => true,
				'custom_attributes' => array(
					'data-placeholder' => 'Выберите статус заказа'
				)
			);

			$form_fields['dadata_settings'] = array(
				'title'       => 'Настройки сервиса Dadata',
				'type'        => 'title',
				'description' => sprintf(
					'Для работы опций в этом блоке обязательно указать данные от сервиса <a href="%s" target="_blank">DADATA</a>. Эти данные можно получить в вашем <a href="%s" target="_blank">личном кабинете Dadata</a>.<br /> Данная опция <strong>работает только для городов России</strong>.',
					esc_url( add_query_arg( array( 'ref' => 6846 ), 'https://dadata.ru/' ) ),
					esc_url( add_query_arg( array( 'ref' => 6846 ), 'https://dadata.ru/profile/' ) )
				)
			);

			$form_fields['dadata_token'] = array(
				'title'    => 'Токен Dadata',
				'type'     => 'text',
				'desc_tip' => 'Введите ваш токен от API. В личном кабинете он подписан как "API-ключ"'
			);

			$form_fields['dadata_secret'] = array(
				'title'    => 'Секретный ключ',
				'type'     => 'text',
				'desc_tip' => 'Введите ваш секретный ключ от API.'
			);

			$form_fields['enable_suggestions_state'] = array(
				'title'             => 'Автоподсказки для поля "Район/область"',
				'desc_tip'          => 'Включить автоподсказки для поля "Район/область". Работает толькое если в опции "Выпадающий список городов" выбрано значение "не использовать"',
				'type'              => 'checkbox',
				'label'             => 'Включить',
				'default'           => 'no',
				'sanitize_callback' => array( $this, 'validate_dadata_fields' )
			);

			$form_fields['enable_suggestions_city'] = array(
				'title'             => 'Автоподсказки для поля "Населённый пункт"',
				'desc_tip'          => 'Включить автоподсказки для поля "Населённый пункт". Работает толькое если в опции "Выпадающий список городов" выбрано значение "не использовать"',
				'type'              => 'checkbox',
				'label'             => 'Включить',
				'default'           => 'no',
				'sanitize_callback' => array( $this, 'validate_dadata_fields' )
			);

			$form_fields['enable_suggestions_address'] = array(
				'title'             => 'Автоподсказки для поля адрес',
				'desc_tip'          => 'Включить автоподсказки для поля "Адрес".',
				'type'              => 'checkbox',
				'label'             => 'Включить',
				'default'           => 'no',
				'sanitize_callback' => array( $this, 'validate_dadata_fields' )
			);

			$form_fields['reload_checkout_fields'] = array(
				'title'    => 'Перезагружать форму оформления заказа',
				'desc_tip' => 'Включите эту опцию если хотите что бы после того как покупатель выбрал адрес из подсказок, форма оформления заказа перезагрузилась. Нужно если адрес доставки влияет на её стоимость.',
				'type'     => 'checkbox',
				'label'    => 'Включить',
				'default'  => 'no'
			);

			$form_fields['fill_postcode_field'] = array(
				'title'    => 'Заполнять поле "Почтовый индекс"',
				'desc_tip' => 'Включите эту опцию если хотите что бы поле "Почтовый индекс" заполянлось автоматически на основании выбранного адреса из подсказок',
				'type'     => 'checkbox',
				'label'    => 'Включить',
				'default'  => 'no'
			);

			$form_fields['enable_detect_customer_location'] = array(
				'title'    => 'Автоматически определять местоположение',
				'desc_tip' => 'Данная опция позволяет получать метоположение пользователя на основании GEO данных. Не работает, если в опции "Город получатель по умолчанию" выбран город.',
				'type'     => 'checkbox',
				'label'    => 'Включить',
				'default'  => 'no',
				'sanitize_callback' => array( $this, 'validate_dadata_fields' )
			);


			$form_fields['additional_settings'] = array(
				'title' => 'Дополнительные настройки',
				'type'  => 'title'
			);

			$form_fields['currency'] = array(
				'title'    => 'Валюта расчёта стоимости',
				'desc_tip' => 'Выберите в какой валюте делать расчёт стоимости.',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'default'  => 'default',
				'options'  => array_merge( array(
					'default' => 'Валюта магазина (по-умолчанию)'
				), wc_edostavka_get_allowed_currencies( true ) )
			);

			if ( ! wc_string_to_bool( get_option( 'woocommerce_enable_shipping_calc' ) ) ) {

				$form_fields['disable_methods_on_cart'] = array(
					'title'    => 'Отключить СДЭК в корзине',
					'desc_tip' => 'Не показывать методы СДЭК на старанице "Корзина".',
					'type'     => 'checkbox',
					'label'    => 'Отключить для корзины',
					'default'  => 'no'
				);
			}

			$form_fields['auto_update_orders'] = array(
				'title'    => 'Включить обновление статуса заказа',
				'label'    => 'Включить',
				'desc_tip' => 'Разрешить получать информацию об изменении статуса заказа. Статусы заказа будут автоматически обновляться.',
				'type'     => 'checkbox',
				'default'  => 'yes'
			);

			$form_fields['cron_auto_update_orders'] = array(
				'title'    => 'Обновление заказов по расписанию',
				'label'    => 'Включить',
				'desc_tip' => 'Разрешить обновлять информацию о заказах по рассписанию.',
				'type'     => 'checkbox',
				'default'  => 'yes'
			);

			$form_fields['status_delivered'] = array(
				'title'    => 'Статус доставленного заказа',
				'desc_tip' => 'Укажите статус который будет установлен заказу в случае его фактической доставки до покупателя',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'default'  => 'wc-completed',
				'options'  => array_merge( array(
					'none' => 'Не использовать'
				), wc_get_order_statuses() )
			);

			$form_fields['cron_auto_update_orders_interval'] = array(
				'title'    => 'Интервал обновления (в минутах)',
				'desc_tip' => 'Укажите с каким интервалом нужно делать обновление информации о заказах в минутах. Крайне не рекомендуются устанавливать маленький интервал.',
				'type'     => 'number',
				'default'  => '180',
				'css'      => 'width:75px;'
			);

			$form_fields['cron_update'] = array(
				'type' => 'cron_update'
			);

			$form_fields['enable_debug'] = array(
				'title'       => 'Записывать логи запросов',
				'label'       => 'Включить логирование запросов',
				'type'        => 'checkbox',
				'description' => sprintf( 'Сохранить отчёты об ошибках и запросы/ответы API в <a href="%s">лог файл</a>', Woodev_Helper::get_wc_log_file_url( $this->id ) ),
				'default'     => 'no'
			);
		}

		if ( wc_edostavka_shipping()->get_documentation_url() ) {

			$documentation_url = add_query_arg( array(
				'utm_source' => str_replace( '.', '_', wp_parse_url( home_url(), PHP_URL_HOST ) ),
				'utm_medium' => 'organic'
			), wc_edostavka_shipping()->get_documentation_url() );

			$form_fields['api_settings']['description'] .= sprintf( '<p>С инструкцией по настройке плагина вы можете ознакомиться <a href="%s" target="_blank">на странице документации</a>.</p>', esc_url( $documentation_url ) );
		}

		$this->form_fields = apply_filters( 'wc_cdek_shipping_integration_form_fields', array_map( array(
			$this,
			'set_verify_code'
		), $form_fields ) );
	}

	public function generate_authorization_html() {
		ob_start();
		wc_edostavka_shipping()->load_template( 'views/html-authorization-status.php', array(
			'status' => false !== wc_edostavka_api_get_access_token()
		) );

		return ob_get_clean();
	}

	public function generate_cron_update_html() {
		ob_start();
		wc_edostavka_shipping()->load_template( 'views/html-cron-update.php', array(
			'timestamp' => wp_next_scheduled( 'wc_edostavka_orders_update' )
		) );

		return ob_get_clean();
	}

	public function generate_box_packing_html() {
		ob_start();
		wc_edostavka_shipping()->load_template( 'views/html-box-packing.php', array(
			'boxes' => $this->get_option( 'boxes', array() )
		) );

		return ob_get_clean();
	}

	public function validate_box_packing_field() {

		$post_data = $this->get_post_data();

		$boxes_name    = isset( $post_data['boxes_name'] ) ? $post_data['boxes_name'] : array();
		$boxes_length  = isset( $post_data['boxes_length'] ) ? $post_data['boxes_length'] : array();
		$boxes_width   = isset( $post_data['boxes_width'] ) ? $post_data['boxes_width'] : array();
		$boxes_height  = isset( $post_data['boxes_height'] ) ? $post_data['boxes_height'] : array();
		$boxes_weight  = isset( $post_data['boxes_weight'] ) ? $post_data['boxes_weight'] : array();
		$boxes_cost    = isset( $post_data['boxes_cost'] ) ? $post_data['boxes_cost'] : array();
		$boxes_enabled = isset( $post_data['boxes_enabled'] ) ? $post_data['boxes_enabled'] : array();

		$boxes = array();

		if ( ! empty( $boxes_length ) && sizeof( $boxes_length ) > 0 ) {

			for ( $i = 0; $i <= max( array_keys( $boxes_length ) ); $i ++ ) {

				if ( ! isset( $boxes_length[ $i ] ) ) {
					continue;
				}

				if ( $boxes_length[ $i ] && $boxes_width[ $i ] && $boxes_height[ $i ] && $boxes_name[ $i ] ) {

					$boxes[] = array(
						'name'    => wc_clean( $boxes_name[ $i ] ),
						'length'  => floatval( $boxes_length[ $i ] ),
						'width'   => floatval( $boxes_width[ $i ] ),
						'height'  => floatval( $boxes_height[ $i ] ),
						'weight'  => floatval( $boxes_weight[ $i ] ),
						'cost'    => wc_clean( $boxes_cost[ $i ] ),
						'enabled' => isset( $boxes_enabled[ $i ] )
					);
				}
			}
		}

		foreach ( wc_edostavka_get_carton_boxes() as $box_key => $box ) {

			$boxes[ $box_key ] = array(
				'enabled' => isset( $boxes_enabled[ $box_key ] ),
				'cost'    => isset( $boxes_cost[ $box_key ] ),
			);
		}

		return $boxes;
	}

	function validate_dadata_fields( $value ) {

		if ( ! is_null( $value ) ) {

			$post_data = $this->get_post_data();
			$errors    = array();

			if ( empty( $post_data['woocommerce_edostavka_dadata_token'] ) ) {
				$errors[] = 'Вы не указали токен от API Dadata';
			}

			if ( empty( $post_data['woocommerce_edostavka_dadata_secret'] ) ) {
				$errors[] = 'Вы не указали секретный ключ от API Dadata';
			}

			if ( empty( $errors ) ) {

				try {

					$dadata_api = new WC_Edostavka_Dadata_API(
						wc_clean( $post_data['woocommerce_edostavka_dadata_token'] ),
						wc_clean( $post_data['woocommerce_edostavka_dadata_secret'] )
					);

					$balance = $dadata_api->get_balance( true );

					if ( ! $balance->get_balance() || $balance->get_balance() < 1 ) {
						$errors[] = 'У вас нулевой баланс на счету DADATA. Перед использованием данной опции, пополните баланс и повторите попытку.';
					}

				} catch ( Woodev_API_Exception $e ) {
					$errors[] = $e->getMessage();
					wc_edostavka_shipping()->log( $e->getMessage() );
				}
			}

			if ( ! empty( $errors ) ) {

				wc_edostavka_shipping()->get_admin_notice_handler()->add_admin_notice(
					sprintf(
						'Опции автоподсказок не доступны для использования. %s: %s.',
						_n( 'Reason', 'Reasons', count( $errors ), 'woocommerce-edostavka' ),
						Woodev_Helper::list_array_items( $errors, 'и' )
					),
					'edostavka_dadata_empty_value',
					array(
						'notice_class' => 'notice-warning'
					)
				);

				$value = null;
			}
		}

		return $this->validate_checkbox_field( null, $value );
	}

	public function is_configured() {
		return ! empty( $this->get_option( 'api_login' ) ) && ! empty( $this->get_option( 'api_password' ) );
	}

	protected function set_verify_code( $field ) {

		if ( ! wc_edostavka_shipping()->get_license_instance()->is_active() && 'title' !== $field['type'] ) {
			if ( ! isset( $field['class'] ) ) {
				$field['class'] = '';
			}
			$field['class'] .= ' woodev-modal';
		}

		return $field;
	}

	public function get_access_token( $use_cache = false ) {
		wc_edostavka_api_get_access_token( $use_cache );
	}

	public function clear_transients() {
		delete_transient( WC_CDEK_SHIPPING_ACEESS_TOKEN_TRANSIENT_NAME );
	}

}
