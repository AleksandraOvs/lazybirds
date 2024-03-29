<?php

use PHPUnit\Framework\TestCase;
use Digitalkassa\MDK\Entities\Atoms\Vat;
use Digitalkassa\MDK\Entities\Atoms\Unit;
use Digitalkassa\MDK\Entities\ReceiptItem;
use Digitalkassa\MDK\Entities\ConverterAbstract;
use Digitalkassa\MDK\Entities\Primitives\Amount;
use Digitalkassa\MDK\Entities\Primitives\Notify;
use Digitalkassa\MDK\Entities\Atoms\PaymentMethod;
use Digitalkassa\MDK\Entities\Primitives\Customer;
use Digitalkassa\MDK\Exceptions\ConverterException;
use Digitalkassa\MDK\Entities\Atoms\ReceiptItemType;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @uses Digitalkassa\MDK\Entities\ConverterAbstract
 * @uses Digitalkassa\MDK\Entities\ReceiptItem
 * @uses Digitalkassa\MDK\Collections\BaseCollection
 * @uses Digitalkassa\MDK\Entities\AtomAbstract
 * @uses Digitalkassa\MDK\Entities\Atoms\PaymentMethod
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptItemType
 * @uses Digitalkassa\MDK\Entities\Atoms\Vat
 * @uses Digitalkassa\MDK\Entities\Atoms\Unit
 * @uses Digitalkassa\MDK\Entities\Primitives\Amount
 * @uses Digitalkassa\MDK\Entities\Primitives\Customer
 * @uses Digitalkassa\MDK\Entities\Primitives\Notify
 */
class ConverterTest extends TestCase
{
    private $converter = null;

    protected function setUp(): void
    {
        $this->converter = $this->getMockForAbstractClass(ConverterAbstract::class);
    }

    //######################################################################

    /**
     * @covers Digitalkassa\MDK\Entities\ConverterAbstract::amountToArray
     */
    public function testAmountToArray()
    {
        $amount = new Amount();
        $amount
            ->setCashless(100)
            ->setCash(200)
            ->setPrepayment(300)
            ->setPostpayment(400)
            ->setBarter(500);

        $this->assertEquals(
            [
                'cashless' => 100.0,
                'cash' => 200.0,
                'prepayment' => 300.0,
                'postpayment' => 400.0,
                'barter' => 500.0,
            ],
            $this->converter->amountToArray($amount)
        );

        $this->expectException(ConverterException::class);
        $this->converter->amountToArray(new Amount());
    }

    /**
     * @covers Digitalkassa\MDK\Entities\ConverterAbstract::amountFromArray
     */
    public function testAmountFromArray()
    {
        $amount = $this->converter->amountFromArray([
            'cashless' => 100.0,
            'cash' => 200.0,
            'prepayment' => 300.0,
            'postpayment' => 400.0,
            'barter' => 500.0,
        ]);
        $this->assertSame(100.0, $amount->getCashless());
        $this->assertSame(200.0, $amount->getCash());
        $this->assertSame(300.0, $amount->getPrepayment());
        $this->assertSame(400.0, $amount->getPostpayment());
        $this->assertSame(500.0, $amount->getBarter());

        $this->expectException(ConverterException::class);
        $this->converter->amountFromArray([]);
    }

    /**
     * @covers Digitalkassa\MDK\Entities\ConverterAbstract::amountFromArray
     */
    public function testAmountFromArrayError()
    {
        $this->expectException(ConverterException::class);
        $this->converter->amountFromArray([
            'cashless' => -100.0,
        ]);
    }

    //######################################################################

    /**
     * @covers Digitalkassa\MDK\Entities\ConverterAbstract::customerToArray
     */
    public function testCustomerToArray()
    {
        $customer = new Customer('Тест Тест Тест', '0000000000');
        $this->assertSame(
            [
                'name' => 'Тест Тест Тест',
                'tin' => '0000000000'
            ],
            $this->converter->customerToArray($customer)
        );

        $this->expectException(ConverterException::class);
        $this->converter->customerToArray(new Customer());
    }

    /**
     * @covers Digitalkassa\MDK\Entities\ConverterAbstract::customerFromArray
     */
    public function testCustomerFromArray()
    {
        $customer = $this->converter->customerFromArray([
            'name' => 'Тест Тест Тест',
            'tin' => '0000000000'
        ]);
        $this->assertSame('Тест Тест Тест', $customer->getName());
        $this->assertSame('0000000000', $customer->getTin());

        $this->expectException(ConverterException::class);
        $this->converter->customerFromArray([]);
    }

    /**
     * @covers Digitalkassa\MDK\Entities\ConverterAbstract::customerFromArray
     */
    public function testCustomerFromArrayError()
    {
        $this->expectException(ConverterException::class);
        $this->converter->customerFromArray([
            'name' => ''
        ]);
    }

    //######################################################################

    /**
     * @covers Digitalkassa\MDK\Entities\ConverterAbstract::notifyToArray
     */
    public function testNotifyToArray()
    {
        $notify = new Notify();
        $notify->setEmail('box@domain.zone');
        $notify->setPhone('+79998887766');
        $this->assertEquals(
            [
                'phone' => '+79998887766',
                'email' => 'box@domain.zone'
            ],
            $this->converter->notifyToArray($notify)
        );

        $this->expectException(ConverterException::class);
        $this->converter->notifyToArray(new Notify());
    }

    /**
     * @covers Digitalkassa\MDK\Entities\ConverterAbstract::notifyFromArray
     */
    public function testNotifyFromArray()
    {
        $notify = $this->converter->notifyFromArray([
            'email' => 'box@domain.zone',
            'phone' => '+79998887766',
        ]);
        $this->assertSame('box@domain.zone', $notify->getEmail());
        $this->assertSame('+79998887766', $notify->getPhone());

        $this->expectException(ConverterException::class);
        $this->converter->notifyFromArray([]);
    }

    /**
     * @covers Digitalkassa\MDK\Entities\ConverterAbstract::notifyFromArray
     */
    public function testNotifyFromArrayError()
    {
        $this->expectException(ConverterException::class);
        $this->converter->notifyFromArray([
            'email' => 'box @domain.zone',
            'phone' => '+7999888776600',
        ]);
    }

    //######################################################################
    //######################################################################
    //######################################################################

    /**
     * @covers Digitalkassa\MDK\Entities\ConverterAbstract::itemFromArray
     * @covers Digitalkassa\MDK\Entities\ConverterAbstract::itemToArray
     */
    public function testItemToFromArray()
    {
        $aOut = [
            'type' => ReceiptItemType::PRODUCT,
            'name' => 'name',
            'price' => 100.0,
            'quantity' => 2.0,
            'amount' => 200.0,
            'payment_method' => PaymentMethod::PREPAYMENT_FULL,
            'vat' => Vat::CODE_WITHOUT,
            'item_id' => '123',
            'unit' => Unit::DEFAULT
        ];
        $receiptItem = $this->converter->itemFromArray($aOut);
        $this->assertEquals($aOut, $this->converter->itemToArray($receiptItem));
    }

    //**********************************************************************

    /**
     * @covers Digitalkassa\MDK\Entities\ConverterAbstract::itemToArray
     */
    public function testItemToArrayEmpty()
    {
        $this->expectException(ConverterException::class);
        $this->converter->itemToArray(new ReceiptItem());
    }

    /**
     * @covers Digitalkassa\MDK\Entities\ConverterAbstract::itemToArray
     */
    public function testItemToArrayPartial1()
    {
        $this->expectException(ConverterException::class);
        $this->converter->itemToArray(
            (new ReceiptItem())->setPrice(10)
        );
    }

    /**
     * @covers Digitalkassa\MDK\Entities\ConverterAbstract::itemToArray
     */
    public function testItemToArrayPartial2()
    {
        $this->expectException(ConverterException::class);
        $this->converter->itemToArray(
            (new ReceiptItem())->setName('name')
        );
    }

    //**********************************************************************

    /**
     * @covers Digitalkassa\MDK\Entities\ConverterAbstract::itemFromArray
     */
    public function testItemFromArrayInvalid()
    {
        $this->expectException(ConverterException::class);
        $this->converter->itemFromArray([]);
    }

    /**
     * @covers Digitalkassa\MDK\Entities\ConverterAbstract::itemFromArray
     */
    public function testItemFromArrayPartial()
    {
        $this->expectException(ConverterException::class);
        $this->converter->itemFromArray(['name' => 'name']);
    }

    /**
     * @covers Digitalkassa\MDK\Entities\ConverterAbstract::itemFromArray
     */
    public function testItemFromArrayError()
    {
        $aOut = [
            'type' => ReceiptItemType::PRODUCT,
            'name' => 'name',
            'price' => -100.0,
            'quantity' => 2.0,
            'amount' => 200.0,
            'payment_method' => PaymentMethod::PREPAYMENT_FULL,
            'vat' => Vat::CODE_WITHOUT
        ];
        $this->expectException(ConverterException::class);
        $this->converter->itemFromArray($aOut);
    }

    //######################################################################

    /**
     * @covers Digitalkassa\MDK\Entities\ConverterAbstract::itemsFromArray
     * @covers Digitalkassa\MDK\Entities\ConverterAbstract::itemsToArray
     */
    public function testItemsToArray()
    {
        $item = [
            'type' => ReceiptItemType::PRODUCT,
            'name' => 'name',
            'price' => 100.0,
            'quantity' => 2.0,
            'amount' => 200.0,
            'payment_method' => PaymentMethod::PREPAYMENT_FULL,
            'vat' => Vat::CODE_WITHOUT,
            'item_id' => '123',
            'unit' => Unit::DEFAULT
        ];
        $items = [];
        $items[] = $item;
        $items[] = $item;
        $items[] = $item;

        $receiptItems = $this->converter->itemsFromArray($items);
        $this->assertEquals($items, $this->converter->itemsToArray($receiptItems));
    }
}
