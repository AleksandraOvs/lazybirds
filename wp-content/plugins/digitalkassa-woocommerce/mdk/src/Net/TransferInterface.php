<?php

namespace Digitalkassa\MDK\Net;

use Digitalkassa\MDK\Entities\Receipt;
use Digitalkassa\MDK\Settings\SettingsConn;
use Digitalkassa\MDK\Entities\Atoms\ReceiptStatus;
use Digitalkassa\MDK\Exceptions\TransferException;
use Digitalkassa\MDK\Exceptions\NetConnectException;

/**
 * Трансфер для взаимодействия с сервером фискализации Pangaea API v2
 * @link https://api.digitalkassa.ru/v2/doc
 */
interface TransferInterface
{
    /**
     * Получение информации о группе касс
     *
     * @link https://api.digitalkassa.ru/v2/doc#c_groups__c_group_id_
     *
     * @throws TransferException
     * @throws NetConnectException
     *
     * @param SettingsConn $settingsConn
     * @return \stdClass
     */
    public function getCashbox(SettingsConn $settingsConn): \stdClass;

    /**
     * Отправка чека на фискализацию
     *
     * @link https://api.digitalkassa.ru/v2/doc#c_groups__c_group_id__receipts_online_store_agent__receipt_id__post
     *
     * @throws TransferException
     * @throws NetConnectException
     *
     * @param SettingsConn $settingsConn
     * @param Receipt $receipt
     * @return ReceiptStatus
     */
    public function sendReceipt(SettingsConn $settingsConn, Receipt $receipt): ReceiptStatus;
}
