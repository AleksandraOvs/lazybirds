<?php

use PHPUnit\Framework\TestCase;
use Digitalkassa\MDK\Entities\Receipt;
use Digitalkassa\MDK\Entities\ReceiptItem;
use Digitalkassa\MDK\Entities\Atoms\ReceiptType;
use Digitalkassa\MDK\Collections\ReceiptCollection;
use Digitalkassa\MDK\Entities\Atoms\ReceiptSubType;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @uses Digitalkassa\MDK\Collections\ReceiptCollection
 * @uses Digitalkassa\MDK\Collections\BaseCollection
 * @uses Digitalkassa\MDK\Collections\ReceiptItemCollection
 * @uses Digitalkassa\MDK\Entities\AtomAbstract
 * @uses Digitalkassa\MDK\Entities\Atoms\PaymentMethod
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptItemType
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptStatus
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptSubType
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptType
 * @uses Digitalkassa\MDK\Entities\Atoms\Vat
 * @uses Digitalkassa\MDK\Entities\Atoms\Unit
 * @uses Digitalkassa\MDK\Entities\Receipt
 * @uses Digitalkassa\MDK\Entities\ReceiptItem
 */
class ReceiptCollectionTest extends TestCase
{
    /**
     * @covers Digitalkassa\MDK\Collections\ReceiptCollection::getAmount
     * @covers Digitalkassa\MDK\Collections\ReceiptCollection::merge
     * @covers Digitalkassa\MDK\Collections\ReceiptCollection::getByType
     * @covers Digitalkassa\MDK\Collections\ReceiptCollection::offsetGet
     * @covers Digitalkassa\MDK\Collections\ReceiptCollection::current
     */
    public function test()
    {
        $receipts = new ReceiptCollection();
        $receipts[] = (new Receipt())
            ->setType(ReceiptType::COMING)
            ->setSubType(ReceiptSubType::FULL)
            ->addItem(
                (new ReceiptItem())
                    ->setPrice(100)
            );

        $this->assertSame(100.0, $receipts->getAmount());

        $receipts2 = new ReceiptCollection();
        $receipts2[] = (new Receipt())
            ->setType(ReceiptType::REFUND_COMING)
            ->addItem(
                (new ReceiptItem())
                    ->setPrice(200)
            );

        $this->assertInstanceOf(ReceiptCollection::class, $receipts->merge($receipts2));

        $this->assertSame(300.0, $receipts->getAmount());

        $this->assertInstanceOf(Receipt::class, $receipts->getByType(ReceiptType::COMING, ReceiptSubType::FULL));
        $this->assertInstanceOf(Receipt::class, $receipts->getByType(ReceiptType::REFUND_COMING));
        $this->assertNull($receipts->getByType(ReceiptType::REFUND_COMING, ReceiptSubType::FULL));

        $this->assertInstanceOf(Receipt::class, $receipts[0]);
        $receipts->rewind();
        $this->assertInstanceOf(Receipt::class, $receipts->current());
    }
}
