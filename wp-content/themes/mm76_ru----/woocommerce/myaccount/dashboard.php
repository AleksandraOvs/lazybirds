<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$allowed_html = array(
	'a' => array(
		'href' => array(),
	),
);
?>

<p class="hello_users_account">
	<?php
	printf(
		/* translators: 1: user display name 2: logout url */
		wp_kses( __( 'Привет, %1$s (не %1$s? <a href="%2$s">Выйти</a>)', 'woocommerce' ), $allowed_html ),
		'<strong>' . esc_html( $current_user->first_name ) . '</strong>',
		esc_url( wc_logout_url() )
	);
	?>
</p>

<div class="tt-info" style="margin-top: 30px; margin-bottom:20px;">
<?php
$user_ID = get_current_user_id();
if( empty( get_field( 'surname', 'user_'.$user_ID ) ) || empty( get_field( 'bdate', 'user_'.$user_ID ) ) || empty( get_field( 'phone', 'user_'.$user_ID ) ) || empty( get_field( 'billing_city', 'user_'.$user_ID ) ) ):
?>
Предлагаем навести уют в личном кабинете. Здесь можно просматривать последние заказы, управлять доставкой, корректировать данные для оплаты и менять пароль.<br>
И не забудь добавить любимые товары в список желаний 🤍
<?php
else:
?>
Рады новой встрече! 🤍<br>
Надеемся, ты уже выбрала новый костюм! Eсли нужна помощь по вопросам заказа или доставки - обращайся.
<?php
endif;
?>
<br><br><div style="text-align:right;">Команда Lazy Birds</div>
</div>
<div style="text-align:right;">
	<img src="<?= get_template_directory_uri(  ); ?>/assets/img/lazybird.png" style="margin: 0;">
</div>

<div class="container__main_cards">
	<!--card-->
	<a href="<?= get_home_url(); ?>/my-account/bonuses/" class="card">
		<div class="ctitle">Ты в бонусе</div>
		<picture>
			<?php echo wp_get_attachment_image( 676, 'full' );?>
		</picture>
	</a>
	<!--card-->
	<?php
		$citates = get_field('czitaty_profilya', 'option');
		$rand_array = [];
		foreach($citates as $citata) {
			$rand_array[] = $citata['foto_czitaty']['url'];
		}
		$k = array_rand($rand_array);
	?>
	<!--card-->
	<div class="card note">
		<div class="note-title">NOTE:</div>
		<picture>
			<img src="<?= $rand_array[$k]; ?>">
		</picture>
	</div>
	<!--card-->
	<?php
		$term_id = get_field('kakuyu_kollekcziyu_vyvodit', 'option');
		$term = get_term( $term_id, 'product_cat' );
		$image_id = get_woocommerce_term_meta( $term->term_id, 'thumbnail_id', true );
	?>
	<!--card-->
	<a class="card" href="<?= get_term_link( $term_id ); ?>">
		<div class="ctitle"><?= $term->name; ?></div>
		<picture>
			<?= wp_get_attachment_image( $image_id, 'full' ); ?>
		</picture>
	</a>
	<!--card-->
</div>

<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );

	/**
	 * Deprecated woocommerce_before_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_before_my_account' );

	/**
	 * Deprecated woocommerce_after_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_after_my_account' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
