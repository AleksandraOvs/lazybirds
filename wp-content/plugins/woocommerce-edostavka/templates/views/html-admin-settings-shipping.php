<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<script type="text/html" id="tmpl-wc-edostavka-tariff-info">
	<p>Название тарифа: <strong>{{ data.name }}</strong>.</p>
	<p>Группа тарифа: {{ data.service_name }}.</p>
	<p>Тип тарифа: {{ data.type_name }}</p>
	<# if ( data.max_weight && data.max_weight > 0 ) { #>
	<p>Максимально допустимый вес: {{ data.max_weight }} кг.</p>
	<# } #>
	<# if ( data.min_weight && data.min_weight > 0 ) { #>
	<p>Минимально допустимый вес: {{ data.min_weight }} кг.</p>
	<# } #>	
	<p>Описание тарифа: {{ data.description }}.</p>
</script>