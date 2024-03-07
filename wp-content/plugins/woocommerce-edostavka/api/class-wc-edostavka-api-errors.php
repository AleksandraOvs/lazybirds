<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WD_Edostavka_Shipping_API_Errors {

	public static function get_error_text( $error = '' ) {
		$errors = self::get_errors();
		return isset( $errors[ $error ] ) ? $errors[ $error ] : $error;
	}

	public static function get_errors() {
		return array(
			'v2_internal_error'						=> 'Запрос выполнился с системной ошибкой',
			'v2_similar_request_still_processed'	=> 'Предыдущий запрос такого же типа над этой же сущностью еще не выполнился',
			'v2_bad_request'						=> 'Передан некорректный запрос',
			'v2_invalid_format'						=> 'Передано некорректное значение',
			'v2_field_is_empty'						=> 'Не передано обязательное поле',
			'v2_parameters_empty'					=> 'Все параметры запроса пустые или не переданы',
			'v2_invalid_value_type'					=> 'Передан некорректный тип данных',
			'v2_entity_not_found'					=> 'Сущность (заказ, заявка и т.д.) с указанным идентификатором не существует, либо удалена',
			'v2_entity_forbidden'					=> 'Сущность (заказ, заявка и т.д.) с указанным идентификатором существует, но принадлежит другому клиенту',
			'v2_entity_invalid'						=> 'Сущность (заказ, заявка и т.д.) с указанным идентификатором существует, но некорректна',
			'v2_order_not_found'					=> 'Заказ с указанным номером не существует, либо удален',
			'v2_order_forbidden'					=> 'Заказ с указанным номером существует, но принадлежит другому клиенту',
			'v2_order_number_empty'					=> 'Не переданы номер и идентификатор заказа',
			'v2_shipment_address_multivalued'		=> 'Одновременно переданы ПВЗ отправителя и адрес отправителя. Необходимо указать 1 параметр',
			'v2_delivery_address_multivalued'		=> 'Одновременно переданы ПВЗ получателя и адрес получателя. Необходимо указать 1 параметр.',
			'v2_sender_location_not_recognized'		=> 'Не удалось определить город отправителя',
			'v2_recipient_location_not_recognized'	=> 'Не удалось определить город получателя',
			'v2_number_items_is_more_126'			=> 'Количество позиций товаров в заказе свыше 126',
			'orders_number_is_empty'				=> 'Все заказы с указанными номерами и идентификаторами некорректны',
			'shipment_location_is_not_recognized'	=> 'Передан некорректный код ПВЗ отправителя',
			'delivery_location_is_not_recognized'	=> 'Передан некорректный код ПВЗ получателя',
			'v2_entity_not_found_im_number'			=> 'Сущность с указанным идентификатором не существует, либо удалена'
		);
	}
}

