<?php

namespace Digitalkassa\MDK\Services;

use Digitalkassa\MDK\Entities\Receipt;
use Digitalkassa\MDK\Net\TransferInterface;
use Digitalkassa\MDK\Storage\ReceiptFilter;
use Digitalkassa\MDK\Settings\SettingsAbstract;
use Digitalkassa\MDK\Entities\Atoms\ReceiptType;
use Digitalkassa\MDK\Entities\Primitives\Amount;
use Digitalkassa\MDK\Entities\Atoms\ReceiptStatus;
use Digitalkassa\MDK\Exceptions\TransferException;
use Digitalkassa\MDK\Entities\Atoms\ReceiptSubType;
use Digitalkassa\MDK\Exceptions\NetConnectException;
use Digitalkassa\MDK\Storage\ReceiptStorageInterface;
use Digitalkassa\MDK\Entities\ReceiptAdapterInterface;
use Digitalkassa\MDK\Exceptions\Services\AutomaticException;
use Digitalkassa\MDK\Exceptions\Base\InvalidArgumentException;
use Digitalkassa\MDK\Exceptions\Services\AutomaticErrorException;
use Digitalkassa\MDK\Entities\ReceiptId\ReceiptIdFactoryInterface;

/**
 * Базовая реализация AutomaticInterface
 */
class AutomaticBase implements AutomaticInterface
{
    /**
     * @param SettingsAbstract $settings
     * @param ReceiptStorageInterface $receiptStorage
     * @param TransferInterface $transfer
     * @param ReceiptAdapterInterface $receiptAdapter
     */
    public function __construct(
        SettingsAbstract $settings,
        ReceiptStorageInterface $receiptStorage,
        TransferInterface $transfer,
        ReceiptAdapterInterface $receiptAdapter,
        ReceiptIdFactoryInterface $receiptIdFactory
    ) {
        $this->settings = $settings;
        $this->receiptStorage = $receiptStorage;
        $this->transfer = $transfer;
        $this->receiptAdapter = $receiptAdapter;
        $this->receiptIdFactory = $receiptIdFactory;
    }

    /**
     * @inheritDoc
     */
    public function fiscalize(string $orderId, string $siteId = '', int $receiptSubType = null): Receipt
    {
        // получить коллекцию действительных чеков по текущему заказу на сайте
        $receipts = $this->receiptStorage->getCollection(
            (new ReceiptFilter())
                ->setOrderId($orderId)
                ->setSiteId($siteId)
                ->setStatus([ReceiptStatus::ACCEPTED, ReceiptStatus::COMPLETED, ReceiptStatus::PREPARED])
        );

        if ($receiptSubType === null) {
            $receiptSubType = (
                (
                    !$receipts->getByType(ReceiptType::COMING, ReceiptSubType::PRE)
                    && $this->settings->getScheme($siteId) == SettingsAbstract::SCHEME_PRE_FULL
                )
                ? ReceiptSubType::PRE
                : ReceiptSubType::FULL
            );
        }

        if ($receipts->getByType(ReceiptType::COMING, $receiptSubType)) {
            throw new AutomaticException("В заказе уже есть такой чек");
        }

        if ($receipts->getByType(ReceiptType::COMING, ReceiptSubType::FULL)) {
            throw new AutomaticException("В заказе уже есть второй чек");
        }

        try {
            $total = $this->receiptAdapter->getTotal($orderId, $siteId);
            $items = $this->receiptAdapter->getItems($orderId, $siteId, $receiptSubType);
            $customer = $this->receiptAdapter->getCustomer($orderId, $siteId);
            $notify = $this->receiptAdapter->getNotify($orderId, $siteId);
        } catch (InvalidArgumentException $e) {
            throw $e;
        }

        $amount = new Amount();

        // если пробиваем второй чек и был чек предоплаты
        if (
            $receiptSubType == ReceiptSubType::FULL
            && ($receiptPre = $receipts->getByType(ReceiptType::COMING, ReceiptSubType::PRE))
        ) {
            if (($totalPre = $receiptPre->getItems()->getAmount()) != $total) {
                throw new AutomaticErrorException(sprintf(
                    "Сумма второго чека '%01.2f' не равна сумме первого чека '%01.2f'",
                    $total,
                    $totalPre
                ));
            }
            $amount->setPrepayment($total);
        } else {
            $amount->setCashless($total);
        }

        $receipt = new Receipt();
        $receipt->setOrderId($orderId);
        $receipt->setSiteId($siteId);
        $receipt->setType(ReceiptType::COMING);
        $receipt->setSubType($receiptSubType);
        $receipt->setItems($items);
        $receipt->setCustomer($customer);
        $receipt->setNotify($notify);
        $receipt->setAmount($amount);
        $receipt->setTaxation($this->settings->getTaxation($siteId));
        $receipt->setLocation($this->settings->getLocation($siteId));
        $receipt->setReceiptId($this->receiptIdFactory->build($receipt));

        $receiptStatus = new ReceiptStatus(ReceiptStatus::PREPARED);
        try {
            $receiptStatus = $this->transfer->sendReceipt($this->settings->extrudeConn($siteId), $receipt);
            $receipt->setStatus($receiptStatus);
        } catch (TransferException $e) {
            $receiptStatus = new ReceiptStatus($e->getCode());
            if ($receiptStatus->getCode() == ReceiptStatus::ERROR) {
                throw $e;
            } elseif ($receiptStatus->getCode() == ReceiptStatus::PREPARED) {
                $receipt->setReceiptId($this->receiptIdFactory->build($receipt));
            }
            $receipt->setStatus($receiptStatus);
        } catch (NetConnectException $e) {
        } finally {
            $this->receiptStorage->save($receipt);
        }

        return $receipt;
    }

    //######################################################################
    // PRIVATE
    //######################################################################

    /** @var SettingsAbstract */
    private $settings = null;

    /** @var ReceiptStorageInterface */
    private $receiptStorage = null;

    /** @var ReceiptAdapterInterface */
    private $receiptAdapter = null;

    /** @var TransferInterface */
    private $transfer = null;

    /** @var ReceiptIdFactoryInterface */
    private $receiptIdFactory = null;
}
