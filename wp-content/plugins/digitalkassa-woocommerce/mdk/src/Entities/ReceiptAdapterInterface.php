<?php

namespace Digitalkassa\MDK\Entities;

use Digitalkassa\MDK\Entities\Primitives\Notify;
use Digitalkassa\MDK\Entities\Primitives\Customer;
use Digitalkassa\MDK\Collections\ReceiptItemCollection;
use Digitalkassa\MDK\Exceptions\Base\InvalidArgumentException;

/**
 * Адаптер чеков из заказов, реализуется на стороне конкретной интеграции
 */
interface ReceiptAdapterInterface
{
    /**
     * Получить коллекцию позиций заказа
     *
     * @throws InvalidArgumentException
     *
     * @param string $orderId
     * @param int $subType подтип создаваемого чека (ReceiptSubType::PRE || ReceiptSubType::FULL)
     * @return ReceiptItemCollection
     */
    public function getItems(string $orderId, string $siteId, int $subType): ReceiptItemCollection;

    /**
     * Получить общую сумму заказа
     *
     * @throws InvalidArgumentException
     *
     * @param string $orderId
     * @return float
     */
    public function getTotal(string $orderId, string $siteId): float;

    /**
     * Получить данные покупателя
     *
     * @param string $orderId
     * @return Customer|null
     */
    public function getCustomer(string $orderId, string $siteId): ?Customer;

    /**
     * Получить данные для уведомления покупателя
     *
     * @param string $orderId
     * @return Notify
     */
    public function getNotify(string $orderId, string $siteId): Notify;
}
