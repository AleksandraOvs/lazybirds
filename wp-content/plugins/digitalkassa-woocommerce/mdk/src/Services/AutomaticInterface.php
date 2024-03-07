<?php

namespace Digitalkassa\MDK\Services;

use Digitalkassa\MDK\Entities\Receipt;
use Digitalkassa\MDK\Exceptions\StorageException;
use Digitalkassa\MDK\Exceptions\TransferException;
use Digitalkassa\MDK\Exceptions\Services\AutomaticException;
use Digitalkassa\MDK\Exceptions\Base\InvalidArgumentException;
use Digitalkassa\MDK\Exceptions\Services\AutomaticErrorException;

/**
 * Интерфейс сервиса автоматической фискализации заказов
 */
interface AutomaticInterface
{
    /**
     * Автоматическая фискализация прихода по заказу
     *
     * @throws InvalidArgumentException
     * @throws TransferException
     * @throws StorageException
     * @throws AutomaticException
     * @throws AutomaticErrorException
     *
     * @param string $orderId
     * @param string $siteId
     * @param integer|null $receiptSubType подтип чека для конкретизации, либо null для автоматического определения типа
     * @return Receipt
     */
    public function fiscalize(string $orderId, string $siteId = '', int $receiptSubType = null): Receipt;
}
