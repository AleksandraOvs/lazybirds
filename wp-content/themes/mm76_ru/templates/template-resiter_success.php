<?php
/*
Template Name: Register Success
*/

if( !isset($_GET['hash']) )
    wp_die('Ошибка доступа');
 
require_once(ABSPATH . WPINC . '/registration.php');
global $wpdb, $user_ID;
//Проверяем, вошел ли уже пользователь в систему
if ($user_ID) {
 
   //Залогиненного пользователя перенаправляем на главную страницу.
   header( 'Location:' . home_url() );
 
} else {

    $pre_user = $wpdb->get_row( 
        "SELECT id, uname, usurname, login_email
        FROM " . $wpdb->prefix . "pre_users
        WHERE hash = '".$_GET['hash']."' "
    );

    if( !$pre_user ) wp_die('Ошибка доступа. Возможно, ссылка для регистрации больше не активна.');

	$errors = array();
 
	if( $_POST ) {
 
		// Проверяем, есть ли email и действителен ли он
        $password_new = (!empty($_POST['paassword']) ? $_POST['paassword'] : '');

        if( mb_strlen($password_new) < 6 ) {
            $errors['pass'] = "Пароль не может быть менее 6 символов";
        }
        
		if(0 === count($errors)) {
 
			$new_user_id = wp_create_user( $pre_user->login_email, $password_new, $pre_user->login_email );

            wp_update_user( [
                'ID' => $new_user_id,
                'first_name' => $pre_user->uname,
                'last_name' => $pre_user->usurname,
            ] );

            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM " . $wpdb->prefix . "pre_users
                    WHERE id = '%d'",
                    $pre_user->id
                )
            );

            /**
             * авторизация пользователя на сайте
             */
            wp_clear_auth_cookie();
            wp_set_current_user( $new_user_id );
            wp_set_auth_cookie ( $new_user_id );
            $redirect_to = get_home_url() . '/my-account/';
            wp_safe_redirect( $redirect_to );
            exit();
 
		}
	}
}
?>

<?php get_header(); ?>

<section class="login_form_container">

        <h1 class="titles">Задать пароль</h1>

        <div class="container">

            <form id="wp_signup_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

                    <?php if(isset( $errors['pass'] )): ?>
                        <span class="tt-info">
                            <?= $errors['pass']; ?>
                        </span>
                    <?php endif; ?>

                    <p class="login-mm76">
                        <input type="password" name="paassword" placeholder="Ваш пароль" id="paassword" value="<?= isset( $_REQUEST['paassword'] ) ? $_REQUEST['paassword']  : '' ?>" class="<?= (isset( $errors['pass'] ) ? 'invalid' : ''); ?>" required>
                    </p>
                    
                    <p class="login-submit">
                        <input type="submit" class="button" id="submitbtn" name="submit" value="Сохранить" />
                    </p>

            </form>
        </div>

</section>

<?php
	get_footer();
?>