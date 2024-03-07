<?php

/**
 * @link       https://digitalkassa.ru/
 * @since      1.1.0
 *
 * @package    Digitalkassa
 * @subpackage Digitalkassa/admin/partials
 */

?>

<?php
$aOrderStatuses = wc_get_order_statuses();

$aSchemes = array(
    '0' => 'Предоплата, полный расчет',
    '1' => 'Полный расчет'
);

$аTaxation = array(
    '1' => 'ОРН',
    '2' => 'УСН доход',
    '4' => 'УСН доход - расход',
    '16' => 'ЕСН',
    '32' => 'ПСН'
);

$аSendingReceipt = array(
    'email' => 'Email',
    'phone' => 'Телефон'
);

$aTypeOfReceiptPosition = array(
    '1' => 'Товар',
    '2' => 'Подакцизный товар',
    '3' => 'Работа',
    '4' => 'Услуга',
    '5' => 'Ставка азартной игры',
    '6' => 'Выигрыш азартной игры',
    '7' => 'Лотерейный билет',
    '8' => 'Выигрыш лотереи',
    '9' => 'Предоставление РИД',
    '10' => 'Платеж',
    '11' => 'Агентское вознаграждение',
    '12' => 'Составной предмет расчета',
    '13' => 'Иной предмет расчета',
    '14' => 'Имущественное право',
    '15' => 'Внереализационный доход',
    '16' => 'Страховые взносы',
    '17' => 'Торговый сбор',
    '18' => 'Курортный сбор',
    '19' => 'Залог',
    '20' => 'Расход',
    '21' => 'Взносы на ОПС ИП',
    '22' => 'Взносы на ОПС',
    '23' => 'Взносы на ОМС ИП',
    '24' => 'Взносы на ОМС',
    '25' => 'Взносы на ОСС',
    '26' => 'Платеж казино',
    '27' => 'Выдача ДС',
    '30' => 'Акцизный товар не маркированный',
    '31' => 'Акцизный товар маркированный',
    '32' => 'Товар не маркированный',
    '33' => 'Товар маркированный'
);

$aVat = array(
    '1' => 'Ставка НДС 20%',
    '2' => 'Ставка НДС 10%',
    '5' => 'Ставка НДС 0%',
    '6' => 'НДС не облагается'
);
?>
<style>
    div label {
        display: block;
    }

    div input {
        width: 250px;
        height: 30px;
        border: unset;
        padding: 0px 10px;
        border-radius: 10px;
    }

    #Digitalkassa_check_wnd {
        position: absolute;
        z-index: 99999;
        top: 0px;
    }

    .wrap div{
        margin: 20px 0px;
    }
    
    .wrap{
        padding: 30px;
        background: white;
        border-radius: 20px;
        max-width: 1000px;
        margin: 20px auto;
    }

    #wpcontent{
        padding-left: 0px;
    }
    
    .wrap h1{
        font-weight: 700;
    }

    .flex_div{
        display: flex;
        justify-content: space-between;
    }
    
    .w66{
        max-width: 66%;
    }

    .flex_div div{
        width: 32%;
        margin: 0;
    }

    .flex_div div input{
        width: 100%;
        background: #f2f2f2;
        height: 50px;
    }

    .flex_div div select{
        width: 100%;
        background: #f2f2f2;
        border: unset;
        height: 50px;
        border-radius: 10px;
    }

    .w66 div{
        width: calc(48% + 3px);
    }
    
    .button-primary{
        background-color: #EC161B !important;
        background: #EC161B !important;
        padding: 10px 20px 39px 20px !important;
        width: max-content !important;
    }

    .flex_div label{
        margin-bottom: 10px;
    }

    .wpop_admin_notice {
        display:none !important
    }

</style>
<div class="wrap">
    <div style="display: flex; align-items: center; margin: 0px">
        <svg width="100" height="100" viewBox="0 0 204 175" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M106.313 10.4989C101.15 10.4989 96.9646 14.7011 96.9646 19.8848V115.582C96.9646 142.464 75.2583 164.257 48.4823 164.257H40.312C18.0483 164.257 0 146.137 0 123.785C0 101.433 18.0483 83.3126 40.312 83.3126H71.8711V93.8115H40.312C23.8237 93.8115 10.4573 107.231 10.4573 123.785C10.4573 140.338 23.8237 153.758 40.312 153.758H48.4823C69.4829 153.758 86.5072 136.666 86.5072 115.582V19.8848C86.5072 8.90275 95.3748 0 106.313 0C117.252 0 126.12 8.90274 126.12 19.8848V61.6896C126.12 64.5553 129.571 65.9904 131.589 63.9641L172.062 23.3299C179.369 15.9947 191.214 15.9947 198.52 23.3299C205.827 30.6652 205.827 42.5581 198.52 49.8932L167.879 80.6551C155.764 92.8173 155.764 112.536 167.879 124.699L182.519 139.397C190.631 147.542 190.631 160.747 182.519 168.891C174.406 177.036 161.253 177.036 153.141 168.891L114.151 129.746L121.545 122.323L160.535 161.468C164.564 165.512 171.096 165.512 175.124 161.468C179.153 157.423 179.153 150.865 175.124 146.821L160.484 132.123C144.286 115.86 144.286 89.4934 160.485 73.2312L191.126 42.4693C194.348 39.2342 194.348 33.9889 191.126 30.7537C187.904 27.5186 182.679 27.5186 179.457 30.7537L138.983 71.3879C130.377 80.0281 115.662 73.9087 115.662 61.6896V19.8848C115.662 14.7011 111.477 10.4989 106.313 10.4989Z" fill="black"/>
        </svg>
        <h1>Настройки digitalkassa</h1>
    </div>
    <form method="post" action="options.php">
        <?php settings_fields('Digitalkassa-option-group');
        settings_errors('Digitalkassa-option-group-errors', '', false);
        ?>
        <?php do_settings_sections('Digitalkassa_submenu'); ?>
        <div class="flex_div">
            <div>
                <label>Идентификатор актора</label>
                <input name="digitalkassa_option_actor_id"
                value="<?php esc_html_e(get_option('digitalkassa_option_actor_id'), 'text_domain'); ?>" />
            </div>
            <div>
                <label>Токен актора</label>
                <input name="digitalkassa_option_actor_token"
                value="<?php esc_html_e(get_option('digitalkassa_option_actor_token'), 'text_domain'); ?>" />
            </div>
            <div>
                <label>Группу касс</label>
                <input name="digitalkassa_option_cashbox" value="<?php esc_html_e(get_option('digitalkassa_option_cashbox'), 'text_domain'); ?>" />
            </div>
        </div>
        <div class="flex_div">
            <div>
                <label>Выберите схему фискализации:</label>
                <select name="digitalkassa_option_scheme">
                    <?php
                    foreach ($aSchemes as $key => $value) {
                        if ($key == get_option('digitalkassa_option_scheme')) {
                            ?>
                                <option selected value="<?php esc_html_e($key, 'text_domain'); ?>"><?php esc_html_e($value, 'text_domain'); ?></option>
                            <?php
                        } else {
                            ?>
                                <option value="<?php esc_html_e($key, 'text_domain'); ?>"><?php esc_html_e($value, 'text_domain'); ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div>
                <label>Статус заказа для чека предоплаты</label>
                <select name="digitalkassa_option_status_first_receipt" value="">
                    <?php
                    foreach ($aOrderStatuses as $key => $value) {
                        if ($key == get_option('digitalkassa_option_status_first_receipt')) {
                            ?>
                                <option selected value="<?php esc_html_e($key, 'text_domain'); ?>"><?php esc_html_e($value, 'text_domain'); ?></option>
                            <?php
                        } else {
                            ?>
                                <option value="<?php esc_html_e($key, 'text_domain'); ?>"><?php esc_html_e($value, 'text_domain'); ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div>
                <label>Статус заказа для чека полного расчета</label>
                <select name="digitalkassa_option_status_second_receipt">
                    <?php
                    foreach ($aOrderStatuses as $key => $value) {
                        if ($key == get_option('digitalkassa_option_status_second_receipt')) {
                            ?>
                                <option selected value="<?php esc_html_e($key, 'text_domain'); ?>"><?php esc_html_e($value, 'text_domain'); ?></option>
                            <?php
                        } else {
                            ?>
                                <option value="<?php esc_html_e($key, 'text_domain'); ?>"><?php esc_html_e($value, 'text_domain'); ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="flex_div">
            <div>
                <label>Место расчетов</label>
                <input name="digitalkassa_option_place_of_settlement"
                value="<?php echo get_option('digitalkassa_option_place_of_settlement'); ?>" />
            </div>
            <div>
                <label>Налогообложение</label>
                <select name="digitalkassa_option_taxation">
                    <?php
                    foreach ($аTaxation as $key => $value) {
                        if ($key == get_option('digitalkassa_option_taxation')) {
                            ?>
                                <option selected value="<?php esc_html_e($key, 'text_domain'); ?>"><?php esc_html_e($value, 'text_domain'); ?></option>
                            <?php
                        } else {
                            ?>
                                <option value="<?php esc_html_e($key, 'text_domain'); ?>"><?php esc_html_e($value, 'text_domain'); ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div>
                <label>Тип позиции чека по умолчанию</label>
                <select name="digitalkassa_option_type_of_receipt_position">
                    <?php
                    foreach ($aTypeOfReceiptPosition as $key => $value) {
                        if ($key == get_option('digitalkassa_option_type_of_receipt_position')) {
                            ?>
                                <option selected value="<?php esc_html_e($key, 'text_domain'); ?>"><?php esc_html_e($value, 'text_domain'); ?></option>
                            <?php
                        } else {
                            ?>
                                <option value="<?php esc_html_e($key, 'text_domain'); ?>"><?php esc_html_e($value, 'text_domain'); ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="flex_div w66">
            <div>
                <label>НДС позиции чека по умолчанию</label>
                <select name="digitalkassa_option_vat">
                    <?php
                    foreach ($aVat as $key => $value) {
                        if ($key == get_option('digitalkassa_option_vat')) {
                            ?>
                                <option selected value="<?php esc_html_e($key, 'text_domain'); ?>"><?php esc_html_e($value, 'text_domain'); ?></option>
                            <?php
                        } else {
                            ?>
                                <option value="<?php esc_html_e($key, 'text_domain'); ?>"><?php esc_html_e($value, 'text_domain'); ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div>
                <label>НДС доставки</label>
                <select name="digitalkassa_option_delivery_vat">
                    <?php
                    foreach ($aVat as $key => $value) {
                        if ($key == get_option('digitalkassa_option_delivery_vat')) {
                            ?>
                                <option selected value="<?php esc_html_e($key, 'text_domain'); ?>"><?php esc_html_e($value, 'text_domain'); ?></option>
                            <?php
                        } else {
                            ?>
                                <option value="<?php esc_html_e($key, 'text_domain'); ?>"><?php esc_html_e($value, 'text_domain'); ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes'); ?>" />
        </p>
    </form>
</div>
