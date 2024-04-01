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

defined('ABSPATH') || exit;

do_action('woocommerce_before_edit_account_form'); ?>

<h2 class="title-page my-account-edit">Личная информация</h2>

<h3>О тебе</h3>
<form class="woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action('woocommerce_edit_account_form_tag'); ?>>

	<?php do_action('woocommerce_edit_account_form_start'); ?>
	<div class="edit-account__inputs">


		<p class="woocommerce-form-row woocommerce-form-row--first form-row">
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" placeholder="Имя" name="account_first_name" placeholder="<?php esc_html_e('First name', 'woocommerce'); ?>" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr($user->first_name); ?>" />
		</p>
		<p class="woocommerce-form-row woocommerce-form-row--last form-row">
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" placeholder="Фамилия" name="account_last_name" placeholder="<?php esc_html_e('Last name', 'woocommerce'); ?>" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr($user->last_name); ?>" />
		</p>


		<p class="woocommerce-form-row woocommerce-form-row--first form-row">
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_surname" placeholder="Отчество" id="account_surname" value="<?php echo get_field('surname', 'user_' . $user->ID); ?>" />
		</p>
		<p class="woocommerce-form-row woocommerce-form-row--last form-row">
			<label style="display: none;" for="account_bdate">Дата рождения</label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_bdate" placeholder="Дата рождения" id="account_bdate" value="<?php echo get_field('bdate', 'user_' . $user->ID); ?>" <?= (empty(get_field('bdate', 'user_' . $user->ID)) ? '' : 'readonly'); ?> />
		</p>

		<p class="woocommerce-form-row woocommerce-form-row--wide form-row">
			<label style="display: none;" for="account_email"><?php esc_html_e('Email address', 'woocommerce'); ?></label>
			<input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" placeholder="<?php esc_html_e('Email address', 'woocommerce'); ?>" id="account_email" autocomplete="email" value="<?php echo esc_attr($user->user_email); ?>" />
		</p>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row">
			<label style="display: none;" for="account_phone">Мобильный телефон</label>
			<input type="text" placeholder="Мобильный телефон" class="woocommerce-Input woocommerce-Input--email input-text" name="account_phone" id="account_phone" value="<?php echo get_field('phone', 'user_' . $user->ID); ?>" />
		</p>
	</div>
	<div class="row_user">
		<input type="checkbox" name="policy" value="yes" <?= (get_field('policy', 'user_' . $user->ID) == 1 ? 'checked' : ''); ?>>
		<label>
			Личные данные используются для управления доступом в личный кабинет и других целей, описанных в <a href="#">политике конфиденциальности</a>.
		</label>
	</div>

	<? /* ?><p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="account_display_name"><?php esc_html_e( 'Display name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" /> <span><em><?php esc_html_e( 'This will be how your name will be displayed in the account section and in reviews', 'woocommerce' ); ?></em></span>
	</p>
	<div class="clear"></div><? */ ?>

	<?php do_action('woocommerce_edit_account_form'); ?>

	<div class="edit-account-submit">
		<?php wp_nonce_field('save_account_details', 'save-account-details-nonce'); ?>
		<button type="submit" class="woocommerce-Button button<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" name="save_account_details" value="Сохранить">Сохранить</button>
		<input type="hidden" name="action" value="save_account_details" />
	</div>


	<?php do_action('woocommerce_edit_account_form_end'); ?>
</form>

<?php do_action('woocommerce_after_edit_account_form'); ?>

<div class="section_edit_account">

	<h3>Адрес доставки</h3>

	<form action="" class="edit-account ajax-address-user">
	<div class="edit-account__inputs">
		<p class="woocommerce-form-row">
			<label style="display: none;" for="billing_city">Населённый пункт</label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_city" placeholder="Населённый пункт" id="billing_city" value="<?php echo get_field('billing_city', 'user_' . $user->ID); ?>" />
		</p>
		<p class="woocommerce-form-row">
			<label style="display: none;" for="billing_street">Улица</label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_street" placeholder="Улица" id="billing_street" value="<?php echo get_field('billing_street', 'user_' . $user->ID); ?>" />
		</p>
		<p>
			<label style="display: none;" for="billing_house">Дом</label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_house" id="billing_house" placeholder="Дом" value="<?php echo get_field('billing_house', 'user_' . $user->ID); ?>" />
		</p>
		<p>
			<label style="display: none;" for="billing_flat">Квартира</label>
			<input type="text" placeholder="Квартира" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_flat" id="billing_flat" value="<?php echo get_field('billing_flat', 'user_' . $user->ID); ?>" />
		</p>
	</div>

	<div class="edit-account-submit">
		<input type="hidden" name="action" value="user_ajax_edit">
		<input type="hidden" name="userid" value="<?= $user->ID; ?>">

		<button type="submit" class="woocommerce-Button button" name="save_account_delivery" value="Сохранить">Сохранить</button>
	</div>
		
	</form>

</div>

<div class="section_edit_account">

	<h3>Изменить пароль</h3>

	<form action="" class="edit-account ajax-pass-user">

	<div class="edit-account__inputs">
		<p class="woocommerce-form-row">
			
				<input type="text" placeholder="Пароль" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="password" value="" />
			</p>
			<p class="woocommerce-form-row">
				<input type="text" placeholder="Подтвердите пароль"  class="woocommerce-Input woocommerce-Input--text input-text" name="password_check" id="password_check" value="" />
			</p>
	</div>

	<div class="edit-account-submit">
		<input type="hidden" name="userid" value="<?= $user->ID; ?>">
		<input type="hidden" name="action" value="userpass_ajax_edit">

		<button type="submit" class="woocommerce-Button button" name="save_account_delivery" value="Сохранить">Сохранить пароль</button>
	</div>
	</form>

</div>