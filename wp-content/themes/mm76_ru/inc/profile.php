<?php
/**
**** Разработка и продвижение сайтов *****
**** по вопросам доработки/разработки ****
**** MM76.RU *****************************
**** Tel: +7 920 650 76-76 ***************
**** WhatsApp: +7 920 650 76-76 **********
**** Tg: @mm76_ru ************************
**** Site: https://mm76.ru/ ***************
*/

/*
 * Добавляем шорткод, его можно использовать в содержимом любой статьи или страницы, вставив [misha_custom_login]
 */
add_shortcode( 'mm76_login', 'mm76_render_login' );
 
function mm76_render_login() {
 
	// проверяем, если пользователь уже авторизован, то выводим соответствующее сообщение и ссылку "Выйти"
	if ( is_user_logged_in() ) {
		return sprintf( "Вы уже авторизованы на сайте. <a href='%s'>Выйти</a>.", wp_logout_url() );
	}
 
	// присваиваем содержимое формы переменной и затем возвращаем её, выводить через echo() мы не можем, так как это шорткод
	$return = '<div class="mm76_login_form_tabs">
        <a class="active">Вход</a>
		<span> / </span>
        <a href="'.get_the_permalink(648).'" class="">Регистрация</a>
    </div>';
 
	// если возникли какие-либо ошибки, отображаем их
	if ( isset( $_REQUEST['errno'] ) ) {
		$error_codes = explode( ',', $_REQUEST['errno'] );
 
		foreach ( $error_codes as $error_code ) {
			switch ( $error_code ) {
				case 'empty_username':
					$return .= '<p class="errno">Вы не забыли указать свой email/имя пользователя?</p>';
					break;
				case 'empty_password':
					$return .= '<p class="errno">Пожалуйста, введите пароль.</p>';
					break;
				case 'invalid_username':
					$return .= '<p class="errno">На сайте не найдено указанного пользователя.</p>';
					break;
				case 'incorrect_password':
					$return .= sprintf( "<p class='errno'>Неверный пароль. <a href='%s'>Забыли</a>?</p>", get_the_permalink( 651 ) );
					break;
				case 'confirm':
					$return .= '<p class="errno success">Инструкции по сбросу пароля отправлены на ваш email.</p>';
					break;
				case 'changed':
					$return .= '<p class="errno success">Пароль успешно изменён.</p>';
					break;
				case 'expiredkey':
				case 'invalidkey':
					$retun .= '<p class="errno">Недействительный ключ.</p>';
					break;
			}
		}

	}

	$return .= '<div class="login-requests"></div>';
 
	// используем wp_login_form() для вывода формы (но можете сделать это и на чистом HTML)
	$return .= wp_login_form(
		array(
			'echo' => false, // не выводим, а возвращаем
			'redirect' => site_url('/my-account/'), // куда редиректить пользователя после входа
            'label_username' => __( 'E-mail' ),
            'label_password' => __( 'Пароль' ),
            'label_remember' => __( 'Запомнить меня' ),
            'label_log_in' => __( 'Войти' ),
		)
	);
 
	$return .= '<a class="forgot-password" href="'.get_the_permalink( 651 ).'">Забыли свой пароль?</a>
	<a href="'. site_url('register') . '" class="register-link">Зарегистрироваться</a>
	</div>';
 
	// и наконец возвращаем всё, что получилось
	return $return;
 
}

/*
 * Редиректы обратно на кастомную форму входа в случае ошибки
 */
add_filter( 'authenticate', 'misha_redirect_at_authenticate', 101, 3 );
 
function misha_redirect_at_authenticate( $user, $username, $password ) {
 
	if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
		if ( is_wp_error( $user ) ) {
			$error_codes = join( ',', $user->get_error_codes() );
 
			$login_url = home_url( '/login/' );
			$login_url = add_query_arg( 'errno', $error_codes, $login_url );
 
			wp_redirect( $login_url );
			exit;
		}
	}
 
	return $user;
}
 
/*
 * Редиректы после выхода с сайта
 */
add_action( 'wp_logout', 'misha_logout_redirect', 5 );
 
function misha_logout_redirect(){
	wp_safe_redirect( site_url( '/login/?logged_out=true' ) );
	exit;
}

///get_login
add_action( 'wp_ajax_get_login', 'get_login' );
add_action( 'wp_ajax_nopriv_get_login', 'get_login' );
function get_login(){
 
	$login = $_POST[ 'login' ];

	if( !email_exists( $login ) ) {
		echo 'no';
	} else {
		echo 'yes';
	}
 
	die;
}

/////get_auth
add_action( 'wp_ajax_get_auth', 'get_auth' );
add_action( 'wp_ajax_nopriv_get_auth', 'get_auth' );
function get_auth(){
 
	$login = $_POST[ 'login' ];
	$pass = $_POST['pass'];

	$user = get_user_by('login', $login);
	$user_id = $user->ID;

	if( $user ){
		$password = $pass;
		$hash     = $user->data->user_pass;

		if ( wp_check_password( $password, $hash ) )
		   echo "yes";
		else
		   echo "no";
	}
 
	die;
}

/**
 * сдеаем отображаемое имя не обязатлельным
 */
add_filter('woocommerce_save_account_details_required_fields', 'wc_save_account_details_required_fields' );
function wc_save_account_details_required_fields( $required_fields ){
	unset( $required_fields['account_display_name'] );
	return $required_fields;
}

/**
 * сохранени профиля
 */
add_action( 'woocommerce_save_account_details', 'cssigniter_save_account_details' );
function cssigniter_save_account_details( $user_id ) {

	///отчество
	if ( isset( $_POST['account_surname'] ) ) {
		update_field( 'surname', sanitize_text_field( $_POST['account_surname'] ), 'user_'.$user_id  );
	}

	///дата рождения
	if ( isset( $_POST['account_bdate'] ) && empty( get_field('bdate', 'user_'.$user_id) ) ) {
		update_field( 'bdate', sanitize_text_field( $_POST['account_bdate'] ), 'user_'.$user_id  );
	}

	///номер теелфона
	if ( isset( $_POST['account_phone'] ) ) {
		update_field( 'phone', sanitize_text_field( $_POST['account_phone'] ), 'user_'.$user_id  );
	}

	////политика конфыиденциальности
	if( isset( $_POST['policy'] ) && $_POST['policy'] == 'yes' ) {
		update_field( 'policy', true, 'user_'.$user_id  );
	} else {
		update_field( 'policy', false, 'user_'.$user_id  );
	}

}

/////user_ajax_edit
add_action( 'wp_ajax_user_ajax_edit', 'user_ajax_edit' );
add_action( 'wp_ajax_nopriv_user_ajax_edit', 'user_ajax_edit' );
function user_ajax_edit(){

	global $user_ID;
 
	$billing_city = (!empty($_POST['billing_city']) ? $_POST['billing_city'] : '');
	$billing_street = (!empty($_POST['billing_street']) ? $_POST['billing_street'] : '');
	$billing_house = (!empty($_POST['billing_house']) ? $_POST['billing_house'] : '');
	$billing_flat = (!empty($_POST['billing_flat']) ? $_POST['billing_flat'] : '');

	$userid = (!empty($_POST['userid']) ? $_POST['userid'] : '');

	if( $user_ID == $userid ) {

		$errors = [];

		if( empty($billing_city) ) $errors['billing_city'] = 'yes';
		if( empty($billing_street) ) $errors['billing_street'] = 'yes';
		if( empty($billing_house) ) $errors['billing_house'] = 'yes';

		if( !empty( $errors ) ) {
			
			echo json_encode([
				'status' => 'errors',
				'errors' => $errors,
			], true);

		} else {

			update_field( 'billing_city', $billing_city, 'user_'.$user_ID );
			update_field( 'billing_street', $billing_street, 'user_'.$user_ID );
			update_field( 'billing_house', $billing_house, 'user_'.$user_ID );
			update_field( 'billing_flat', $billing_flat, 'user_'.$user_ID );

			echo json_encode([
				'status' => 'ok',
			], true);

		}

	}
 
	die;
}

/////userpass_ajax_edit
add_action( 'wp_ajax_userpass_ajax_edit', 'userpass_ajax_edit' );
add_action( 'wp_ajax_nopriv_userpass_ajax_edit', 'userpass_ajax_edit' );
function userpass_ajax_edit(){

	global $wpdb, $user_ID;
 
	$password = (!empty($_POST['password']) ? $_POST['password'] : '');
	$password_check = (!empty($_POST['password_check']) ? $_POST['password_check'] : '');

	$userid = (!empty($_POST['userid']) ? $_POST['userid'] : '');

	if( $user_ID == $userid ) {

		$errors = [];

		if( $password !== $password_check ) $errors['password_check'] = 'yes';

		if( empty($password) ) $errors['password'] = 'yes';
		if( empty($password_check) ) $errors['password_check'] = 'yes';

		if( !empty( $errors ) ) {
			
			echo json_encode([
				'status' => 'errors',
				'errors' => $errors,
			], true);

		} else {
			
			$wpdb->query(
                $wpdb->prepare(
                    "UPDATE " . $wpdb->prefix . "users
                    SET user_pass = MD5('".$password."')
                    WHERE ID = '%d'",
                    $user_ID
                )
            );

			echo json_encode([
				'status' => 'ok',
			], true);

		}

	}
 
	die;
}