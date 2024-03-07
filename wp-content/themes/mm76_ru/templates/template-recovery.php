<?php
/*
Template Name: Recovery Pass
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

        $email = $wpdb->escape($_REQUEST['email']);
        if( !email_exists( $email ) ) {
            header( 'Location:' . get_bloginfo('url') . '/register/?email=' . $email );
        } else {
            /**
             * если емайл существует, то будем отправлять ссылку на смену пароля
             */

            /**
             * Генерация хеша
            */
            $length = 10;
            $password = '';
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $limit = strlen($characters) - 1;
            for ($i = 0; $i < $length; $i++) {
                $password .= $characters[rand(0, $limit)];
            }

            $wpdb->query(
                $wpdb->prepare(
                    "INSERT INTO " . $wpdb->prefix . "pre_pass
                    ( hash, login_email )
                    VALUES ( %s, %s )",
                    $password, $email
                )
            );

            $multiple_to_recipients = array(
                $email,
            );
            
            add_filter( 'wp_mail_content_type', function( $content_type ){
                return "text/html";
            } );

            $user = get_user_by('email', $email );

            $message_html = '<p>'.$user->first_name.' '.$user->last_name.',</p><br>
            <p>мы получили запрос на восстановление пароля от твоего аккаунта на сайте Lazy Birds.</p><br>
            <p>Чтобы подтвердить запрос и создать новый пароль, перейди по ссылке: <a href="'.get_home_url().'/recovery_success?hash='.$password.'">'.get_home_url().'/recovery_success?hash='.$password.'</a>. (Ссылка действительна в течение 30 минут с момента первого запроса на восстановление пароля).</p><br>
            <p>Если ты не отправляла запрос на восстановление пароля, никаких действий выполнять не нужно.<br>
            Если нужна помощь, свяжись с нами.</p>';

            wp_mail( $multiple_to_recipients, 'Запрос на восстановление пароля', $message_html );
 
			header( 'Location:' . get_bloginfo('url') . '/recovery/?success=1&u=' . $email );

        }
	}
}
?>

<?php get_header(); ?>

<section class="login_form_container">

        <h1 class="titles">Восстановление пароля</h1>

        <?php
            if( isset($_GET['success']) && $_GET['success'] = 1 ):
        ?>

            <div class="cont">

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
                        Письмо с восстановлением пароля скоро придет на твою почту :)
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

            </div>

        <?php
            else:
        ?>

            <div class="container">

            <form id="wp_signup_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

                    <?php if(isset($_GET['email'])): ?>
                        <p>Мы не нашли аккаунт с такой электронной почтой: </p>
                    <?php else: ?>
                        <p>Ссылка на восстановление пароля будет отправлена на почту ниже: </p>
                    <?php endif; ?>

                    <?php if(isset( $errors['pass'] )): ?>
                        <span class="tt-info">
                            <?= $errors['pass']; ?>
                        </span>
                    <?php endif; ?>

                    <p class="login-mm76">
                        <input type="email" name="email" id="email2" value="<?= isset( $_GET['email'] ) ? $_GET['email']  : '' ?>" class="<?= (isset( $errors['pass'] ) ? 'invalid' : ''); ?>" required>
                        <span>Ваш E-Mail</span>
                    </p>
                    
                    <p class="login-submit">
                        <input type="submit" id="submitbtn" name="submit" value="Отправить" />
                    </p>

            </form>
            <div class="container__success">
                <a class="center-link" style="margin-top:0;" href="<?= get_home_url(); ?>/login/">
                    <div class="icon">
                        <svg width="49" height="24" viewBox="0 0 49 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.939339 10.9393C0.353554 11.5251 0.353554 12.4749 0.939339 13.0607L10.4853 22.6066C11.0711 23.1924 12.0208 23.1924 12.6066 22.6066C13.1924 22.0208 13.1924 21.0711 12.6066 20.4853L4.12132 12L12.6066 3.51472C13.1924 2.92893 13.1924 1.97919 12.6066 1.3934C12.0208 0.807611 11.0711 0.807611 10.4853 1.3934L0.939339 10.9393ZM49 10.5L2 10.5V13.5L49 13.5V10.5Z" fill="#8EBCEE"/>
                        </svg>
                    </div>
                    <div class="text">Обратно в Личный кабинет</div>
                </a>
            </div>
            </div>

        <?php
            endif;
        ?>

</section>

<?php
	get_footer();
?>