<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="cdek-method-description">
	<p>Название тарифа: <strong><?php echo $name; ?></strong>.</p>
	<p>Группа тарифа: <?php echo $service_name; ?>.</p>
	<p>Тип тарифа: <?php echo $type_name; ?>.</p>
	<?php if( isset( $max_weight ) && $max_weight > 0 ) : ?>
		<p>Максимально допустимый вес: <?php echo $max_weight; ?> кг.</p>
	<?php endif;?>
	<?php if( isset( $min_weight ) && $min_weight > 0 ) : ?>
		<p>Максимально допустимый вес: <?php echo $min_weight; ?> кг.</p>
	<?php endif;?>
	<p>Описание тарифа: <?php echo $description; ?>.</p>
</div>
