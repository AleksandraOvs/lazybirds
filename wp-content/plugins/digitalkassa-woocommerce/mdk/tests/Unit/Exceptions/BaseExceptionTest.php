<?php

use PHPUnit\Framework\TestCase;
use Digitalkassa\MDK\Exceptions\BaseException;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @uses Digitalkassa\MDK\Exceptions\BaseException
 */
class BaseExceptionTest extends TestCase
{
    /**
     * @covers Digitalkassa\MDK\Exceptions\BaseException::toArray
     */
    public function testToArray()
    {
        $exception = new BaseException('message', 1);
        $this->assertEquals(
            [
                'code' => 1,
                'message' => 'message'
            ],
            $exception->toArray()
        );
    }
}
