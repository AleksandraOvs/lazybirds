<?php

namespace Digitalkassa\MDK\Net;

use Digitalkassa\MDK\Entities\Receipt;
use Digitalkassa\MDK\Entities\ConverterAbstract;
use Digitalkassa\MDK\Entities\Primitives\Notify;
use Digitalkassa\MDK\Exceptions\ConverterException;

/**
 * Реализация интерфейса ConverterAbstract для Pangaea API v2
 *
 * @link https://api.digitalkassa.ru/v2/doc
 */
class ConverterApi extends ConverterAbstract
{
    /**
     * @inheritDoc
     */
    public function receiptToArray(Receipt $receipt): array
    {
        $a = [];

        if ($receipt->getItems()->count() == 0) {
            throw new ConverterException("uninitialize required field 'item'");
        }

        if (!$receipt->getTaxation()) {
            throw new ConverterException("uninitialize required field 'taxation'");
        }

        if (!$receipt->getAmount()) {
            throw new ConverterException("uninitialize required field 'amount'");
        }

        if (!$receipt->getNotify()) {
            throw new ConverterException("uninitialize required field 'notify'");
        }

        if (!$receipt->getLocation()) {
            throw new ConverterException("uninitialize required field 'location'");
        }

        $a['order_id'] = $receipt->getOrderId();
        $a['type'] = $receipt->getType();
        $a['items'] = $this->itemsToArray($receipt->getItems());
        $a['taxation'] = $receipt->getTaxation();
        $a['amount'] = $this->amountToArray($receipt->getAmount());
        $a['notify'] = $this->notifyToArray($receipt->getNotify());
        $a['loc'] = [
            'billing_place' => $receipt->getLocation()
        ];

        if ($receipt->getCustomer()) {
            $a['customer'] = $this->customerToArray($receipt->getCustomer());
        }

        return $a;
    }

    /**
     * @inheritDoc
     */
    public function receiptFromArray(array $a): Receipt
    {
        throw new ConverterException('unsupported ' . __CLASS__ . '::' . __METHOD__);
    }

    //######################################################################

    /**
     * @inheritDoc
     */
    public function notifyToArray(Notify $notify): array
    {
        $a = [];

        if ($notify->getEmail()) {
            $a['emails'] = [$notify->getEmail()];
        }
        if ($notify->getPhone()) {
            $a['phone'] = $notify->getPhone();
        }

        if (!$a) {
            throw new ConverterException('invalid notify array');
        }

        return $a;
    }

    /**
     * @inheritDoc
     */
    public function notifyFromArray(array $a): Notify
    {
        throw new ConverterException('unsuported ' . __CLASS__ . '::' . __METHOD__);
    }
}
