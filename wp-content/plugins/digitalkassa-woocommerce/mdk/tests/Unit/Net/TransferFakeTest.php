<?php

use Digitalkassa\MDK\Net\Transfer;
use PHPUnit\Framework\TestCase;
use Digitalkassa\MDK\Entities\Receipt;
use Digitalkassa\MDK\Settings\SettingsConn;
use Digitalkassa\MDK\Net\NetClientInterface;
use Digitalkassa\MDK\Settings\SettingsAbstract;
use Digitalkassa\MDK\Entities\ConverterAbstract;
use Digitalkassa\MDK\Entities\Atoms\ReceiptStatus;
use Digitalkassa\MDK\Exceptions\TransferException;
use Digitalkassa\MDK\Exceptions\ConverterException;
use Digitalkassa\MDK\Exceptions\NetConnectException;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @uses Digitalkassa\MDK\Net\Transfer
 * @uses Digitalkassa\MDK\Exceptions\TransferException
 * @uses Digitalkassa\MDK\Entities\Receipt
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptType
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptSubType
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptStatus
 * @uses Digitalkassa\MDK\Entities\AtomAbstract
 * @uses Digitalkassa\MDK\Exceptions\BaseException
 * @uses Digitalkassa\MDK\Settings\SettingsConn
 */
class TransferFakeTest extends TestCase
{
    /** @var NetClientInterface */
    private $client;

    /** @var ConverterAbstract */
    private $converter;

    /** @var SettingsAbstract */
    private $settings;

    //######################################################################

    protected function setUp(): void
    {
        $this->client = $this->createMock(NetClientInterface::class);
        $this->client->method('send')
            ->will($this->returnSelf());
        $this->client->method('write')
            ->will($this->returnSelf());
        $this->client->method('reset')
            ->will($this->returnSelf());

        $this->converter = $this->createMock(ConverterAbstract::class);

        $this->settings = $this->createMock(SettingsAbstract::class);
        $this->settings->method('getActorId')
            ->willReturn(TEST_ACTOR_ID);
        $this->settings->method('getActorToken')
            ->willReturn(TEST_ACTOR_TOKEN);
        $this->settings->method('getCashbox')
            ->willReturn(TEST_CASHBOX_WITHOUT_AGENT);
        $this->settings->method('extrudeConn')
            ->willReturn(new SettingsConn(TEST_ACTOR_ID, TEST_ACTOR_TOKEN, TEST_CASHBOX_WITHOUT_AGENT));
    }

    //######################################################################

    /**
     * @covers Digitalkassa\MDK\Net\Transfer::__construct
     * @covers Digitalkassa\MDK\Net\Transfer::getCashbox
     */
    public function testGetCashbox()
    {
        $cashbox = '{"type": "online_store", "taxation": 1, "billing_place_list": ["https://example.com/"]}';
        $this->client
            ->method('read')
            ->will($this->returnValueMap([
                [NetClientInterface::BODY, $cashbox],
                [NetClientInterface::CODE, 200]
            ]));

        $transfer = new Transfer($this->client, $this->converter);
        $response = $transfer->getCashbox($this->settings->extrudeConn());
        $this->assertIsObject($response);
        $this->assertEquals(json_decode($cashbox), $response);
    }

    /**
     * @covers Digitalkassa\MDK\Net\Transfer::__construct
     * @covers Digitalkassa\MDK\Net\Transfer::getCashbox
     */
    public function testGetCashboxFailConnect()
    {
        $this->client
            ->method('send')
            ->will($this->throwException(new NetConnectException()));

        $transfer = new Transfer($this->client, $this->converter);

        $this->expectException(NetConnectException::class);
        $transfer->getCashbox($this->settings->extrudeConn());
    }

    /**
     * @covers Digitalkassa\MDK\Net\Transfer::__construct
     * @covers Digitalkassa\MDK\Net\Transfer::getCashbox
     */
    public function testGetCashboxFailApi()
    {
        $this->client
            ->method('read')
            ->will($this->returnValueMap([
                [NetClientInterface::BODY, '[]'],
                [NetClientInterface::CODE, 401]
            ]));

        $transfer = new Transfer($this->client, $this->converter);

        $this->expectException(TransferException::class);
        $this->expectExceptionCode(401);
        $transfer->getCashbox($this->settings->extrudeConn());
    }

    //######################################################################

    /**
     * @covers Digitalkassa\MDK\Net\Transfer::__construct
     * @covers Digitalkassa\MDK\Net\Transfer::sendReceipt
     */
    public function testSendReceipt()
    {
        $this->client
            ->method('read')
            ->will($this->returnValueMap([
                [NetClientInterface::BODY, ''],
                [NetClientInterface::CODE, 202]
            ]));

        $this->converter
            ->method('receiptToArray')
            ->willReturn([]);

        $transfer = new Transfer($this->client, $this->converter);
        $receipt = new Receipt();

        $receiptStatus =  $transfer->sendReceipt($this->settings->extrudeConn(), $receipt);
        $this->assertInstanceOf(ReceiptStatus::class, $receiptStatus);
        $this->assertEquals(ReceiptStatus::ACCEPTED, $receiptStatus->getCode());
    }

    /**
     * @covers Digitalkassa\MDK\Net\Transfer::__construct
     * @covers Digitalkassa\MDK\Net\Transfer::sendReceipt
     */
    public function testSendReceiptFailConnect()
    {
        $this->client
            ->method('send')
            ->will($this->throwException(new NetConnectException('connect error', 18)));

        $this->converter
            ->method('receiptToArray')
            ->willReturn([]);

        $transfer = new Transfer($this->client, $this->converter);
        $receipt = new Receipt();

        $this->expectException(NetConnectException::class);
        $transfer->sendReceipt($this->settings->extrudeConn(), $receipt);
    }

    /**
     * @covers Digitalkassa\MDK\Net\Transfer::__construct
     * @covers Digitalkassa\MDK\Net\Transfer::sendReceipt
     */
    public function testSendReceiptFailApi()
    {
        $this->client
            ->method('read')
            ->will($this->returnValueMap([
                [NetClientInterface::BODY, ''],
                [NetClientInterface::CODE, 401]
            ]));

        $this->converter
            ->method('receiptToArray')
            ->willReturn([]);

        $transfer = new Transfer($this->client, $this->converter);
        $receipt = new Receipt();

        try {
            $transfer->sendReceipt($this->settings->extrudeConn(), $receipt);
        } catch (TransferException $e) {
            $this->assertEquals(401, $e->getCode());
        }
    }

    /**
     * @covers Digitalkassa\MDK\Net\Transfer::__construct
     * @covers Digitalkassa\MDK\Net\Transfer::sendReceipt
     */
    public function testSendReceiptFailConverter()
    {
        $this->converter
            ->method('receiptToArray')
            ->will($this->throwException(new ConverterException()));

        $transfer = new Transfer($this->client, $this->converter);
        $receipt = new Receipt();

        try {
            $transfer->sendReceipt($this->settings->extrudeConn(), $receipt);
        } catch (TransferException $e) {
            $this->assertEquals(ReceiptStatus::ERROR, $e->getCode());
        }
    }
}
