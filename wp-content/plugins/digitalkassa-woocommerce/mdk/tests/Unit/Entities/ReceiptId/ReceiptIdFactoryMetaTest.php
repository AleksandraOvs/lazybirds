<?php

use PHPUnit\Framework\TestCase;
use Digitalkassa\MDK\Entities\Receipt;
use Digitalkassa\MDK\Entities\ReceiptId\ReceiptIdFactoryMeta;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @uses Digitalkassa\MDK\Entities\ReceiptId\ReceiptIdFactoryMeta
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptStatus
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptSubType
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptType
 * @uses Digitalkassa\MDK\Entities\Receipt
 */
class ReceiptIdFactoryMetaTest extends TestCase
{
    /**
     * @covers Digitalkassa\MDK\Entities\ReceiptId\ReceiptIdFactoryMeta::build
     * @covers Digitalkassa\MDK\Entities\ReceiptId\ReceiptIdFactoryMeta::verify
     * @covers Digitalkassa\MDK\Entities\ReceiptId\ReceiptIdFactoryMeta::getEngine
     */
    public function test()
    {
        $receiptIdFactory = new ReceiptIdFactoryMeta();

        $receipt = new Receipt();
        $receipt->setOrderId('123');
        $receiptId = $receiptIdFactory->build($receipt);
        $this->assertIsString($receiptId);
        $this->assertTrue($receiptIdFactory->verify($receiptId));
    }
}
