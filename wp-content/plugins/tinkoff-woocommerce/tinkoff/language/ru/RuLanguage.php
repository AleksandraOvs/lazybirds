<?php

include_once(dirname(__FILE__).'/../Language.php');

class RuLanguage extends Language {

    public function __construct()
    {

        $this->language['PAYMENT_METHOD_FFD'] = 'Признак способа расчёта';
        $this->language['FULL_PREPAYMENT'] = 'Предоплата 100%';
        $this->language['PREPAYMENT'] = 'Предоплата';
        $this->language['ADVANCE'] = 'Аванс';
        $this->language['FULL_PAYMENT'] = 'Полный расчет';
        $this->language['PARTIAL_PAYMENT'] = 'Частичный расчет и кредит';
        $this->language['CREDIT'] = 'Передача в кредит';
        $this->language['CREDIT_PAYMENT'] = 'Оплата кредита';
        $this->language['PAYMENT_OBJECT_FFD'] = 'Признак предмета расчёта';
        $this->language['COMMODITY'] = 'Товар';
        $this->language['EXCISE'] = 'Подакцизный товар';
        $this->language['JOB'] = 'Работа';
        $this->language['SERVICE'] = 'Услуга';
        $this->language['GAMBLING_BET'] = 'Ставка азартной игры';
        $this->language['GAMBLING_PRIZE'] = 'Выигрыш азартной игры';
        $this->language['LOTTERY'] = 'Лотерейный билет';
        $this->language['LOTTERY_PRIZE'] = 'Выигрыш лотереи';
        $this->language['INTELLECTUAL_ACTIVITY'] = 'Предоставление результатов интеллектуальной деятельности';
        $this->language['PAYMENT'] = 'Платеж';
        $this->language['AGENT_COMMISSION'] = 'Агентское вознаграждение';
        $this->language['COMPOSITE'] = 'Составной предмет расчета';
        $this->language['ANOTHER'] = 'Иной предмет расчета';
        $this->language['EMAIL_COMPANY_PERSONAL'] = 'Введите email компании';
        $this->language['EMAIL_COMPANY'] = 'Email компании';
        $this->language['PAYMENT_METHOD'] = 'Активность способа оплаты';
        $this->language['ACTIVE'] = 'Активен';
        $this->language['PAYMENT_METHOD_NAME'] = 'Название способа оплаты';
        $this->language['PAYMENT_METHOD_USER'] = 'Название способа оплаты, которое увидит пользователь при оформлении заказа';
        $this->language['TINKOFF_BANK'] = 'Тинькофф Банк';
        $this->language['TERMINAL'] = 'Терминал';
        $this->language['SPECIFIED_PERSONAL'] = 'Указан в Личном кабинете https://oplata.tinkoff.ru';
        $this->language['PASSWORD'] = 'Пароль';
        $this->language['DESCRIPTION_PAYMENT_METHOD'] = 'Описание способа оплаты, которое клиент будет видеть на вашем сайте.';
        $this->language['PAYMENT_THROUGH'] = 'Оплата через www.tinkoff.ru';
        $this->language['ORDER_COMPLETION'] = 'Автозавершение заказа';
        $this->language['AUTOMATIC_SUCCESSFUL'] = 'Автоматический перевод заказа в статус "Выполнен" после успешной оплаты';
        $this->language['SEND_DATA_CHECK'] = 'Передавать данные для формирования чека';
        $this->language['DATA_TRANSFER'] = 'Передача данных';
        $this->language['TAX_SYSTEM'] = 'Система налогообложения';
        $this->language['CHOOSE_SYSTEM_STORE'] = 'Выберите систему налогообложения для Вашего магазина';
        $this->language['TOTAL_CH'] = 'Общая СН';
        $this->language['SIMPLIFIED_CH'] = 'Упрощенная СН (доходы)';
        $this->language['SIMPLIFIED__COSTS'] = 'Упрощенная СН (доходы минус расходы)';
        $this->language['SINGLE_IMPUTED_INCOME'] = 'Единый налог на вмененный доход';
        $this->language['UNIFIED_AGRICULTURAL_TAX'] = 'Единый сельскохозяйственный налог';
        $this->language['PATENT_CH'] = 'Патентная СН';
        $this->language['PAYMENT_LANGUAGE'] = 'Язык платежной формы';
        $this->language['CHOOSE_PAYMENT_LANGUAGE'] = 'Выберите язык платежной формы для Вашего магазина';
        $this->language['RUSSIA'] = 'Русский';
        $this->language['ENGLISH'] = 'Английский';
        $this->language['PAYMENT_SUCCESS'] = 'Платеж успешно оплачен';
        $this->language['PAYMENT_NOT_SUCCESS'] = 'Платеж не оплачен';
        $this->language['PAYMENT_THANK'] = 'Благодарим вас за покупку!';
        $this->language['PAYMENT_ERROR'] = 'Во время платежа произошла ошибка. Повторите попытку или обратитесь к администратору';
        $this->language['REQUEST_TO_PAYMENT'] = 'Запрос к платежному сервису был отправлен некорректно';
        $this->language['SETUP_OF_RECEIVING'] = 'Настройка приема электронных платежей через Tinkoff';
        $this->language['TINKOFF_DOES_NOT_SUPPORT'] = 'Tinkoff не поддерживает валюты Вашего магазина.';
        $this->language['GATEWAY_IS_DISABLED'] = 'Шлюз отключен.';
    }
}

