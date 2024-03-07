<?php

use Digitalkassa\MDK\Client;
use Digitalkassa\MDK\Net\Transfer;
use PHPUnit\Framework\TestCase;
use Digitalkassa\MDK\Net\ConverterApi;
use Digitalkassa\MDK\Net\NetClientCurl;
use Digitalkassa\MDK\Services\PipelineBase;
use Digitalkassa\MDK\Services\AutomaticBase;
use Digitalkassa\MDK\Services\ConnectorBase;
use Digitalkassa\MDK\Settings\SettingsAbstract;
use Digitalkassa\MDK\Storage\ReceiptStorageInterface;
use Digitalkassa\MDK\Entities\ReceiptAdapterInterface;
use Digitalkassa\MDK\Entities\ReceiptId\ReceiptIdFactoryInterface;
use Digitalkassa\MDK\Entities\ReceiptId\ReceiptIdFactoryMeta;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @uses Digitalkassa\MDK\Client
 * @uses Digitalkassa\MDK\Net\NetClientCurl
 * @uses Digitalkassa\MDK\Net\Transfer
 * @uses Digitalkassa\MDK\Services\AutomaticBase
 * @uses Digitalkassa\MDK\Services\ConnectorBase
 * @uses Digitalkassa\MDK\Services\PipelineBase
 */
class ClientTest extends TestCase
{
    /**
     * @covers Digitalkassa\MDK\Client::__construct
     * @covers Digitalkassa\MDK\Client::serviceAutomatic
     * @covers Digitalkassa\MDK\Client::servicePipeline
     * @covers Digitalkassa\MDK\Client::serviceConnector
     *
     * @covers Digitalkassa\MDK\Client::componentSettings
     * @covers Digitalkassa\MDK\Client::componentStorage
     */
    public function test()
    {
        $settings = $this->createMock(SettingsAbstract::class);
        $storage = $this->createMock(ReceiptStorageInterface::class);
        $adapter = $this->createMock(ReceiptAdapterInterface::class);
        $receiptIdFactory = $this->createMock(ReceiptIdFactoryInterface::class);

        $transfer = new Transfer(new NetClientCurl(), new ConverterApi());

        $automatic = new AutomaticBase($settings, $storage, $transfer, $adapter, new ReceiptIdFactoryMeta());
        $pipeline = new PipelineBase($settings, $storage, $transfer, $receiptIdFactory);
        $connector = new ConnectorBase($transfer);

        $client = new Client(
            $settings,
            $storage,
            $automatic,
            $pipeline,
            $connector
        );

        $this->assertSame($automatic, $client->serviceAutomatic());
        $this->assertSame($pipeline, $client->servicePipeline());
        $this->assertSame($connector, $client->serviceConnector());

        $this->assertSame($settings, $client->componentSettings());
        $this->assertSame($storage, $client->componentStorage());
    }
}
