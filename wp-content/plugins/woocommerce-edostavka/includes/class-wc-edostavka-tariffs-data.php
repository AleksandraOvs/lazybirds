<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_Edostavka_Tariffs_Data {

	public static function get_all_tariffs() {
		return array(
			'shop'		=> self::get_shop_tariffs(),
			'delivery'	=> self::get_delivery_tariffs(),
			'china'		=> self::get_china_tariffs()
		);
	}

	public static function get_groups() {
		return array(
			'shop'		=> 'Тарифы для интернет-магазинов',
			'delivery'	=> 'Тарифы для обычной доставки',
			'china'		=> 'Тарифы Китайский экспресс'
		);
	}

	public static function get_group_name( $type = '' ) {
		$groups = self::get_groups();
		return isset( $groups[ $type ] ) ? $groups[ $type ] : null;
	}

	public static function get_tariff_types() {
		return array(
			'door_door'			=> 'Дверь-Дверь',
			'door_stock'		=> 'Дверь-Склад',
			'stock_stock'		=> 'Склад-Склад',
			'stock_door'		=> 'Склад-Дверь',
			'stock_postamat'	=> 'Склад-Постамат',
			'door_postamat'		=> 'Дверь-Постамат'
		);
	}

	public static function get_tariff_type_name( $type = '' ) {
		$types = self::get_tariff_types();
		return isset( $types[ $type ] ) ? $types[ $type ] : null;
	}

	public static function get_merged_tariffs() {
		return array_merge( self::get_shop_tariffs(), self::get_delivery_tariffs(), self::get_china_tariffs() );
	}

	public static function get_tariff_by_code( $code ) {

		$result = array();

		foreach( self::get_merged_tariffs() as $tariff ) {
			if( $tariff['code'] == $code ) {
				$result = array_merge( $tariff, array( 'type_name' => self::get_tariff_type_name( $tariff['type'] ) ) );
				break;
			}
		}

		return $result;
	}

	public static function get_shop_tariffs() {
		return apply_filters( 'wc_edostavka_shop_tariffs_data', array(
			array(
				'code'			=> 7,
				'name'			=> 'Международный экспресс документы дверь-дверь',
				'type'			=> 'door_door',
				'max_weight'	=> 5,
				'service_name'	=> 'Международный экспресс',
				'description'	=> 'Экспресс-доставка за/из-за границы документов и писем.'
			),
			array(
				'code'			=> 8,
				'name'			=> 'Международный экспресс грузы дверь-дверь',
				'type'			=> 'door_door',
				'max_weight'	=> 30,
				'service_name'	=> 'Международный экспресс',
				'description'	=> 'Экспресс-доставка за/из-за границы грузов и посылок до 30 кг.'
			),
			array(
				'code'			=> 136,
				'name'			=> 'Посылка склад-склад',
				'type'			=> 'stock_stock',
				'max_weight'	=> 30,
				'service_name'	=> 'Посылка',
				'description'	=> 'Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.'
			),
			array(
				'code'			=> 137,
				'name'			=> 'Посылка склад-дверь',
				'type'			=> 'stock_door',
				'max_weight'	=> 30,
				'service_name'	=> 'Посылка',
				'description'	=> 'Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.'
			),
			array(
				'code'			=> 138,
				'name'			=> 'Посылка дверь-склад',
				'type'			=> 'door_stock',
				'max_weight'	=> 30,
				'service_name'	=> 'Посылка',
				'description'	=> 'Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.'
			),
			array(
				'code'			=> 139,
				'name'			=> 'Посылка дверь-дверь',
				'type'			=> 'door_door',
				'max_weight'	=> 30,
				'service_name'	=> 'Посылка',
				'description'	=> 'Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.'
			),
			array(
				'code'			=> 184,
				'name'			=> 'E-com Standard дверь-дверь',
				'type'			=> 'door_door',
				'max_weight'	=> 0,
				'service_name'	=> 'E-com Standard',
				'description'	=> 'Сервис по доставке товаров из-за рубежа с услугами по таможенному оформлению.'
			),
			array(
				'code'			=> 185,
				'name'			=> 'E-com Standard склад-склад',
				'type'			=> 'stock_stock',
				'max_weight'	=> 0,
				'service_name'	=> 'E-com Standard',
				'description'	=> 'Сервис по доставке товаров из-за рубежа с услугами по таможенному оформлению.'
			),
			array(
				'code'			=> 186,
				'name'			=> 'E-com Standard склад-дверь',
				'type'			=> 'stock_door',
				'max_weight'	=> 0,
				'service_name'	=> 'E-com Standard',
				'description'	=> 'Сервис по доставке товаров из-за рубежа с услугами по таможенному оформлению.'
			),
			array(
				'code'			=> 187,
				'name'			=> 'E-com Standard дверь-склад',
				'type'			=> 'door_stock',
				'max_weight'	=> 0,
				'service_name'	=> 'E-com Standard',
				'description'	=> 'Сервис по доставке товаров из-за рубежа с услугами по таможенному оформлению.'
			),
			array(
				'code'			=> 497,
				'name'			=> 'E-com Standard дверь-постамат',
				'type'			=> 'door_postamat',
				'max_weight'	=> 0,
				'service_name'	=> 'E-com Standard',
				'description'	=> 'Сервис по доставке товаров из-за рубежа с услугами по таможенному оформлению.'
			),
			array(
				'code'			=> 498,
				'name'			=> 'E-com Standard склад-постамат',
				'type'			=> 'stock_postamat',
				'max_weight'	=> 0,
				'service_name'	=> 'E-com Standard',
				'description'	=> 'Сервис по доставке товаров из-за рубежа с услугами по таможенному оформлению.'
			),
			array(
				'code'			=> 231,
				'name'			=> 'Экономичная посылка дверь-дверь',
				'type'			=> 'door_door',
				'max_weight'	=> 50,
				'service_name'	=> 'Экономичная посылка',
				'description'	=> 'Услуга действует по направлениям из Москвы в подразделения СДЭК, находящиеся за Уралом и в Крым.'
			),
			array(
				'code'			=> 232,
				'name'			=> 'Экономичная посылка дверь-склад',
				'type'			=> 'door_stock',
				'max_weight'	=> 50,
				'service_name'	=> 'Экономичная посылка',
				'description'	=> 'Услуга действует по направлениям из Москвы в подразделения СДЭК, находящиеся за Уралом и в Крым.'
			),
			array(
				'code'			=> 233,
				'name'			=> 'Экономичная посылка склад-дверь',
				'type'			=> 'stock_door',
				'max_weight'	=> 50,
				'service_name'	=> 'Экономичная посылка',
				'description'	=> 'Услуга действует по направлениям из Москвы в подразделения СДЭК, находящиеся за Уралом и в Крым.'
			),
			array(
				'code'			=> 234,
				'name'			=> 'Экономичная посылка склад-склад',
				'type'			=> 'stock_stock',
				'max_weight'	=> 50,
				'service_name'	=> 'Экономичная посылка',
				'description'	=> 'Услуга действует по направлениям из Москвы в подразделения СДЭК, находящиеся за Уралом и в Крым.'
			),
			array(
				'code'			=> 291,
				'name'			=> 'E-com Express склад-склад',
				'type'			=> 'stock_stock',
				'max_weight'	=> 500,
				'service_name'	=> 'E-com Express',
				'description'	=> 'Самая быстрая экспресс-доставка в режиме авиа. Сервис по доставке товаров из-за рубежа с услугами по таможенному оформлению.'
			),
			array(
				'code'			=> 293,
				'name'			=> 'E-com Express дверь-дверь',
				'type'			=> 'door_door',
				'max_weight'	=> 500,
				'service_name'	=> 'E-com Express',
				'description'	=> 'Самая быстрая экспресс-доставка в режиме авиа. Сервис по доставке товаров из-за рубежа с услугами по таможенному оформлению.'
			),
			array(
				'code'			=> 294,
				'name'			=> 'E-com Express склад-дверь',
				'type'			=> 'stock_door',
				'max_weight'	=> 500,
				'service_name'	=> 'E-com Express',
				'description'	=> 'Самая быстрая экспресс-доставка в режиме авиа. Сервис по доставке товаров из-за рубежа с услугами по таможенному оформлению.'
			),
			array(
				'code'			=> 295,
				'name'			=> 'E-com Express дверь-склад',
				'type'			=> 'door_stock',
				'max_weight'	=> 500,
				'service_name'	=> 'E-com Express',
				'description'	=> 'Самая быстрая экспресс-доставка в режиме авиа. Сервис по доставке товаров из-за рубежа с услугами по таможенному оформлению.'
			),
			array(
				'code'			=> 509,
				'name'			=> 'E-com Express дверь-постамат',
				'type'			=> 'door_postamat',
				'max_weight'	=> 500,
				'service_name'	=> 'E-com Express',
				'description'	=> 'Самая быстрая экспресс-доставка в режиме авиа. Сервис по доставке товаров из-за рубежа с услугами по таможенному оформлению.'
			),
			array(
				'code'			=> 510,
				'name'			=> 'E-com Express склад-постамат',
				'type'			=> 'stock_postamat',
				'max_weight'	=> 500,
				'service_name'	=> 'E-com Express',
				'description'	=> 'Самая быстрая экспресс-доставка в режиме авиа. Сервис по доставке товаров из-за рубежа с услугами по таможенному оформлению.'
			),
			array(
				'code'			=> 366,
				'name'			=> 'Посылка дверь-постамат',
				'type'			=> 'door_postamat',
				'max_weight'	=> 30,
				'service_name'	=> 'Посылка',
				'description'	=> 'Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю. До постаматов.'
			),
			array(
				'code'			=> 368,
				'name'			=> 'Посылка склад-постамат',
				'type'			=> 'stock_postamat',
				'max_weight'	=> 30,
				'service_name'	=> 'Посылка',
				'description'	=> 'Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю. До постаматов.'
			),
			array(
				'code'			=> 378,
				'name'			=> 'Экономичная посылка склад-постамат',
				'type'			=> 'stock_postamat',
				'max_weight'	=> 50,
				'service_name'	=> 'Экономичная посылка',
				'description'	=> 'Услуга действует по направлениям из Москвы в подразделения СДЭК, находящиеся за Уралом и в Крым.'
			)
		) );
	}

	public static function get_delivery_tariffs() {
		return apply_filters( 'wc_edostavka_delivery_tariffs_data', array(
			array(
				'code'			=> 3,
				'name'			=> 'Супер-экспресс до 18',
				'type'			=> 'door_door',
				'max_weight'	=> 30,
				'service_name'	=> 'Срочная доставка',
				'description'	=> 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу.'
			),
			array(
				'code'			=> 57,
				'name'			=> 'Супер-экспресс до 9',
				'type'			=> 'door_door',
				'max_weight'	=> 30,
				'service_name'	=> 'Срочная доставка',
				'description'	=> 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу (доставка за 1-2 суток).'
			),
			array(
				'code'			=> 58,
				'name'			=> 'Супер-экспресс до 10',
				'type'			=> 'door_door',
				'max_weight'	=> 30,
				'service_name'	=> 'Срочная доставка',
				'description'	=> 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу (доставка за 1-2 суток).'
			),
			array(
				'code'			=> 59,
				'name'			=> 'Супер-экспресс до 12',
				'type'			=> 'door_door',
				'max_weight'	=> 30,
				'service_name'	=> 'Срочная доставка',
				'description'	=> 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу (доставка за 1-2 суток).'
			),
			array(
				'code'			=> 60,
				'name'			=> 'Супер-экспресс до 14',
				'type'			=> 'door_door',
				'max_weight'	=> 30,
				'service_name'	=> 'Срочная доставка',
				'description'	=> 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу (доставка за 1-2 суток).'
			),
			array(
				'code'			=> 61,
				'name'			=> 'Супер-экспресс до 16',
				'type'			=> 'door_door',
				'max_weight'	=> 30,
				'service_name'	=> 'Срочная доставка',
				'description'	=> 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу (доставка за 1-2 суток).'
			),
			array(
				'code'			=> 777,
				'name'			=> 'Супер-экспресс до 12',
				'type'			=> 'door_stock',
				'max_weight'	=> 30,
				'service_name'	=> 'Срочная доставка',
				'description'	=> 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу (доставка за 1-2 суток).'
			),
			array(
				'code'			=> 786,
				'name'			=> 'Супер-экспресс до 14',
				'type'			=> 'door_stock',
				'max_weight'	=> 30,
				'service_name'	=> 'Срочная доставка',
				'description'	=> 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу (доставка за 1-2 суток).'
			),
			array(
				'code'			=> 795,
				'name'			=> 'Супер-экспресс до 16',
				'type'			=> 'door_stock',
				'max_weight'	=> 30,
				'service_name'	=> 'Срочная доставка',
				'description'	=> 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу (доставка за 1-2 суток).'
			),
			array(
				'code'			=> 804,
				'name'			=> 'Супер-экспресс до 18',
				'type'			=> 'door_stock',
				'max_weight'	=> 30,
				'service_name'	=> 'Срочная доставка',
				'description'	=> 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу (доставка за 1-2 суток).'
			),
			array(
				'code'			=> 778,
				'name'			=> 'Супер-экспресс до 12',
				'type'			=> 'stock_door',
				'max_weight'	=> 30,
				'service_name'	=> 'Срочная доставка',
				'description'	=> 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу (доставка за 1-2 суток).'
			),
			array(
				'code'			=> 787,
				'name'			=> 'Супер-экспресс до 14',
				'type'			=> 'stock_door',
				'max_weight'	=> 30,
				'service_name'	=> 'Срочная доставка',
				'description'	=> 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу (доставка за 1-2 суток).'
			),
			array(
				'code'			=> 796,
				'name'			=> 'Супер-экспресс до 16',
				'type'			=> 'stock_door',
				'max_weight'	=> 30,
				'service_name'	=> 'Срочная доставка',
				'description'	=> 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу (доставка за 1-2 суток).'
			),
			array(
				'code'			=> 805,
				'name'			=> 'Супер-экспресс до 18',
				'type'			=> 'stock_door',
				'max_weight'	=> 30,
				'service_name'	=> 'Срочная доставка',
				'description'	=> 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу (доставка за 1-2 суток).'
			),
			array(
				'code'			=> 779,
				'name'			=> 'Супер-экспресс до 12',
				'type'			=> 'stock_stock',
				'max_weight'	=> 30,
				'service_name'	=> 'Срочная доставка',
				'description'	=> 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу (доставка за 1-2 суток).'
			),
			array(
				'code'			=> 788,
				'name'			=> 'Супер-экспресс до 14',
				'type'			=> 'stock_stock',
				'max_weight'	=> 30,
				'service_name'	=> 'Срочная доставка',
				'description'	=> 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу (доставка за 1-2 суток).'
			),
			array(
				'code'			=> 797,
				'name'			=> 'Супер-экспресс до 16',
				'type'			=> 'stock_stock',
				'max_weight'	=> 30,
				'service_name'	=> 'Срочная доставка',
				'description'	=> 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу (доставка за 1-2 суток).'
			),
			array(
				'code'			=> 806,
				'name'			=> 'Супер-экспресс до 18',
				'type'			=> 'stock_stock',
				'max_weight'	=> 30,
				'service_name'	=> 'Срочная доставка',
				'description'	=> 'Срочная доставка документов и грузов «из рук в руки» по России к определенному часу (доставка за 1-2 суток).'
			),
			array(
				'code'			=> 62,
				'name'			=> 'Магистральный экспресс склад-склад',
				'type'			=> 'stock_stock',
				'max_weight'	=> 0,
				'service_name'	=> 'Экономичная доставка',
				'description'	=> 'Быстрая экономичная доставка грузов по России.'
			),
			array(
				'code'			=> 63,
				'name'			=> 'Магистральный супер-экспресс склад-склад',
				'type'			=> 'stock_stock',
				'max_weight'	=> 0,
				'service_name'	=> 'Экономичная доставка',
				'description'	=> 'Быстрая экономичная доставка грузов к определенному часу'
			),
			array(
				'code'			=> 121,
				'name'			=> 'Магистральный экспресс дверь-дверь',
				'type'			=> 'door_door',
				'max_weight'	=> 0,
				'service_name'	=> 'Экономичная доставка',
				'description'	=> 'Быстрая экономичная доставка грузов по России.'
			),
			array(
				'code'			=> 122,
				'name'			=> 'Магистральный экспресс склад-дверь',
				'type'			=> 'stock_door',
				'max_weight'	=> 0,
				'service_name'	=> 'Экономичная доставка',
				'description'	=> 'Быстрая экономичная доставка грузов по России.'
			),
			array(
				'code'			=> 123,
				'name'			=> 'Магистральный экспресс дверь-склад',
				'type'			=> 'door_stock',
				'max_weight'	=> 0,
				'service_name'	=> 'Экономичная доставка',
				'description'	=> 'Быстрая экономичная доставка грузов по России.'
			),
			array(
				'code'			=> 124,
				'name'			=> 'Магистральный супер-экспресс дверь-дверь',
				'type'			=> 'door_door',
				'max_weight'	=> 0,
				'service_name'	=> 'Экономичная доставка',
				'description'	=> 'Быстрая экономичная доставка грузов к определенному часу.'
			),
			array(
				'code'			=> 125,
				'name'			=> 'Магистральный супер-экспресс склад-дверь',
				'type'			=> 'stock_door',
				'max_weight'	=> 0,
				'service_name'	=> 'Экономичная доставка',
				'description'	=> 'Быстрая экономичная доставка грузов к определенному часу.'
			),
			array(
				'code'			=> 126,
				'name'			=> 'Магистральный супер-экспресс дверь-склад',
				'type'			=> 'door_stock',
				'max_weight'	=> 0,
				'service_name'	=> 'Экономичная доставка',
				'description'	=> 'Быстрая экономичная доставка грузов к определенному часу.'
			),
			array(
				'code'			=> 480,
				'name'			=> 'Экспресс дверь-дверь',
				'type'			=> 'door_door',
				'max_weight'	=> 0,
				'service_name'	=> 'Экспресс',
				'description'	=> 'Классическая экспресс-доставка документов и грузов по стандартным срокам доставки внутри страны (Россия, Белоруссия, Армения, Киргизия, Казахстан). Также действует по направлениям между странами таможенного союза (Россия, Белоруссия, Армения, Киргизия, Казахстан).'
			),
			array(
				'code'			=> 481,
				'name'			=> 'Экспресс дверь-склад',
				'type'			=> 'door_stock',
				'max_weight'	=> 0,
				'service_name'	=> 'Экспресс',
				'description'	=> 'Классическая экспресс-доставка документов и грузов по стандартным срокам доставки внутри страны (Россия, Белоруссия, Армения, Киргизия, Казахстан). Также действует по направлениям между странами таможенного союза (Россия, Белоруссия, Армения, Киргизия, Казахстан).'
			),
			array(
				'code'			=> 482,
				'name'			=> 'Экспресс склад-дверь',
				'type'			=> 'stock_door',
				'max_weight'	=> 0,
				'service_name'	=> 'Экспресс',
				'description'	=> 'Классическая экспресс-доставка документов и грузов по стандартным срокам доставки внутри страны (Россия, Белоруссия, Армения, Киргизия, Казахстан). Также действует по направлениям между странами таможенного союза (Россия, Белоруссия, Армения, Киргизия, Казахстан).'
			),
			array(
				'code'			=> 483,
				'name'			=> 'Экспресс склад-склад',
				'type'			=> 'stock_stock',
				'max_weight'	=> 0,
				'service_name'	=> 'Экспресс',
				'description'	=> 'Классическая экспресс-доставка документов и грузов по стандартным срокам доставки внутри страны (Россия, Белоруссия, Армения, Киргизия, Казахстан). Также действует по направлениям между странами таможенного союза (Россия, Белоруссия, Армения, Киргизия, Казахстан).'
			),
			array(
				'code'			=> 485,
				'name'			=> 'Экспресс дверь-постамат',
				'type'			=> 'door_postamat',
				'max_weight'	=> 0,
				'service_name'	=> 'Экспресс',
				'description'	=> 'Классическая экспресс-доставка документов и грузов по стандартным срокам доставки внутри страны (Россия, Белоруссия, Армения, Киргизия, Казахстан). Также действует по направлениям между странами таможенного союза (Россия, Белоруссия, Армения, Киргизия, Казахстан).'
			),
			array(
				'code'			=> 486,
				'name'			=> 'Экспресс склад-постамат',
				'type'			=> 'stock_postamat',
				'max_weight'	=> 0,
				'service_name'	=> 'Экспресс',
				'description'	=> 'Классическая экспресс-доставка документов и грузов по стандартным срокам доставки внутри страны (Россия, Белоруссия, Армения, Киргизия, Казахстан). Также действует по направлениям между странами таможенного союза (Россия, Белоруссия, Армения, Киргизия, Казахстан).'
			),
			array(
				'code'			=> 533,
				'name'			=> 'СДЭК документы дверь-дверь',
				'type'			=> 'door_door',
				'max_weight'	=> 0.3,
				'service_name'	=> 'СДЭК документы',
				'description'	=> 'Экспресс доставка документов по всей РФ и ЕАЭС, со спец. условием от 90 документов за 90 дней.'
			),
			array(
				'code'			=> 534,
				'name'			=> 'СДЭК документы дверь-склад',
				'type'			=> 'door_stock',
				'max_weight'	=> 0.3,
				'service_name'	=> 'СДЭК документы',
				'description'	=> 'Экспресс доставка документов по всей РФ и ЕАЭС, со спец. условием от 90 документов за 90 дней.'
			),
			array(
				'code'			=> 535,
				'name'			=> 'СДЭК документы склад-дверь',
				'type'			=> 'stock_door',
				'max_weight'	=> 0.3,
				'service_name'	=> 'СДЭК документы',
				'description'	=> 'Экспресс доставка документов по всей РФ и ЕАЭС, со спец. условием от 90 документов за 90 дней.'
			),
			array(
				'code'			=> 536,
				'name'			=> 'СДЭК документы склад-склад',
				'type'			=> 'stock_stock',
				'max_weight'	=> 0.3,
				'service_name'	=> 'СДЭК документы',
				'description'	=> 'Экспресс доставка документов по всей РФ и ЕАЭС, со спец. условием от 90 документов за 90 дней.'
			),
			array(
				'code'			=> 751,
				'name'			=> 'Сборный груз склад-склад',
				'type'			=> 'stock_stock',
				'max_weight'	=> 20000,
				'service_name'	=> 'Сборный груз',
				'description'	=> 'Экономичная наземная доставка сборных грузов.'
			)
		) );
	}

	public static function get_china_tariffs() {
		return apply_filters( 'wc_edostavka_china_tariffs_data', array(
			array(
				'code'			=> 243,
				'name'			=> 'Китайский экспресс склад-склад',
				'type'			=> 'stock_stock',
				'max_weight'	=> 0,
				'service_name'	=> 'Китайский Экспресс',
				'description'	=> 'Услуга по доставке из Китая в Россию, Белоруссию и Казахстан.'
			),
			array(
				'code'			=> 245,
				'name'			=> 'Китайский экспресс дверь-дверь',
				'type'			=> 'door_door',
				'max_weight'	=> 0,
				'service_name'	=> 'Китайский Экспресс',
				'description'	=> 'Услуга по доставке из Китая в Россию, Белоруссию и Казахстан.'
			),
			array(
				'code'			=> 246,
				'name'			=> 'Китайский экспресс склад-дверь',
				'type'			=> 'stock_door',
				'max_weight'	=> 0,
				'service_name'	=> 'Китайский Экспресс',
				'description'	=> 'Услуга по доставке из Китая в Россию, Белоруссию и Казахстан.'
			),
			array(
				'code'			=> 247,
				'name'			=> 'Китайский экспресс дверь-склад',
				'type'			=> 'door_stock',
				'max_weight'	=> 0,
				'service_name'	=> 'Китайский Экспресс',
				'description'	=> 'Услуга по доставке из Китая в Россию, Белоруссию и Казахстан.'
			)
		) );
	}
}
