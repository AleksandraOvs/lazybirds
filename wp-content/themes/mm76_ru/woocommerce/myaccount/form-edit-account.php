<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' ); ?>

<div class="sec-title">Личная информация</div>

<form class="woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

	<div class="row_2">
		<p class="woocommerce-form-row woocommerce-form-row--first form-row">
			<label for="account_first_name"><?php esc_html_e( 'First name', 'woocommerce' ); ?></label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" />
		</p>
		<p class="woocommerce-form-row woocommerce-form-row--last form-row">
			<label for="account_last_name"><?php esc_html_e( 'Last name', 'woocommerce' ); ?></label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" />
		</p>
	</div>

	<div class="row_2">
		<p class="woocommerce-form-row woocommerce-form-row--first form-row">
			<label for="account_surname">Отчество</label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_surname" id="account_surname" value="<?php echo get_field( 'surname', 'user_'.$user->ID ); ?>" />
		</p>
		<p class="woocommerce-form-row woocommerce-form-row--last form-row">
			<label for="account_bdate">Дата рождения</label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_bdate" id="account_bdate" value="<?php echo get_field( 'bdate', 'user_'.$user->ID ); ?>"  <?= (empty(get_field( 'bdate', 'user_'.$user->ID )) ? '' : 'readonly'); ?>/>
		</p>
	</div>

	<div class="row_2">
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row">
			<label for="account_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?></label>
			<input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" />
		</p>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row">
			<label for="account_phone">Мобильный телефон</label>
			<input type="text" class="woocommerce-Input woocommerce-Input--email input-text" name="account_phone" id="account_phone" value="<?php echo get_field( 'phone', 'user_'.$user->ID ); ?>" />
		</p>
	</div>

	<div class="row_user">
		<label>
			<input type="checkbox" name="policy" value="yes" <?= (get_field( 'policy', 'user_'.$user->ID ) == 1 ? 'checked' : ''); ?>>
			Личные данные используются для управления доступом в личный кабинет и других целей, описанных в <a href="#">политике конфиденциальности</a>.
		</label>
	</div>

	<? /* ?><p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="account_display_name"><?php esc_html_e( 'Display name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" /> <span><em><?php esc_html_e( 'This will be how your name will be displayed in the account section and in reviews', 'woocommerce' ); ?></em></span>
	</p>
	<div class="clear"></div><? */ ?>

	<?php do_action( 'woocommerce_edit_account_form' ); ?>

	<p>
		<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
		<button type="submit" class="woocommerce-Button button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="save_account_details" value="Сохранить">Сохранить</button>
		<input type="hidden" name="action" value="save_account_details" />
	</p>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>

<div class="section_edit_account">

	<div class="sec-title">Адрес доставки</div>

	<form action="" class="edit-account ajax-address-user">
		<div class="row_2">
			<p class="woocommerce-form-row">
				<label for="billing_city">Населённый пункт</label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_city" id="billing_city" value="<?php echo get_field( 'billing_city', 'user_'.$user->ID ); ?>" />
			</p>
			<p class="woocommerce-form-row">
				<label for="billing_street">Улица</label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_street" id="billing_street" value="<?php echo get_field( 'billing_street', 'user_'.$user->ID ); ?>" />
			</p>
		</div>

		<div class="row_2">
			<div class="row-2">
				<div>
					<label for="billing_house">Дом</label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_house" id="billing_house" value="<?php echo get_field( 'billing_house', 'user_'.$user->ID ); ?>" />
				</div>
				<div>
					<label for="billing_flat">Квартира</label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_flat" id="billing_flat" value="<?php echo get_field( 'billing_flat', 'user_'.$user->ID ); ?>" />
				</div>
			</div>
		</div> 

		<input type="hidden" name="action" value="user_ajax_edit">
		<input type="hidden" name="userid" value="<?= $user->ID; ?>">

		<button type="submit" class="woocommerce-Button button" name="save_account_delivery" value="Сохранить">Сохранить</button>

	</form>

</div>

<div class="section_edit_account">

	<div class="sec-title">Изменить пароль</div>

	<form action="" class="edit-account ajax-pass-user">

		<div class="row_2">
			<p class="woocommerce-form-row">
				<label for="password">Пароль</label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="password" value="" />
			</p>
			<p class="woocommerce-form-row">
				<label for="password_check">Подтвердите пароль</label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="password_check" id="password_check" value="" />
			</p>
		</div>

		<input type="hidden" name="userid" value="<?= $user->ID; ?>">
		<input type="hidden" name="action" value="userpass_ajax_edit">

		<button type="submit" class="woocommerce-Button button" name="save_account_delivery" value="Сохранить">Сохранить пароль</button>

	</form>

</div>