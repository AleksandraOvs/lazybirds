<?php
/*
Template Name: Register
*/
 
require_once(ABSPATH . WPINC . '/registration.php');
global $wpdb, $user_ID;
//Проверяем, вошел ли уже пользователь в систему
if ($user_ID) {
 
   //Залогиненного пользователя перенаправляем на главную страницу.
   header( 'Location:' . home_url() );
 
} else {
	$errors = array();
 
	if( $_POST ) {
 
		// Проверяем, есть ли email и действителен ли он
		$email = $wpdb->escape($_REQUEST['email']);
		if( !is_email( $email ) ) { 
			$errors['email'] = "Пожалуйста, введи действительный E-mail";
		} elseif( email_exists( $email ) ) {
			$errors['email'] = 'Пташка, кажется у тебя уже есть личный кабинет!<br><a href="'.get_the_permalink( 651 ).'?email='.$email.'">Восстановить доступ</a>';
		}

        if( isset( $_POST['uname'] ) && empty( $_POST['uname'] ) ) {
            $errors['uname'] = "Пожалуйста, введи своё имя";
        }

        if( isset( $_POST['surname'] ) && empty( $_POST['surname'] ) ) {
            $errors['surname'] = "Пожалуйста, введи свою фамилию";
        }
        
		if(0 === count($errors)) {
 
            /**
             * Генерация пароля
             */
            $length = 10;
            $password = '';
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $limit = strlen($characters) - 1;
            for ($i = 0; $i < $length; $i++) {
                $password .= $characters[rand(0, $limit)];
            }
			//$password = $_POST['password'];
 
			//$new_user_id = wp_create_user( $email, $password, $email );

            $wpdb->query(
                $wpdb->prepare(
                    "
                    INSERT INTO " . $wpdb->prefix . "pre_users
                    ( hash, uname, usurname, login_email )
                    VALUES ( %s, %s, %s, %s )",
                    $password, $_POST['uname'], $_POST['surname'], $email
                )
            );
 
			// Здесь вы можете делать все, что угодно, например, отправлять электронное письмо пользователю и т. д. 
 
			$success = 1;

            $multiple_to_recipients = array(
                $email,
            );
            
            add_filter( 'wp_mail_content_type', function( $content_type ){
                return "text/html";
            } );

            $message_html = '<h3>Рады познакомиться, пташка!</h3><br>
            <p>Привет, '.$_POST['uname'].' '.$_POST['surname'].'</p>
            <p>Спасибо за регистрацию на сайте Lazy Birds. Мы уверены, что это событие станет началом долгой дружбы ❤️</p><br>
            <p>Имя аккаунта совпадает с указанным в форме регистрации. Для просмотра информации о заказах, изменения пароля и других личных данных рекомендуем добавить в закладки ссылку на личный кабинет: <a href="https://lazybirds.ru/my-account/">https://lazybirds.ru/my-account/</a></p><br>
            <p><a href="'.get_home_url().'/register_success?hash='.$password.'">Нажми здесь, чтобы активировать аккаунт и задать пароль.</a></p><br>
            <p>До скорой встречи 🕊️</p>';

            wp_mail( $multiple_to_recipients, 'Твой аккаунт на сайте Lazy Birds создан', $message_html );
 
			header( 'Location:' . get_bloginfo('url') . '/register/?success=1&u=' . $email );
 
		}
	}
}
?>

<?php get_header(); ?>

<section class="login_form_container">
    <div class="cont">
    <div class="breads">
            <?php
            if (function_exists('yoast_breadcrumb')) {
                yoast_breadcrumb('<p id="breadcrumbs">', '</p>');
            }
            ?>
        </div>
        <h1 class="titles">Личный кабинет</h1>

        <?php
            if( isset($_GET['success']) && $_GET['success'] = 1 ):
        ?>

            

                <div class="container__success">
                    <div class="center-icon">
                        <svg width="143" height="143" viewBox="0 0 143 143" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_73_501)">
                                <path d="M71.5327 1.58809e-05C110.94 0.0261036 142.957 32.0401 143 71.4631C143.044 110.886 110.757 143.157 71.4196 143C31.9953 142.848 -0.0521309 110.738 6.36652e-05 71.437C0.0522582 32.0923 32.2128 -0.0260719 71.5327 1.58809e-05ZM129.717 71.55C129.804 39.5534 103.698 13.3743 71.624 13.2874C39.6288 13.2004 13.401 39.3186 13.3227 71.35C13.2444 103.451 39.3852 129.669 71.5109 129.708C103.519 129.747 129.634 103.66 129.717 71.5544V71.55Z" fill="#8EBCEE"/>
                                <path d="M61.2329 84.333C61.6765 83.6634 61.9766 82.9938 62.4594 82.5068C74.8948 70.0412 87.3432 57.593 99.8003 45.1535C102.862 42.0969 106.929 41.9795 109.574 44.8318C111.905 47.3493 112.036 50.9711 109.865 53.6321C109.461 54.1277 108.991 54.5756 108.534 55.0277C94.4286 69.1282 80.323 83.233 66.2175 97.329C62.3638 101.181 59.0451 101.181 55.1957 97.3421C48.0364 90.1897 40.877 83.0416 33.7307 75.8762C31.7386 73.8805 31.0427 71.4935 32.0126 68.8108C32.9695 66.1585 34.9529 64.628 37.7627 64.3324C40.0114 64.0976 41.8382 65.0281 43.4084 66.6064C48.7323 71.9544 54.0779 77.2806 59.4191 82.6112C59.8671 83.059 60.3369 83.4851 61.2329 84.333Z" fill="#8EBCEE"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_73_501">
                                    <rect width="143" height="143" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </div>
                    <div class="center-text">
                        Проверь свою электронную почту и активируй аккаунт по ссылке. Если письмо затерялось, проверь ящик «Спам».
                    </div>
                    <a class="center-link" href="<?php get_home_url(); ?>/login/">
                        <div class="icon">
                            <svg width="49" height="24" viewBox="0 0 49 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0.939339 10.9393C0.353554 11.5251 0.353554 12.4749 0.939339 13.0607L10.4853 22.6066C11.0711 23.1924 12.0208 23.1924 12.6066 22.6066C13.1924 22.0208 13.1924 21.0711 12.6066 20.4853L4.12132 12L12.6066 3.51472C13.1924 2.92893 13.1924 1.97919 12.6066 1.3934C12.0208 0.807611 11.0711 0.807611 10.4853 1.3934L0.939339 10.9393ZM49 10.5L2 10.5V13.5L49 13.5V10.5Z" fill="#8EBCEE"/>
                            </svg>
                        </div>
                        <div class="text">Обратно в Личный кабинет</div>
                    </a>
                </div>

        <?php
            else:
        ?>

            <div class="container">

                <div class="mm76_login_form_tabs">
                    <a href="<?= get_the_permalink(642); ?>" class="">Вход</a>
                    <span>/</span>
                    <a class="active">Регистрация</a>
                </div>

                <?php if(!isset($errors) || empty($errors)): ?>
                <!-- <div class="tt-info" style="margin-top:40px;">
                    Получи 1000 бонусов на счет за регистрацию (1 бонус = 1 рубль)
                </div> -->
                <?php endif; ?>

                <form id="wp_signup_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

                        <?php if(isset( $errors['email'] )): ?>
                            <span class="tt-info">
                                <?= $errors['email']; ?>
                            </span>
                        <?php endif; ?>

                        <div class="email_requests"></div>

                        <p class="login-mm76">
                            <input type="text" name="email" id="email" placeholder="E-mail" value="<?= isset( $_REQUEST['email'] ) ? $_REQUEST['email']  : (isset($_GET['email']) ? $_GET['email'] : '') ?>" class="<?= (isset( $errors['email'] ) ? 'invalid' : ''); ?>" required>
                        </p>

                        <p class="login-mm76">
                            <input type="text" name="uname" placeholder="Имя" id="uname" value="<?= isset( $_REQUEST['uname'] ) ? $_REQUEST['uname']  : '' ?>" required>
                            <span class="error"><?= isset( $errors['uname'] ) ? $errors['uname']  : '' ?></span>
                        </p>

                        <p class="login-mm76">
                            <input type="text" name="surname" placeholder="Фамилия" id="surname" value="<?= isset( $_REQUEST['surname'] ) ? $_REQUEST['surname']  : '' ?>" required>
                            <span class="error"><?= isset( $errors['surname'] ) ? $errors['surname']  : '' ?></span>
                        </p>

                        <p class="text">
                            Личные данные используются для управления доступом в личный кабинет и других целей, описанных в <a href="#">политике конфиденциальности</a>.
                        </p>
                        
                        <p class="login-submit">
                            <input class="button" type="submit" id="submitbtn" name="submit" value="Зарегистрироваться" />
                        </p>

                </form>
            </div>

        <?php
            endif;
        ?>
    </div>
</section>

<?php
	get_footer();
?>