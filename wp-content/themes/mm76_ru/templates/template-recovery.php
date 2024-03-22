<?php
/*
Template Name: Recovery Pass
*/

require_once(ABSPATH . WPINC . '/registration.php');
global $wpdb, $user_ID;
//Проверяем, вошел ли уже пользователь в систему
if ($user_ID) {

    //Залогиненного пользователя перенаправляем на главную страницу.
    header('Location:' . home_url());
} else {

    $errors = array();

    if ($_POST) {

        $email = $wpdb->escape($_REQUEST['email']);
        if (!email_exists($email)) {
            header('Location:' . get_bloginfo('url') . '/register/?email=' . $email);
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
                    $password,
                    $email
                )
            );

            $multiple_to_recipients = array(
                $email,
            );

            add_filter('wp_mail_content_type', function ($content_type) {
                return "text/html";
            });

            $user = get_user_by('email', $email);

            $message_html = '<p>' . $user->first_name . ' ' . $user->last_name . ',</p><br>
            <p>мы получили запрос на восстановление пароля от твоего аккаунта на сайте Lazy Birds.</p><br>
            <p>Чтобы подтвердить запрос и создать новый пароль, перейди по ссылке: <a href="' . get_home_url() . '/recovery_success?hash=' . $password . '">' . get_home_url() . '/recovery_success?hash=' . $password . '</a>. (Ссылка действительна в течение 30 минут с момента первого запроса на восстановление пароля).</p><br>
            <p>Если ты не отправляла запрос на восстановление пароля, никаких действий выполнять не нужно.<br>
            Если нужна помощь, свяжись с нами.</p>';

            wp_mail($multiple_to_recipients, 'Запрос на восстановление пароля', $message_html);

            header('Location:' . get_bloginfo('url') . '/recovery/?success=1&u=' . $email);
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

        <!-- <h1 class="titles">Восстановление пароля</h1> -->

        <?php
        if (isset($_GET['success']) && $_GET['success'] = 1) :
        ?>

            <div class="container__success">
                <div class="icon">
                        <svg width="60" height="52" viewBox="0 0 60 52" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.539 23.329C21.2187 27.7462 24.8086 32.1633 28.6678 36.4038C29.2063 37.0222 30.3729 37.3755 31.0909 36.7571C36.2963 32.7817 40.6939 28.1879 45.0916 23.4174C47.3353 21.0321 49.4892 18.5585 51.8227 16.35C54.2458 13.9647 57.387 12.0212 59.5409 9.45927C60.6179 8.13413 59.182 6.4556 57.6562 7.074C52.0021 9.19423 47.3353 15.2899 43.3864 19.6187C38.7195 24.8309 34.2321 30.2198 28.7575 34.5486C29.655 34.5486 30.6422 34.6369 31.5397 34.6369C27.4113 30.3965 23.0136 26.5094 18.7057 22.3573C18.1673 21.9156 16.9108 22.6223 17.539 23.329Z" fill="#FF81C9" />
                            <path d="M34.1694 1.00071C25.5392 0.472489 16.1082 2.58541 9.79123 8.83606C3.6522 14.9106 3.65222 24.3306 5.07576 32.254C6.58827 40.4414 11.3038 46.34 19.3112 49.3332C27.2296 52.2385 36.2157 51.7983 43.8673 48.4529C51.3409 45.1074 57.0351 39.473 57.9248 31.1975C58.8145 22.6579 57.124 14.9987 50.2732 9.1882C42.8886 2.93755 32.8348 1.52895 23.4928 0.824654C15.8413 0.29643 9.34637 2.05717 4.8978 8.65997C0.0933494 15.879 -1.24123 28.4684 3.38528 35.9515C9.16842 45.4596 21.2686 46.6041 31.3223 47.2203C33.1907 47.3084 34.0804 44.6673 31.9451 44.4912C24.3826 44.051 16.0193 43.6988 9.88023 38.7688C3.65224 33.7506 3.11841 25.299 4.54195 17.9039C5.96549 10.5088 10.8589 3.81792 18.9553 3.46577C26.162 3.11363 35.0591 4.43419 41.643 7.42746C48.4938 10.5088 53.921 15.9671 54.7218 23.6263C55.0777 26.9717 54.8108 30.6693 53.9211 33.9267C52.7644 38.0645 49.4725 40.9697 45.9136 43.1706C34.7922 50.1256 15.4854 49.8615 9.79123 36.0396C7.12209 29.5248 6.14344 19.6646 9.88023 13.414C14.5067 5.57866 25.3613 2.67344 33.9025 3.02559C35.1481 2.93755 35.415 1.08875 34.1694 1.00071Z" fill="#FF81C9" />
                        </svg>

                    </div>
                <div class="center-text">
                    Проверь свою электронную почту и активируй аккаунт по ссылке. Если письмо затерялось, проверь ящик «Спам» </div>
                <a class="center-link button" href="<?php get_home_url(); ?>/login/">В личный кабинет
                    
                    <!-- <div class="text">В личный личный кабинет</div> -->
                </a>
            </div>

        <?php
        else :
        ?>

            <div class="container">

                <form id="wp_signup_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

                    <?php if (isset($_GET['email'])) : ?>
                        <p>Мы не нашли аккаунт с такой электронной почтой: </p>
                    <?php else : ?>
                        <p>Ссылка на восстановление пароля будет отправлена на почту ниже: </p>
                    <?php endif; ?>

                    <?php if (isset($errors['pass'])) : ?>
                        <span class="tt-info">
                            <?= $errors['pass']; ?>
                        </span>
                    <?php endif; ?>

                    <p class="login-mm76">
                        <input type="email" name="email" id="email2" value="<?= isset($_GET['email']) ? $_GET['email']  : '' ?>" class="<?= (isset($errors['pass']) ? 'invalid' : ''); ?>" required>
                        <span>Ваш E-Mail</span>
                    </p>

                    <p class="login-submit">
                        <input type="submit" class="button" id="submitbtn" name="submit" value="Отправить" />
                    </p>

                </form>
                <div class="container__success">
                    <a class="center-link" style="margin-top:0;" href="<?= get_home_url() ?>/login/">
                        <div class="icon">
                            <svg width="49" height="24" viewBox="0 0 49 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0.939339 10.9393C0.353554 11.5251 0.353554 12.4749 0.939339 13.0607L10.4853 22.6066C11.0711 23.1924 12.0208 23.1924 12.6066 22.6066C13.1924 22.0208 13.1924 21.0711 12.6066 20.4853L4.12132 12L12.6066 3.51472C13.1924 2.92893 13.1924 1.97919 12.6066 1.3934C12.0208 0.807611 11.0711 0.807611 10.4853 1.3934L0.939339 10.9393ZM49 10.5L2 10.5V13.5L49 13.5V10.5Z" fill="#8EBCEE" />
                            </svg>
                        </div>
                        <div class="text">Обратно в Личный кабинет</div>
                    </a>
                </div>
            </div>

        <?php
        endif;
        ?>



    </div>

</section>

<?php
get_footer();
?>