<?php

use Digitalkassa\MDK\Net\Transfer;
use PHPUnit\Framework\TestCase;
use Digitalkassa\MDK\Entities\Receipt;
use Digitalkassa\MDK\Net\NetClientInterface;
use Digitalkassa\MDK\Services\ConnectorBase;
use Digitalkassa\MDK\Entities\Atoms\Taxation;
use Digitalkassa\MDK\Entities\ConverterAbstract;
use Digitalkassa\MDK\Settings\SettingsAbstract;
use Digitalkassa\MDK\Entities\Atoms\ReceiptStatus;
use Digitalkassa\MDK\Exceptions\SettingsException;
use Digitalkassa\MDK\Exceptions\NetConnectException;
use Digitalkassa\MDK\Storage\ReceiptStorageInterface;
use Digitalkassa\MDK\Exceptions\Services\PrinterException;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @uses Digitalkassa\MDK\Services\ConnectorBase
 * @uses Digitalkassa\MDK\Net\Transfer
 * @uses Digitalkassa\MDK\Exceptions\TransferException
 * @uses Digitalkassa\MDK\Entities\AtomAbstract
 * @uses Digitalkassa\MDK\Entities\Atoms\Taxation
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptStatus
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptSubType
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptType
 * @uses Digitalkassa\MDK\Entities\Receipt
 * @uses Digitalkassa\MDK\Exceptions\BaseException
 */
class ConnectorBaseFakeTest extends TestCase
{
    private $client;
    private $converter;
    private $settings;

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
        $this->settings->method('getTaxation')
            ->willReturn(Taxation::ORN);
        $this->settings->method('getLocation')
            ->willReturn('https://example.com/');
    }

    /**
     * @covers Digitalkassa\MDK\Services\ConnectorBase::__construct
     * @covers Digitalkassa\MDK\Services\ConnectorBase::testSettings
     */
    public function testSettingsSuccess()
    {
        $cashbox = '{"type": "online_store", "taxation": 1, "billing_place_list": ["https://example.com/"]}';
        $this->client->method('read')
            ->will($this->returnValueMap([
                [NetClientInterface::BODY, $cashbox],
                [NetClientInterface::CODE, 200]
            ]));

        $transfer = new Transfer(
            $this->client,
            $this->converter
        );
        $connector = new ConnectorBase($transfer);
        $this->assertTrue($connector->testSettings($this->settings));
    }

    /**
     * @covers Digitalkassa\MDK\Services\ConnectorBase::__construct
     * @covers Digitalkassa\MDK\Services\ConnectorBase::testSettings
     */
    public function testSettingsFailServer500()
    {
        $this->client->method('read')
            ->will($this->returnValueMap([
                [NetClientInterface::CODE, 500],
                [NetClientInterface::BODY, ''],
            ]));

        $transfer = new Transfer(
            $this->client,
            $this->converter
        );
        $connector = new ConnectorBase($transfer);
        $this->expectException(SettingsException::class);
        $this->expectExceptionMessage(
            sprintf('Сервер временно недоступен (%d), попробуйте позже', 500)
        );
        $connector->testSettings($this->settings);
    }

    /**
     * @covers Digitalkassa\MDK\Services\ConnectorBase::__construct
     * @covers Digitalkassa\MDK\Services\ConnectorBase::testSettings
     */
    public function testSettingsFailServer28()
    {
        $this->client
            ->method('send')
            ->will($this->throwException(new NetConnectException('', 28)));

        $transfer = new Transfer(
            $this->client,
            $this->converter
        );
        $connector = new ConnectorBase($transfer);
        $this->expectException(SettingsException::class);
        $this->expectExceptionMessage(
            sprintf('Сервер временно недоступен (%d), попробуйте позже', 28)
        );
        $connector->testSettings($this->settings);
    }

    /**
     * @covers Digitalkassa\MDK\Services\ConnectorBase::__construct
     * @covers Digitalkassa\MDK\Services\ConnectorBase::testSettings
     */
    public function testSettingsFailServer401402403404()
    {
        $this->client->method('read')
            ->will($this->returnValueMap([
                [NetClientInterface::CODE, 401],
                [NetClientInterface::BODY, ''],
            ]));

        $transfer = new Transfer(
            $this->client,
            $this->converter
        );
        $connector = new ConnectorBase($transfer);
        $this->expectException(SettingsException::class);
        $this->expectExceptionMessage(
            sprintf('Неверные авторизационные данные (%d)', 401)
        );
        $connector->testSettings($this->settings);
    }

    /**
     * @covers Digitalkassa\MDK\Services\ConnectorBase::__construct
     * @covers Digitalkassa\MDK\Services\ConnectorBase::testSettings
     */
    public function testSettingsFailAuth()
    {
        $this->client->method('send')
            ->will($this->throwException(new NetConnectException()));

        $transfer = new Transfer(
            $this->client,
            $this->converter
        );
        $connector = new ConnectorBase($transfer);
        $this->expectException(SettingsException::class);
        $connector->testSettings($this->settings);
    }

    /**
     * @covers Digitalkassa\MDK\Services\ConnectorBase::__construct
     * @covers Digitalkassa\MDK\Services\ConnectorBase::testSettings
     */
    public function testSettingsFailTaxation()
    {
        $cashbox = '{"type": "online_store", "taxation": 32, "billing_place_list": ["https://example.com/"]}';
        $this->client->method('read')
            ->will($this->returnValueMap([
                [NetClientInterface::BODY, $cashbox],
                [NetClientInterface::CODE, 200]
            ]));

        $transfer = new Transfer(
            $this->client,
            $this->converter
        );
        $connector = new ConnectorBase($transfer);
        $this->expectException(SettingsException::class);
        $connector->testSettings($this->settings);
    }

    /**
     * @covers Digitalkassa\MDK\Services\ConnectorBase::__construct
     * @covers Digitalkassa\MDK\Services\ConnectorBase::testSettings
     */
    public function testSettingsFailLocation()
    {
        $cashbox = '{"type": "online_store", "taxation": 1, "billing_place_list": ["http://example.com"]}';
        $this->client->method('read')
            ->will($this->returnValueMap([
                [NetClientInterface::BODY, $cashbox],
                [NetClientInterface::CODE, 200]
            ]));

        $transfer = new Transfer(
            $this->client,
            $this->converter
        );
        $connector = new ConnectorBase($transfer);
        $this->expectException(SettingsException::class);
        $connector->testSettings($this->settings);
    }
}
