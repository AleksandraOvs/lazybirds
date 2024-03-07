<?php

use Digitalkassa\MDK\Entities\Atoms\Vat;
use Digitalkassa\MDK\Entities\ReceiptItem;
use Digitalkassa\MDK\Entities\Primitives\Notify;
use Digitalkassa\MDK\Settings\SettingsAbstract;
use Digitalkassa\MDK\Entities\Atoms\PaymentMethod;
use Digitalkassa\MDK\Entities\Primitives\Customer;
use Digitalkassa\MDK\Entities\Atoms\ReceiptSubType;
use Digitalkassa\MDK\Entities\ReceiptAdapterInterface;
use Digitalkassa\MDK\Collections\ReceiptItemCollection;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
class ReceiptAdapterConcrete implements ReceiptAdapterInterface
{
    public function __construct(db $db, SettingsAbstract $settings)
    {
        $this->db = $db;
        $this->settings = $settings;
    }

    public function getItems(string $orderId, ?string $siteId, int $subType): ReceiptItemCollection
    {
        $paymentMethod = $this->getPaymentMethod($subType);

        $a = json_decode($this->get($orderId)['items'], true);
        $items = new ReceiptItemCollection();
        foreach ($a as $value) {
            $items[] = (new ReceiptItem())
                ->setType($this->settings->getTypeDefaultItems())
                ->setName($value['name'])
                ->setPrice($value['price'])
                ->setQuantity($value['quantity'])
                ->setPaymentMethod($paymentMethod)
                ->setVat(new Vat($this->settings->getVatDefaultItems()));
        }

        return $items;
    }

    public function getTotal(string $orderId, ?string $siteId): float
    {
        $items = $this->getItems($orderId, $siteId, ReceiptSubType::PRE);
        return $items->getAmount();
    }

    public function getCustomer(string $orderId, ?string $siteId): ?Customer
    {
        $a = $this->get($orderId);
        return new Customer($a['customer']);
    }

    public function getNotify(string $orderId, ?string $siteId): Notify
    {
        $a = $this->get($orderId);
        return new Notify($a['notify']);
    }

    //######################################################################

    private $db = null;
    private $settings = null;

    //######################################################################

    private function get(string $orderId): array
    {
        $sql = "SELECT * FROM `orders` WHERE `id`=$orderId";
        return $this->db->query($sql, true)[0];
    }

    private function getPaymentMethod(int $subType): int
    {
        $paymentMethod = null;

        switch ($subType) {
            case ReceiptSubType::PRE:
                $paymentMethod = PaymentMethod::PREPAYMENT_FULL;
                break;
            case ReceiptSubType::FULL:
                $paymentMethod = PaymentMethod::PAYMENT_FULL;
                break;
            default:
                throw new InvalidArgumentException("invalid subType '$subType'");
        }

        return $paymentMethod;
    }
}
