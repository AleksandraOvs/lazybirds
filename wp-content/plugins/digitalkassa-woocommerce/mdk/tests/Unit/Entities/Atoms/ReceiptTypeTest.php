<?php

use PHPUnit\Framework\TestCase;
use Digitalkassa\MDK\Entities\Atoms\ReceiptType;
use Digitalkassa\MDK\Exceptions\Base\InvalidArgumentException;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptType
 */
class ReceiptTypeTest extends TestCase
{
    /**
     * @covers Digitalkassa\MDK\Entities\Atoms\ReceiptType::__construct
     * @covers Digitalkassa\MDK\Entities\Atoms\ReceiptType::getCode
     */
    public function test()
    {
        $this->assertSame(
            ReceiptType::COMING,
            (new ReceiptType(ReceiptType::COMING))->getCode()
        );
        $this->assertSame(
            ReceiptType::REFUND_COMING,
            (new ReceiptType(ReceiptType::REFUND_COMING))->getCode()
        );
        $this->assertSame(
            ReceiptType::EXPENCE,
            (new ReceiptType(ReceiptType::EXPENCE))->getCode()
        );
        $this->assertSame(
            ReceiptType::REFUND_EXPENSE,
            (new ReceiptType(ReceiptType::REFUND_EXPENSE))->getCode()
        );

        $this->expectException(InvalidArgumentException::class);
        new ReceiptType(0);
    }

    /**
     * @covers Digitalkassa\MDK\Entities\Atoms\ReceiptType::all
     */
    public function testAll()
    {
        $a = ReceiptType::all();
        $this->assertIsArray($a);
        $this->assertContainsOnlyInstancesOf(ReceiptType::class, $a);
        $this->assertCount(4, $a);
    }
}
