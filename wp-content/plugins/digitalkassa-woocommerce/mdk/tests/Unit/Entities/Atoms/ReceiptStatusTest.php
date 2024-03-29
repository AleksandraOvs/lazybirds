<?php

use PHPUnit\Framework\TestCase;
use Digitalkassa\MDK\Entities\Atoms\ReceiptStatus;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptStatus
 */
class ReceiptStatusTest extends TestCase
{
    /**
     * @covers Digitalkassa\MDK\Entities\Atoms\ReceiptStatus::__construct
     * @covers Digitalkassa\MDK\Entities\Atoms\ReceiptStatus::getCode
     */
    public function testResponseCode()
    {
        $this->assertSame(
            ReceiptStatus::COMPLETED,
            (new ReceiptStatus(200))->getCode()
        );
        $this->assertSame(
            ReceiptStatus::COMPLETED,
            (new ReceiptStatus(201))->getCode()
        );

        $this->assertSame(
            ReceiptStatus::ACCEPTED,
            (new ReceiptStatus(202))->getCode()
        );

        $this->assertSame(
            ReceiptStatus::PREPARED,
            (new ReceiptStatus(500))->getCode()
        );
        $this->assertSame(
            ReceiptStatus::PREPARED,
            (new ReceiptStatus(501))->getCode()
        );
        $this->assertSame(
            ReceiptStatus::PREPARED,
            (new ReceiptStatus(502))->getCode()
        );
        $this->assertSame(
            ReceiptStatus::PREPARED,
            (new ReceiptStatus(503))->getCode()
        );
        $this->assertSame(
            ReceiptStatus::PREPARED,
            (new ReceiptStatus(504))->getCode()
        );

        $this->assertSame(
            ReceiptStatus::ERROR,
            (new ReceiptStatus(401))->getCode()
        );
        $this->assertSame(
            ReceiptStatus::ERROR,
            (new ReceiptStatus(404))->getCode()
        );

        $this->assertSame(
            ReceiptStatus::ERROR,
            (new ReceiptStatus(400))->getCode()
        );
        $this->assertSame(
            ReceiptStatus::ERROR,
            (new ReceiptStatus(402))->getCode()
        );
        $this->assertSame(
            ReceiptStatus::ERROR,
            (new ReceiptStatus(403))->getCode()
        );
        $this->assertSame(
            ReceiptStatus::ERROR,
            (new ReceiptStatus(406))->getCode()
        );
        $this->assertSame(
            ReceiptStatus::ERROR,
            (new ReceiptStatus(409))->getCode()
        );
        $this->assertSame(
            ReceiptStatus::ERROR,
            (new ReceiptStatus(422))->getCode()
        );
    }

    /**
     * @covers Digitalkassa\MDK\Entities\Atoms\ReceiptStatus::__construct
     * @covers Digitalkassa\MDK\Entities\Atoms\ReceiptStatus::getCode
     */
    public function testCode()
    {
        $this->assertSame(
            ReceiptStatus::PREPARED,
            (new ReceiptStatus(ReceiptStatus::PREPARED))->getCode()
        );
        $this->assertSame(
            ReceiptStatus::COMPLETED,
            (new ReceiptStatus(ReceiptStatus::COMPLETED))->getCode()
        );
        $this->assertSame(
            ReceiptStatus::ACCEPTED,
            (new ReceiptStatus(ReceiptStatus::ACCEPTED))->getCode()
        );
        $this->assertSame(
            ReceiptStatus::ERROR,
            (new ReceiptStatus(ReceiptStatus::ERROR))->getCode()
        );

        $this->assertSame(
            ReceiptStatus::EXPIRED,
            (new ReceiptStatus(ReceiptStatus::EXPIRED))->getCode()
        );
    }

    /**
     * @covers Digitalkassa\MDK\Entities\Atoms\ReceiptStatus::all
     */
    public function testAll()
    {
        $a = ReceiptStatus::all();
        $this->assertIsArray($a);
        $this->assertContainsOnlyInstancesOf(ReceiptStatus::class, $a);
        $this->assertCount(5, $a);
    }
}
