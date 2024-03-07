<?php

use PHPUnit\Framework\TestCase;
use Digitalkassa\MDK\Entities\Primitives\Amount;
use Digitalkassa\MDK\Exceptions\Base\InvalidArgumentException;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @uses Digitalkassa\MDK\Entities\Primitives\Amount
 */
class AmountTest extends TestCase
{
    /**
     * @covers Digitalkassa\MDK\Entities\Primitives\Amount::__construct
     * @covers Digitalkassa\MDK\Entities\Primitives\Amount::setCash
     * @covers Digitalkassa\MDK\Entities\Primitives\Amount::getCash
     * @covers Digitalkassa\MDK\Entities\Primitives\Amount::setCashless
     * @covers Digitalkassa\MDK\Entities\Primitives\Amount::getCashless
     * @covers Digitalkassa\MDK\Entities\Primitives\Amount::setPrepayment
     * @covers Digitalkassa\MDK\Entities\Primitives\Amount::getPrepayment
     * @covers Digitalkassa\MDK\Entities\Primitives\Amount::setPostpayment
     * @covers Digitalkassa\MDK\Entities\Primitives\Amount::getPostpayment
     * @covers Digitalkassa\MDK\Entities\Primitives\Amount::setBarter
     * @covers Digitalkassa\MDK\Entities\Primitives\Amount::getBarter
     */
    public function testSetGetCash()
    {
        $amount = new Amount();
        $this->assertSame($amount, $amount->setCash(100));
        $this->assertSame(100.0, $amount->getCash());

        $this->assertSame($amount, $amount->setCashless(101));
        $this->assertSame(101.0, $amount->getCashless());

        $this->assertSame($amount, $amount->setPrepayment(102));
        $this->assertSame(102.0, $amount->getPrepayment());

        $this->assertSame($amount, $amount->setPostpayment(103));
        $this->assertSame(103.0, $amount->getPostpayment());

        $this->assertSame($amount, $amount->setBarter(104));
        $this->assertSame(104.0, $amount->getBarter());
    }

    /**
     * @covers Digitalkassa\MDK\Entities\Primitives\Amount::__construct
     * @covers Digitalkassa\MDK\Entities\Primitives\Amount::setCash
     */
    public function testSetCashFail()
    {
        $amount = new Amount();
        $this->expectException(InvalidArgumentException::class);
        $amount->setCash(-100);
    }

    /**
     * @covers Digitalkassa\MDK\Entities\Primitives\Amount::__construct
     * @covers Digitalkassa\MDK\Entities\Primitives\Amount::setCashless
     */
    public function testSetCashlessFail()
    {
        $amount = new Amount();
        $this->expectException(InvalidArgumentException::class);
        $amount->setCashless(-100);
    }

    /**
     * @covers Digitalkassa\MDK\Entities\Primitives\Amount::__construct
     * @covers Digitalkassa\MDK\Entities\Primitives\Amount::setPrepayment
     */
    public function testSetPrepaymentFail()
    {
        $amount = new Amount();
        $this->expectException(InvalidArgumentException::class);
        $amount->setPrepayment(-100);
    }

    /**
     * @covers Digitalkassa\MDK\Entities\Primitives\Amount::__construct
     * @covers Digitalkassa\MDK\Entities\Primitives\Amount::setPostpayment
     */
    public function testSetPostpaymentFail()
    {
        $amount = new Amount();
        $this->expectException(InvalidArgumentException::class);
        $amount->setPostpayment(-100);
    }

    /**
     * @covers Digitalkassa\MDK\Entities\Primitives\Amount::__construct
     * @covers Digitalkassa\MDK\Entities\Primitives\Amount::setBarter
     */
    public function testSetBarterFail()
    {
        $amount = new Amount();
        $this->expectException(InvalidArgumentException::class);
        $amount->setBarter(-100);
    }
}
