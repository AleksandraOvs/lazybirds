<?php

use PHPUnit\Framework\TestCase;
use Digitalkassa\MDK\Entities\ReceiptItem;
use Digitalkassa\MDK\Collections\ReceiptItemCollection;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @uses Digitalkassa\MDK\Collections\ReceiptItemCollection
 * @uses Digitalkassa\MDK\Collections\BaseCollection
 * @uses Digitalkassa\MDK\Entities\ReceiptItem
 * @uses Digitalkassa\MDK\Entities\Atoms\PaymentMethod
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptItemType
 * @uses Digitalkassa\MDK\Entities\Atoms\Vat
 * @uses Digitalkassa\MDK\Entities\Atoms\Unit
 */
class ReceiptItemCollectionTest extends TestCase
{
    /**
     * @covers Digitalkassa\MDK\Collections\ReceiptItemCollection::getAmount
     */
    public function test()
    {
        $items = new ReceiptItemCollection();
        $items[] = (new ReceiptItem())
            ->setPrice(100);
        $items[] = (new ReceiptItem())
            ->setPrice(100);

        $this->assertSame(200.0, $items->getAmount());
    }
}
