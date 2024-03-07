<?php

use PHPUnit\Framework\TestCase;
use Digitalkassa\MDK\Entities\Atoms\ReceiptSubType;
use Digitalkassa\MDK\Exceptions\Base\InvalidArgumentException;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptSubType
 */
class ReceiptSubTypeTest extends TestCase
{
    /**
     * @covers Digitalkassa\MDK\Entities\Atoms\ReceiptSubType::__construct
     * @covers Digitalkassa\MDK\Entities\Atoms\ReceiptSubType::getCode
     */
    public function testResponseCode()
    {
        $this->assertSame(
            ReceiptSubType::PRE,
            (new ReceiptSubType(ReceiptSubType::PRE))->getCode()
        );
        $this->assertSame(
            ReceiptSubType::FULL,
            (new ReceiptSubType(ReceiptSubType::FULL))->getCode()
        );

        $this->expectException(InvalidArgumentException::class);
        $vat = new ReceiptSubType(3);
    }

    /**
     * @covers Digitalkassa\MDK\Entities\Atoms\ReceiptSubType::all
     */
    public function testAll()
    {
        $a = ReceiptSubType::all();
        $this->assertIsArray($a);
        $this->assertContainsOnlyInstancesOf(ReceiptSubType::class, $a);
        $this->assertCount(2, $a);
    }
}
