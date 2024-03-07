<?php

use Digitalkassa\MDK\Client;
use Digitalkassa\MDK\Net\Transfer;
use PHPUnit\Framework\TestCase;
use Digitalkassa\MDK\Entities\Receipt;
use Digitalkassa\MDK\Net\ConverterApi;
use Digitalkassa\MDK\Net\NetClientCurl;
use Digitalkassa\MDK\Entities\Atoms\Vat;
use Digitalkassa\MDK\Entities\ReceiptItem;
use Digitalkassa\MDK\Services\PipelineBase;
use Digitalkassa\MDK\Storage\ReceiptFilter;
use Digitalkassa\MDK\Services\AutomaticBase;
use Digitalkassa\MDK\Services\ConnectorBase;
use Digitalkassa\MDK\Entities\Atoms\Taxation;
use Digitalkassa\MDK\Storage\ConverterStorage;
use Digitalkassa\MDK\Settings\SettingsAbstract;
use Digitalkassa\MDK\Entities\Atoms\ReceiptType;
use Digitalkassa\MDK\Entities\Primitives\Amount;
use Digitalkassa\MDK\Entities\Primitives\Notify;
use Digitalkassa\MDK\Entities\Atoms\PaymentMethod;
use Digitalkassa\MDK\Entities\Atoms\ReceiptStatus;
use Digitalkassa\MDK\Entities\Primitives\Customer;
use Digitalkassa\MDK\Exceptions\SettingsException;
use Digitalkassa\MDK\Collections\ReceiptCollection;
use Digitalkassa\MDK\Entities\Atoms\ReceiptSubType;
use Digitalkassa\MDK\Entities\Atoms\ReceiptItemType;
use Digitalkassa\MDK\Collections\ReceiptItemCollection;
use Digitalkassa\MDK\Exceptions\Services\AutomaticException;
use Digitalkassa\MDK\Entities\ReceiptId\ReceiptIdFactoryMeta;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @uses Digitalkassa\MDK\Collections\BaseCollection
 * @uses Digitalkassa\MDK\Entities\AtomAbstract
 * @uses Digitalkassa\MDK\Entities\Atoms\PaymentMethod
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptItemType
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptStatus
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptSubType
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptType
 * @uses Digitalkassa\MDK\Entities\Atoms\Taxation
 * @uses Digitalkassa\MDK\Entities\Atoms\Vat
 * @uses Digitalkassa\MDK\Entities\ConverterAbstract
 * @uses Digitalkassa\MDK\Entities\Primitives\Amount
 * @uses Digitalkassa\MDK\Entities\Primitives\Customer
 * @uses Digitalkassa\MDK\Entities\Primitives\Notify
 * @uses Digitalkassa\MDK\Entities\Receipt
 * @uses Digitalkassa\MDK\Entities\ReceiptItem
 * @uses Digitalkassa\MDK\Storage\ConverterStorage
 * @uses Digitalkassa\MDK\Storage\ReceiptFilter
 * @uses Digitalkassa\MDK\Client
 * @uses Digitalkassa\MDK\Collections\ReceiptCollection
 * @uses Digitalkassa\MDK\Collections\ReceiptItemCollection
 * @uses Digitalkassa\MDK\Exceptions\TransferException
 * @uses Digitalkassa\MDK\Net\ConverterApi
 * @uses Digitalkassa\MDK\Net\NetClientCurl
 * @uses Digitalkassa\MDK\Net\Transfer
 */
class SystemTest extends TestCase
{
    protected static $db;

    /** @var SettingsConcrete */
    protected static $settings;

    /** @var ReceiptStorageConcrete */
    protected static $storage;

    /** @var ReceiptAdapterConcrete */
    protected static $adapter;

    /** @var Client */
    protected static $client;

    public static function setUpBeforeClass(): void
    {
        self::$db = new db('db', 'root', 'root');
        self::$db->query(file_get_contents(__DIR__ . '/db.sql'));

        self::$settings = new SettingsConcrete([
            'actor_id' => TEST_ACTOR_ID,
            'actor_token' => TEST_ACTOR_TOKEN,
            'cashbox' => TEST_CASHBOX_WITHOUT_AGENT,
            'site' => 'https://example.com/',
            'taxation' => Taxation::USN,
            'scheme' => SettingsAbstract::SCHEME_PRE_FULL,
            'type_default_items' => ReceiptItemType::PRODUCT,
            'vat_default_items' => Vat::CODE_WITHOUT
        ]);

        self::$storage = new ReceiptStorageConcrete(
            new ConverterStorage(new ReceiptIdFactoryMeta()),
            self::$db
        );
        self::$adapter = new ReceiptAdapterConcrete(self::$db, self::$settings);

        $transfer = new Transfer(
            new NetClientCurl(),
            new ConverterApi()
        );

        $automatic = new AutomaticBase(
            self::$settings,
            self::$storage,
            $transfer,
            self::$adapter,
            new ReceiptIdFactoryMeta()
        );
        $pipeline = new PipelineBase(self::$settings, self::$storage, $transfer, new ReceiptIdFactoryMeta());
        $connector = new ConnectorBase($transfer);

        self::$client = new Client(
            self::$settings,
            self::$storage,
            $automatic,
            $pipeline,
            $connector
        );
    }

    //######################################################################

    /**
     * @covers ReceiptStorageConcrete
     */
    public function testStorage()
    {
        $receipt = new Receipt();
        $receipt
            ->setType(ReceiptType::COMING)
            ->setOrderId('456')
            ->addItem(
                (new ReceiptItem())
                    ->setPrice(100.0)
                    ->setQuantity(2)
                    ->setName('name')
            )
            ->setTaxation(Taxation::USN)
            ->setAmount((new Amount())->setCashless(200))
            ->setNotify(new Notify('box@domain.zone'))
            ->setCustomer(new Customer('Test'))
            ->setLocation('https://example.com/');
        $receipt->setReceiptId((new ReceiptIdFactoryMeta())->build($receipt));

        $index = self::$storage->save($receipt);
        $this->assertSame($index, $receipt->getId());

        $receipt->setType(ReceiptType::REFUND_COMING);
        $index = self::$storage->save($receipt);
        $this->assertSame($index, $receipt->getId());

        $receipt->setId(0);
        $receipt->setType(ReceiptType::COMING);
        $index2 = self::$storage->save($receipt);
        $this->assertSame($index + 1, $index2);


        $receiptFromDB = self::$storage->getOne($index);
        $this->assertSame($index, $receiptFromDB->getId());
        $this->assertSame(ReceiptType::REFUND_COMING, $receiptFromDB->getType());
        $this->assertSame(Taxation::USN, $receiptFromDB->getTaxation());
        $this->assertSame(200.0, $receiptFromDB->getAmount()->getCashless());
        $this->assertSame('box@domain.zone', $receiptFromDB->getNotify()->getEmail());
        $this->assertSame('Test', $receiptFromDB->getCustomer()->getName());
        $this->assertSame('https://example.com/', $receiptFromDB->getLocation());
        $this->assertSame(1, $receiptFromDB->getItems()->count());


        $receiptsComing = self::$storage->getCollection(
            (new ReceiptFilter())
                ->setType(ReceiptType::COMING)
        );

        $this->assertInstanceOf(ReceiptCollection::class, $receiptsComing);
    }

    //######################################################################

    /**
     * @covers ReceiptAdapterConcrete
     */
    public function testAdapter()
    {
        $items = self::$adapter->getItems(1, null, ReceiptSubType::PRE);
        $this->assertInstanceOf(ReceiptItemCollection::class, $items);
        $this->assertSame(PaymentMethod::PREPAYMENT_FULL, $items[0]->getPaymentMethod());

        $items = self::$adapter->getItems(1, null, ReceiptSubType::FULL);
        $this->assertSame(PaymentMethod::PAYMENT_FULL, $items[0]->getPaymentMethod());

        $total = self::$adapter->getTotal(1, null);
        $this->assertIsFloat($total);
        $this->assertTrue($total > 0);

        $customer = self::$adapter->getCustomer(1, null);
        $this->assertInstanceOf(Customer::class, $customer);

        $notify = self::$adapter->getNotify(1, null);
        $this->assertInstanceOf(Notify::class, $notify);
    }

    //######################################################################

    /**
     * @covers Digitalkassa\MDK\Services\AutomaticBase
     * @depends testConnectorSuccess
     * @depends testStorage
     * @depends testAdapter
     */
    public function testAutomatic()
    {
        $automatic = self::$client->serviceAutomatic();
        $receipt1 = $automatic->fiscalize(1, '', ReceiptSubType::PRE);
        $this->assertTrue($receipt1->getStatus()->getCode() != ReceiptStatus::ERROR);
        $receipt2 = $automatic->fiscalize(1, '', ReceiptSubType::FULL);
        $this->assertTrue($receipt2->getStatus()->getCode() != ReceiptStatus::ERROR);

        $this->expectException(AutomaticException::class);
        $automatic->fiscalize(1, '', ReceiptSubType::FULL);
    }

    //######################################################################

    /**
     * @covers Digitalkassa\MDK\Services\PipelineBase
     * @depends testConnectorSuccess
     * @depends testStorage
     * @depends testAdapter
     * @depends testAutomatic
     */
    public function testPipelineSuccess()
    {
        $receipts = [];
        $automatic = self::$client->serviceAutomatic();

        /*
            создадим чек для заказа 5, пробьем и специально установим статус ACCEPTED,
            в тестах будем ждать COMPLETED | ACCEPTED
        */
        $orderId = 5;
        $receiptComing = $automatic->fiscalize($orderId);
        $receiptComing->setStatus(new ReceiptStatus(ReceiptStatus::ACCEPTED));
        self::$storage->save($receiptComing);
        $receipts[$receiptComing->getId()] = [ReceiptStatus::COMPLETED, ReceiptStatus::ACCEPTED];

        /*
            создадим чек для заказа 6, пробьем и специально установим статус PREPARED,
            в тестах будем ждать EXPIRED
        */
        $orderId = 6;
        $receiptComing = $automatic->fiscalize($orderId);
        $receiptComing->setStatus(new ReceiptStatus(ReceiptStatus::PREPARED));
        $receiptComing->setStartTime(date('Y-m-d H:i:s', time() - (Receipt::ALLOWED_ATTEMPT_TIME + 1)));
        self::$storage->save($receiptComing);
        $receipts[$receiptComing->getId()] = [ReceiptStatus::EXPIRED];

        /*
            создадим чек для заказа 3, присвоим ему статус PREPARED,
            в тестах будем ждать COMPLETED | ACCEPTED
        */
        $orderId = 3;
        $receiptComing = $automatic->fiscalize($orderId);
        $receiptComing->setStatus(new ReceiptStatus(ReceiptStatus::PREPARED));
        self::$storage->save($receiptComing);
        $receipts[$receiptComing->getId()] = [ReceiptStatus::COMPLETED, ReceiptStatus::ACCEPTED];


        /*
            создадим еще один чек для несуществующего заказа 10,
            не будем фискализировать и установим статус PREPARED
            в тестах будем ждать COMPLETED | ACCEPTED
        */
        $receipt = new Receipt();
        $receipt
            ->setId(0)
            ->setStatus(new ReceiptStatus(ReceiptStatus::PREPARED))
            ->setOrderId(10)
            ->setType(ReceiptType::COMING)
            ->addItem(
                (new ReceiptItem())
                    ->setPrice(100.0)
                    ->setQuantity(2)
                    ->setName('name')
            )
            ->setTaxation(Taxation::USN)
            ->setAmount((new Amount())->setCashless(200))
            ->setNotify(new Notify('box@domain.zone'))
            ->setCustomer(new Customer('Test'))
            ->setLocation('https://example.com/');
        $receipt->setReceiptId((new ReceiptIdFactoryMeta())->build($receipt));
        self::$storage->save($receipt);
        $receipts[$receipt->getId()] = [ReceiptStatus::COMPLETED, ReceiptStatus::ACCEPTED];


        $pipeline = self::$client->servicePipeline();
        $pipeline->update(__DIR__ . '/../../.pipeline');
        foreach ($receipts as $key => $value) {
            $receipt = self::$storage->getOne($key);
            $statusCode = $receipt->getStatus()->getCode();
            $this->assertContains($statusCode, $value);
        }
    }

    /**
     * @covers Digitalkassa\MDK\Services\Monitoring
     * @depends testPipelineSuccess
     */
    public function testMonitoringSuccess()
    {
        $file = __DIR__ . '/../../.monitoring';
        $pipeline = self::$client->servicePipeline();
        $this->assertTrue($pipeline->monitoring($file, 'start_time'));
    }

    //######################################################################

    /**
     * @covers Digitalkassa\MDK\Services\ConnectorBase
     */
    public function testConnectorSuccess()
    {
        $connector = self::$client->serviceConnector();

        $this->assertTrue($connector->testSettings(self::$settings));

        $transfer = new Transfer(
            new NetClientCurl(),
            new ConverterApi()
        );
        $connector = new ConnectorBase($transfer);

        $this->assertTrue($connector->testSettings(self::$settings));
    }

    /**
     * @covers Digitalkassa\MDK\Services\ConnectorBase
     */
    public function testConnectorFailAuth()
    {
        $settings = new SettingsConcrete([
            'actor_id' => '0',
            'actor_token' => TEST_ACTOR_TOKEN,
            'cashbox' => TEST_CASHBOX_WITHOUT_AGENT,
            'site' => 'https://example.com/',
            'taxation' => Taxation::USN,
        ]);
        $transfer = new Transfer(
            new NetClientCurl(),
            new ConverterApi()
        );
        $connector = new ConnectorBase($transfer);

        $this->expectException(SettingsException::class);
        $connector->testSettings($settings);
    }

    /**
     * @covers Digitalkassa\MDK\Services\ConnectorBase
     */
    public function testConnectorFailCashbox()
    {
        $settings = new SettingsConcrete([
            'actor_id' => TEST_ACTOR_ID,
            'actor_token' => TEST_ACTOR_TOKEN,
            'cashbox' => '0',
            'site' => 'https://example.com/',
            'taxation' => Taxation::USN,
        ]);
        $transfer = new Transfer(
            new NetClientCurl(),
            new ConverterApi()
        );
        $connector = new ConnectorBase($transfer);

        $this->expectException(SettingsException::class);
        $connector->testSettings($settings);
    }

    /**
     * @covers Digitalkassa\MDK\Services\ConnectorBase
     */
    public function testConnectorFailTaxation()
    {
        $connector = self::$client->serviceConnector();

        $this->expectException(SettingsException::class);
        $connector->testSettings(
            new SettingsConcrete([
                'actor_id' => TEST_ACTOR_ID,
                'actor_token' => TEST_ACTOR_TOKEN,
                'cashbox' => '0',
                'site' => 'https://example.com/',
                'taxation' => Taxation::ESN,
            ])
        );
    }
}
