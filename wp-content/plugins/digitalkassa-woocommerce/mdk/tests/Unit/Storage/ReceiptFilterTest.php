<?php

use PHPUnit\Framework\TestCase;
use Digitalkassa\MDK\Storage\ReceiptFilter;
use Digitalkassa\MDK\Entities\Atoms\ReceiptType;
use Digitalkassa\MDK\Entities\Atoms\ReceiptStatus;
use Digitalkassa\MDK\Entities\Atoms\ReceiptSubType;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @uses Digitalkassa\MDK\Storage\ReceiptFilter
 */
class ReceiptFilterTest extends TestCase
{
    /**
     * @covers Digitalkassa\MDK\Storage\ReceiptFilter::setId
     * @covers Digitalkassa\MDK\Storage\ReceiptFilter::setType
     * @covers Digitalkassa\MDK\Storage\ReceiptFilter::setSubType
     * @covers Digitalkassa\MDK\Storage\ReceiptFilter::setStatus
     * @covers Digitalkassa\MDK\Storage\ReceiptFilter::setOrderId
     * @covers Digitalkassa\MDK\Storage\ReceiptFilter::setSiteId
     * @covers Digitalkassa\MDK\Storage\ReceiptFilter::toArray
     */
    public function test()
    {
        $filter = new ReceiptFilter();
        $filter->setType(ReceiptType::COMING);
        $this->assertSame(
            [
                'type' => [
                    'value' => ReceiptType::COMING,
                    'op' => ReceiptFilter::OP_EQ
                ]
            ],
            $filter->toArray()
        );

        $filter = new ReceiptFilter();
        $filter->setType(ReceiptType::COMING, ReceiptFilter::OP_GT);
        $filter->setSubType(ReceiptSubType::FULL, ReceiptFilter::OP_LT);
        $filter->setStatus(ReceiptStatus::COMPLETED, ReceiptFilter::OP_NOTEQ);
        $filter->setOrderId('0');
        $filter->setId('0', ReceiptFilter::OP_GT);
        $filter->setSiteId('0', ReceiptFilter::OP_GT);
        $this->assertEquals(
            [
                'type' => [
                    'value' => ReceiptType::COMING,
                    'op' => ReceiptFilter::OP_GT
                ],
                'subtype' => [
                    'value' => ReceiptSubType::FULL,
                    'op' => ReceiptFilter::OP_LT
                ],
                'status' => [
                    'value' => ReceiptStatus::COMPLETED,
                    'op' => ReceiptFilter::OP_NOTEQ
                ],
                'order_id' => [
                    'value' => '0',
                    'op' => ReceiptFilter::OP_EQ
                ],
                'id' => [
                    'value' => '0',
                    'op' => ReceiptFilter::OP_GT
                ],
                'site_id' => [
                    'value' => '0',
                    'op' => ReceiptFilter::OP_GT
                ]
            ],
            $filter->toArray()
        );
    }
}
