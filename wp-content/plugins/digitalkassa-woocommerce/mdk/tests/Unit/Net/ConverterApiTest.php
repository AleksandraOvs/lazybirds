<?php

use PHPUnit\Framework\TestCase;
use Digitalkassa\MDK\Entities\Receipt;
use Digitalkassa\MDK\Net\ConverterApi;
use Digitalkassa\MDK\Entities\Atoms\Unit;
use Digitalkassa\MDK\Entities\ReceiptItem;
use Digitalkassa\MDK\Entities\Atoms\Taxation;
use Digitalkassa\MDK\Entities\Atoms\ReceiptType;
use Digitalkassa\MDK\Entities\Primitives\Amount;
use Digitalkassa\MDK\Entities\Primitives\Notify;
use Digitalkassa\MDK\Entities\Primitives\Customer;
use Digitalkassa\MDK\Exceptions\ConverterException;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @uses Digitalkassa\MDK\Entities\Primitives\Notify
 * @uses Digitalkassa\MDK\Collections\BaseCollection
 * @uses Digitalkassa\MDK\Entities\AtomAbstract
 * @uses Digitalkassa\MDK\Entities\Atoms\PaymentMethod
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptItemType
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptStatus
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptSubType
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptType
 * @uses Digitalkassa\MDK\Entities\Atoms\Taxation
 * @uses Digitalkassa\MDK\Entities\Atoms\Vat
 * @uses Digitalkassa\MDK\Entities\Atoms\Unit
 * @uses Digitalkassa\MDK\Entities\ConverterAbstract
 * @uses Digitalkassa\MDK\Entities\Primitives\Amount
 * @uses Digitalkassa\MDK\Entities\Receipt
 * @uses Digitalkassa\MDK\Entities\ReceiptItem
 * @uses Digitalkassa\MDK\Entities\Primitives\Customer
 */
class ConverterApiTest extends TestCase
{
    /**
     * @covers Digitalkassa\MDK\Net\ConverterApi::receiptToArray
     * @covers Digitalkassa\MDK\Net\ConverterApi::notifyToArray
     */
    public function testReceiptToArray()
    {
        $conv = new ConverterApi();
        $receipt = new Receipt();
        $receipt
            ->setOrderId('456')
            ->setType(ReceiptType::COMING)
            ->addItem(
                (new ReceiptItem())
                    ->setPrice(100.0)
                    ->setQuantity(2)
                    ->setName('name')
                    ->setItemId('123')
                    ->setUnit(Unit::DEFAULT)
            )
            ->setTaxation(Taxation::ORN)
            ->setAmount((new Amount())->setCashless(200))
            ->setNotify(new Notify('box@domain.zone'))
            ->setCustomer(new Customer('Test'))
            ->setLocation('http://example.com/');

        $this->assertEquals(
            [
                'order_id' => '456',
                'type' => ReceiptType::COMING,
                'items' => [[
                    'type' => 1,
                    'name' => 'name',
                    'price' => 100.0,
                    'quantity' => 2.0,
                    'amount' => 200.0,
                    'payment_method' => 4,
                    'vat' => 6,
                    'item_id' => '123',
                    'unit' => Unit::DEFAULT
                ]],
                'taxation' => Taxation::ORN,
                'amount' => [
                    'cashless' => 200.0
                ],
                'notify' => [
                    'emails' => ['box@domain.zone']
                ],
                'customer' => [
                    'name' => 'Test'
                ],
                'loc' => [
                    'billing_place' => 'http://example.com/'
                ]
            ],
            $conv->receiptToArray($receipt)
        );
    }

    /**
     * @covers Digitalkassa\MDK\Net\ConverterApi::receiptToArray
     */
    public function testReceiptToArrayFailItems()
    {
        $conv = new ConverterApi();
        $receipt = new Receipt();
        $receipt
            ->setType(ReceiptType::COMING)
            ->setTaxation(Taxation::ORN)
            ->setAmount((new Amount())->setCashless(200))
            ->setNotify(new Notify('box@domain.zone'))
            ->setCustomer(new Customer('Test'))
            ->setLocation('http://example.com/');

        $this->expectException(ConverterException::class);
        $conv->receiptToArray($receipt);
    }

    /**
     * @covers Digitalkassa\MDK\Net\ConverterApi::receiptToArray
     */
    public function testReceiptToArrayFailTaxation()
    {
        $conv = new ConverterApi();
        $receipt = new Receipt();
        $receipt
            ->setType(ReceiptType::COMING)
            ->addItem(
                (new ReceiptItem())
                    ->setPrice(100.0)
                    ->setQuantity(2)
                    ->setName('name')
            )
            ->setAmount((new Amount())->setCashless(200))
            ->setNotify(new Notify('box@domain.zone'))
            ->setCustomer(new Customer('Test'))
            ->setLocation('http://example.com/');

        $this->expectException(ConverterException::class);
        $conv->receiptToArray($receipt);
    }

    /**
     * @covers Digitalkassa\MDK\Net\ConverterApi::receiptToArray
     */
    public function testReceiptToArrayFailAmount()
    {
        $conv = new ConverterApi();
        $receipt = new Receipt();
        $receipt
            ->setType(ReceiptType::COMING)
            ->addItem(
                (new ReceiptItem())
                    ->setPrice(100.0)
                    ->setQuantity(2)
                    ->setName('name')
            )
            ->setTaxation(Taxation::ORN)
            ->setNotify(new Notify('box@domain.zone'))
            ->setCustomer(new Customer('Test'))
            ->setLocation('http://example.com/');

        $this->expectException(ConverterException::class);
        $conv->receiptToArray($receipt);
    }

    /**
     * @covers Digitalkassa\MDK\Net\ConverterApi::receiptToArray
     */
    public function testReceiptToArrayFailNotify()
    {
        $conv = new ConverterApi();
        $receipt = new Receipt();
        $receipt
            ->setType(ReceiptType::COMING)
            ->addItem(
                (new ReceiptItem())
                    ->setPrice(100.0)
                    ->setQuantity(2)
                    ->setName('name')
            )
            ->setTaxation(Taxation::ORN)
            ->setAmount((new Amount())->setCashless(200))
            ->setCustomer(new Customer('Test'))
            ->setLocation('http://example.com/');

        $this->expectException(ConverterException::class);
        $conv->receiptToArray($receipt);
    }

    /**
     * @covers Digitalkassa\MDK\Net\ConverterApi::receiptToArray
     */
    public function testReceiptToArrayFailLocation()
    {
        $conv = new ConverterApi();
        $receipt = new Receipt();
        $receipt
            ->setType(ReceiptType::COMING)
            ->addItem(
                (new ReceiptItem())
                    ->setPrice(100.0)
                    ->setQuantity(2)
                    ->setName('name')
            )
            ->setTaxation(Taxation::ORN)
            ->setAmount((new Amount())->setCashless(200))
            ->setNotify(new Notify('box@domain.zone'))
            ->setCustomer(new Customer('Test'));

        $this->expectException(ConverterException::class);
        $conv->receiptToArray($receipt);
    }

    //**********************************************************************

    /**
     * @covers Digitalkassa\MDK\Net\ConverterApi::receiptFromArray
     */
    public function testReceiptFromArray()
    {
        $this->expectException(ConverterException::class);
        $conv = new ConverterApi();
        $conv->receiptFromArray([]);
    }

    //######################################################################

    /**
     * @covers Digitalkassa\MDK\Net\ConverterApi::notifyToArray
     */
    public function testNotifyToArrayEmail()
    {
        $conv = new ConverterApi();
        $notify = new Notify();
        $notify
            ->setEmail('box@domain.zone')
            ->setPhone('+79998887766');

        $this->assertEquals(
            [
                'emails' => ['box@domain.zone'],
                'phone' => '+79998887766',
            ],
            $conv->notifyToArray($notify)
        );
    }

    /**
     * @covers Digitalkassa\MDK\Net\ConverterApi::notifyToArray
     */
    public function testNotifyToArrayPhone()
    {
        $conv = new ConverterApi();
        $notify = new Notify();
        $notify->setPhone('+79998887766');

        $this->assertEquals(
            [
                'phone' => '+79998887766',
            ],
            $conv->notifyToArray($notify)
        );
    }

    /**
     * @covers Digitalkassa\MDK\Net\ConverterApi::notifyToArray
     */
    public function testNotifyToArrayFail()
    {
        $this->expectException(ConverterException::class);
        $conv = new ConverterApi();
        $conv->notifyToArray(new Notify());
    }

    //**********************************************************************

    /**
     * @covers Digitalkassa\MDK\Net\ConverterApi::notifyFromArray
     */
    public function testNotifyFromArray()
    {
        $this->expectException(ConverterException::class);
        $conv = new ConverterApi();
        $conv->notifyFromArray([]);
    }
}
