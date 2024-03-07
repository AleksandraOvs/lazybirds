<?php

use PHPUnit\Framework\TestCase;
use Digitalkassa\MDK\Entities\Primitives\Customer;
use Digitalkassa\MDK\Exceptions\Base\InvalidArgumentException;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @uses Digitalkassa\MDK\Entities\Primitives\Customer
 */
class CustomerTest extends TestCase
{
    /**
     * @covers Digitalkassa\MDK\Entities\Primitives\Customer::__construct
     * @covers Digitalkassa\MDK\Entities\Primitives\Customer::setName
     * @covers Digitalkassa\MDK\Entities\Primitives\Customer::getName
     * @covers Digitalkassa\MDK\Entities\Primitives\Customer::setTin
     * @covers Digitalkassa\MDK\Entities\Primitives\Customer::getTin
     */
    public function testConstructSuccess()
    {
        $customer = new Customer();
        $this->assertSame('', $customer->getName());
        $this->assertSame('', $customer->getTin());

        $customer = new Customer('Тест Тест Тест', '0000000000');
        $this->assertSame('Тест Тест Тест', $customer->getName());
        $this->assertSame('0000000000', $customer->getTin());
    }

    /**
     * @covers Digitalkassa\MDK\Entities\Primitives\Customer::__construct
     * @covers Digitalkassa\MDK\Entities\Primitives\Customer::setName
     */
    public function testConstructFail1()
    {
        $this->expectException(InvalidArgumentException::class);
        $customer = new Customer('', '0000000000');
    }

    /**
     * @covers Digitalkassa\MDK\Entities\Primitives\Customer::__construct
     * @covers Digitalkassa\MDK\Entities\Primitives\Customer::setTin
     */
    public function testConstructFail2()
    {
        $this->expectException(InvalidArgumentException::class);
        $customer = new Customer('Тест Тест Тест', '');
    }

    //######################################################################
}
