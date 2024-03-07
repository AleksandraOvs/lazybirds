<?php
/**
 * Страница генератора условий для страницы условий бонусных баллов
 *
 * @since  5.0.0
 * @version 5.0.0
 */

?>
<div class="wrap bonus-for-woo-admin">
    <?php  echo '<h1>Генератор условий бонусной системы лояльности</h1>'; ?>
    <p>Данный генератор создает текст правил условий вашей лояльной системы на основе настроек плагина Bonus for Woo.
        Все, что вам надо - скопировать текст, отредактировать по своему усмотрению и вставить на страницу например, "Бонусная программа".</p>
    <hr>
<div class="bfw-generator-wrap">
<?php
$val = get_option('bonus_option_name');

?><div style="background: #fff;padding: 10px 20px;">
<h1>Программа лояльности "<?php if(!empty($val['bonus-points-on-cart'])){echo $val['bonus-points-on-cart'];}else{echo 'бонусной системы';}  ?>"</h1>
<p>Целью разработанной и внедренной нами системы бонусов является создание выгодных условий для покупателей,
    нацеленных на экономию их финансовых средств.</p>

<p>Программа лояльности подразумевает под собой возврат(кешбэк) средств от покупок товаров в виде бонусных баллов.
    При этом 1 бонус равен 1 рублю.</p>
 <p>Размер возврата бонусных баллов зависит от общей суммы заказа и статуса клиента. В зависимости от общей суммы заказов
 клиенту присваивается соответствующий статус:</p>
<ul>
    <?php
    global $wpdb;
$table_bfw = $wpdb->get_results("SELECT *,summa_start FROM " . $wpdb->prefix . "bfw_computy  ORDER BY summa_start + 0 asc");
if ($table_bfw) {
    foreach ($table_bfw as $bfw) {
      echo '<li>- '. $bfw->name.': при общей сумме заказов ' . $bfw->summa_start .get_woocommerce_currency_symbol(). '. Начисляется ' . $bfw->percent . '% кешбэка.</li>';
    }
}
    ?>
    </ul>

        <h2>Отображение баллов и кешбэка</h2>
        <p>Узнать сколько баллов на счету можно в личном кабинете во вкладке "<?php if(!empty($val['title-on-account'])){echo $val['title-on-account'];}else{echo 'Страница бонусов';} ?> " </p>
        <?php if(empty($val['hystory-hide'])){
            echo '<p>Так же в этой вкладке можно посмотреть историю списаний и начислений баллов.</p>';
        }?>
        <?php if(!empty($val['bonus-in-price'])){
            echo '<p>На странице товара указано сколько вам вернется баллов за покупку данного товара.</p>';
        }?>
        <?php if(!empty($val['cashback-in-cart'])){
            echo '<p>На странице оформления товара и в корзине будет указано сколько вам вернется баллов за весь заказ.</p>';
        }?>


   <h2>Начисление баллов</h2>
        <p>Кроме начисления баллов за покупки товаров, предусмотрены другие вознаграждения:<br>
        <?php
        if(!empty($val['bonus-for-otziv'])){ echo $val['bonus-for-otziv'].' баллов за отзыв о купленном товаре.<br>';  }

        if((new BfwRoles)->is_pro()){
            if(!empty($val['birthday'])){ echo $val['birthday'].' баллов начислится в день вашего рождения, который 
 вы укажите в настройках профиля.<br>';  }
            if(!empty($val['points-for-registration'])){ echo $val['points-for-registration'].' баллов начислится сразу за регистрацию.<br>';  }
        }
        ?>
        </p>
        <h2>Ограничения начисления баллов</h2>
        <?php if(!empty($val['cashback-for-shipping'])){
            echo '<p>Кешбэк за доставку не будет начислен.</p>';
        }

        ?>


        <h2>Использование баллов</h2>
        <p>В корзине
            <?php if(!empty($val['spisanie-in-checkout'])){
                echo ' и в оформлении заказа ';
            }?>
            вы можете использовать баллы для покупки товаров.</p>

        <h2>Ограничения использования баллов</h2>
        <?php if(!empty($val['spisanie-onsale'])){
            echo '<p>Вы не можете использовать баллы на покупку товаров со скидкой.</p>';
        }?>
        <?php if(!empty($val['balls-and-coupon'])){
            echo '<p>Так же вы не можете использовать баллы если применен скидочный купон.</p>';
        }

        if((new BfwRoles)->is_pro()){
            $max_percenet_bonuses = $val['max-percent-bonuses'] ?? 100;
            if($max_percenet_bonuses<100){
                echo '<p>Только '.$max_percenet_bonuses.'% от суммы заказа вы можете потратить баллами. </p>';
            }

            $categoriexs = $val['exclude-category-cashback'] ?? '';
            if(!empty($categoriexs)){
                echo '<p>Товары из категорий: ';
                foreach ($categoriexs as $cat){
                    $term = get_term_by( 'id', $cat, 'product_cat', 'ARRAY_A' );
                    echo $term['name'].',';
                } echo ' за кешбэк не приобрести.</p>';
            }
            if(!empty($val['yous_balls_no_cashback'])){
                echo '<p>Если вы используете баллы, то в данном заказе кешбэка не будет.</p>';
            }
            if(!empty($val['minimal-amount'])){
                echo '<p>Для траты баллов сумма в заказе должна быть не менее '.$val['minimal-amount'].get_woocommerce_currency_symbol().'.</p>';
            }
            if(!empty($val['day-inactive'])){
                echo '<h2>Сгорание баллов</h2>
                <p>При отсутствии покупок более '.$val['day-inactive'].' дней, баллы с вашего счета сгорят.</p>';
            }

            if(!empty($val['referal-system'])){
                $referal_cashback =$val['referal-cashback'] ?? 0;
                echo '<h2>Реферальная система</h2>
                <p>Так же в нашей бонусной программе есть реферальная система. В личном кабинете будет реферальная ссылка,
                которую вы можете отправить вашим друзьям личными сообщениями в мессенджерах и СМС, а также выкладывая в социальных сетях. Число приглашенных не ограничено.</p>';
                echo '<p>За покупки ваших приглашенных друзей, вы будете получать '.$referal_cashback.'% кешбэка в виде бонусных баллов';
                if(!empty($val['first-order-referal'])){echo ', но только за первую покупку.';}else{echo '.</p>';}
            }


        }
        ?>


    </div>
</div>
</div>

