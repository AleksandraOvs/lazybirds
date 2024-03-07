<?php

use PHPUnit\Framework\TestCase;
use Digitalkassa\MDK\Entities\Receipt;
use Digitalkassa\MDK\Entities\ReceiptItem;
use Digitalkassa\MDK\Net\TransferInterface;
use Digitalkassa\MDK\Settings\SettingsConn;
use Digitalkassa\MDK\Services\AutomaticBase;
use Digitalkassa\MDK\Entities\Atoms\Taxation;
use Digitalkassa\MDK\Settings\SettingsAbstract;
use Digitalkassa\MDK\Entities\Atoms\ReceiptType;
use Digitalkassa\MDK\Entities\ConverterAbstract;
use Digitalkassa\MDK\Entities\Primitives\Amount;
use Digitalkassa\MDK\Entities\Primitives\Notify;
use Digitalkassa\MDK\Entities\Atoms\PaymentMethod;
use Digitalkassa\MDK\Entities\Atoms\ReceiptStatus;
use Digitalkassa\MDK\Entities\Primitives\Customer;
use Digitalkassa\MDK\Exceptions\TransferException;
use Digitalkassa\MDK\Collections\ReceiptCollection;
use Digitalkassa\MDK\Entities\Atoms\ReceiptSubType;
use Digitalkassa\MDK\Exceptions\NetConnectException;
use Digitalkassa\MDK\Storage\ReceiptStorageInterface;
use Digitalkassa\MDK\Entities\ReceiptAdapterInterface;
use Digitalkassa\MDK\Collections\ReceiptItemCollection;
use Digitalkassa\MDK\Exceptions\Services\AutomaticException;
use Digitalkassa\MDK\Entities\ReceiptId\ReceiptIdFactoryMeta;
use Digitalkassa\MDK\Exceptions\Base\InvalidArgumentException;
use Digitalkassa\MDK\Exceptions\Services\AutomaticErrorException;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @uses Digitalkassa\MDK\Services\AutomaticBase
 * @uses Digitalkassa\MDK\Entities\ReceiptItem
 * @uses Digitalkassa\MDK\Entities\Receipt
 * @uses Digitalkassa\MDK\Entities\Primitives\Notify
 * @uses Digitalkassa\MDK\Entities\Primitives\Amount
 * @uses Digitalkassa\MDK\Collections\BaseCollection
 * @uses Digitalkassa\MDK\Collections\ReceiptItemCollection
 * @uses Digitalkassa\MDK\Collections\ReceiptCollection
 * @uses Digitalkassa\MDK\Entities\AtomAbstract
 * @uses Digitalkassa\MDK\Entities\Atoms\PaymentMethod
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptItemType
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptStatus
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptSubType
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptType
 * @uses Digitalkassa\MDK\Entities\Atoms\Taxation
 * @uses Digitalkassa\MDK\Entities\Atoms\Vat
 * @uses Digitalkassa\MDK\Entities\Atoms\Unit
 * @uses Digitalkassa\MDK\Storage\ReceiptFilter
 * @uses Digitalkassa\MDK\Entities\Primitives\Customer
 * @uses Digitalkassa\MDK\Exceptions\TransferException
 * @uses Digitalkassa\MDK\Entities\ReceiptId\ReceiptIdFactoryMeta
 * @uses Digitalkassa\MDK\Settings\SettingsConn
 */
class AutomaticBaseFakeTest extends TestCase
{
    private $storage;
    private $adapter;
    private $transfer;
    private $settings;

    protected function setUp(): void
    {
        $items = new ReceiptItemCollection();
        $items[] = (new ReceiptItem())
            ->setPrice(100.0)
            ->setQuantity(2)
            ->setName('name');

        $this->adapter = $this->createMock(ReceiptAdapterInterface::class);
        $this->adapter->method('getItems')
            ->willReturn($items);
        $this->adapter->method('getTotal')
            ->willReturn(200.0);
        $this->adapter->method('getCustomer')
            ->willReturn(new Customer('Test'));
        $this->adapter->method('getNotify')
            ->willReturn(new Notify('+79998887766'));

        $this->transfer = $this->createMock(TransferInterface::class);
        $this->transfer
            ->method('sendReceipt')
            ->will($this->returnValue(new ReceiptStatus(ReceiptStatus::PREPARED)));

        $this->storage = $this->createMock(ReceiptStorageInterface::class);

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
        $this->settings->method('extrudeConn')
            ->willReturn(new SettingsConn(TEST_ACTOR_ID, TEST_ACTOR_TOKEN, TEST_CASHBOX_WITHOUT_AGENT));
    }

    //######################################################################

    /**
     * Тест чека предоплаты
     * @covers Digitalkassa\MDK\Services\AutomaticBase::__construct
     * @covers Digitalkassa\MDK\Services\AutomaticBase::fiscalize
     */
    public function testFiscalizeSuccessPre()
    {
        $this->settings->method('getScheme')
            ->willReturn(SettingsAbstract::SCHEME_PRE_FULL);

        $this->storage
            ->method('getCollection')
            ->willReturn(new ReceiptCollection());

        $automatic = new AutomaticBase(
            $this->settings,
            $this->storage,
            $this->transfer,
            $this->adapter,
            new ReceiptIdFactoryMeta()
        );

        $receipt = $automatic->fiscalize('0', 's1');
        $this->assertInstanceOf(Receipt::class, $receipt);
        $this->assertSame(ReceiptType::COMING, $receipt->getType());
        $this->assertSame(ReceiptSubType::PRE, $receipt->getSubType());
        $this->assertSame('s1', $receipt->getSiteId());
        $this->assertSame(200.0, $receipt->getAmount()->getCashless());
        $this->assertSame('Test', $receipt->getCustomer()->getName());
        $this->assertSame('+79998887766', $receipt->getNotify()->getPhone());

        $receipt = $automatic->fiscalize('0', '', ReceiptSubType::PRE);
        $this->assertInstanceOf(Receipt::class, $receipt);
        $this->assertSame(ReceiptType::COMING, $receipt->getType());
        $this->assertSame(ReceiptSubType::PRE, $receipt->getSubType());
        $this->assertSame('', $receipt->getSiteId());
        $this->assertSame(200.0, $receipt->getAmount()->getCashless());
        $this->assertSame('Test', $receipt->getCustomer()->getName());
        $this->assertSame('+79998887766', $receipt->getNotify()->getPhone());
    }

    //**********************************************************************

    /**
     * Тест чека полного расчета, без чека предоплаты
     * @covers Digitalkassa\MDK\Services\AutomaticBase::__construct
     * @covers Digitalkassa\MDK\Services\AutomaticBase::fiscalize
     */
    public function testFiscalizeSuccessFull1()
    {
        $this->settings->method('getScheme')
            ->willReturn(SettingsAbstract::SCHEME_PRE_FULL);

        $this->storage
            ->method('getCollection')
            ->willReturn(new ReceiptCollection());

        $automatic = new AutomaticBase(
            $this->settings,
            $this->storage,
            $this->transfer,
            $this->adapter,
            new ReceiptIdFactoryMeta()
        );

        $receipt = $automatic->fiscalize('0', '', ReceiptSubType::FULL);
        $this->assertInstanceOf(Receipt::class, $receipt);
        $this->assertSame(ReceiptSubType::FULL, $receipt->getSubType());
        $this->assertSame(200.0, $receipt->getAmount()->getCashless());
    }

    /**
     * Тест чека полного расчета с еще не пробитым чеком предоплаты
     * @covers Digitalkassa\MDK\Services\AutomaticBase::__construct
     * @covers Digitalkassa\MDK\Services\AutomaticBase::fiscalize
     */
    /*public function testFiscalizeFailFull2()
    {
        $this->settings->method('getScheme')
            ->willReturn(SettingsAbstract::SCHEME_PRE_FULL);

        $receipts = new ReceiptCollection();
        $receipt = new Receipt();
        $receipt->setType(ReceiptType::COMING);
        $receipt->setSubType(ReceiptSubType::PRE);
        $receipts[] = $receipt;

        $this->storage = $this->createMock(ReceiptStorageInterface::class);
        $this->storage
            ->method('getCollection')
            ->will($this->onConsecutiveCalls($receipts));

        $automatic = new AutomaticBase(
            $this->settings,
            $this->storage,
            $this->transfer,
            $this->adapter,
            new ReceiptIdFactoryMeta()
        );

        $this->expectException(AutomaticException::class);
        $receipt = $automatic->fiscalize('0');
    }*/

    /**
     * Тест чека полного расчета с чеком предоплаты
     * @covers Digitalkassa\MDK\Services\AutomaticBase::__construct
     * @covers Digitalkassa\MDK\Services\AutomaticBase::fiscalize
     */
    public function testFiscalizeSuccessFull2()
    {
        $this->settings->method('getScheme')
            ->willReturn(SettingsAbstract::SCHEME_PRE_FULL);

        $receipts = new ReceiptCollection();
        $receipt = new Receipt();
        $receipt->setType(ReceiptType::COMING);
        $receipt->setSubType(ReceiptSubType::PRE);
        $receipt->setStatus(new ReceiptStatus(ReceiptStatus::COMPLETED));
        $receipt->addItem(
            (new ReceiptItem())
                ->setName('test')
                ->setPrice(100)
                ->setQuantity(2)
                ->setPaymentMethod(PaymentMethod::PREPAYMENT_FULL)
        );
        $receipts[] = $receipt;

        $this->storage = $this->createMock(ReceiptStorageInterface::class);
        $this->storage
            ->method('getCollection')
            ->will($this->onConsecutiveCalls($receipts));

        $automatic = new AutomaticBase(
            $this->settings,
            $this->storage,
            $this->transfer,
            $this->adapter,
            new ReceiptIdFactoryMeta()
        );
        $receipt = $automatic->fiscalize('0');
        $this->assertInstanceOf(Receipt::class, $receipt);
        $this->assertSame(ReceiptSubType::FULL, $receipt->getSubType());
        $this->assertSame(200.0, $receipt->getAmount()->getPrepayment());
    }

    /**
     * Тест чека полного расчета с чеком предоплаты, при этом их суммы не будут равны
     * @covers Digitalkassa\MDK\Services\AutomaticBase::__construct
     * @covers Digitalkassa\MDK\Services\AutomaticBase::fiscalize
     */
    public function testFiscalizeFailFull2()
    {
        $this->settings->method('getScheme')
            ->willReturn(SettingsAbstract::SCHEME_PRE_FULL);

        $receipts = new ReceiptCollection();
        $receipt = new Receipt();
        $receipt->setType(ReceiptType::COMING);
        $receipt->setSubType(ReceiptSubType::PRE);
        $receipt->setStatus(new ReceiptStatus(ReceiptStatus::COMPLETED));
        $receipt->addItem(
            (new ReceiptItem())
                ->setName('test')
                ->setPrice(100)
                ->setQuantity(1)
                ->setPaymentMethod(PaymentMethod::PREPAYMENT_FULL)
        );
        $receipts[] = $receipt;

        $this->storage = $this->createMock(ReceiptStorageInterface::class);
        $this->storage
            ->method('getCollection')
            ->will($this->onConsecutiveCalls($receipts));

        $automatic = new AutomaticBase(
            $this->settings,
            $this->storage,
            $this->transfer,
            $this->adapter,
            new ReceiptIdFactoryMeta()
        );

        $this->expectException(AutomaticErrorException::class);
        $receipt = $automatic->fiscalize('0');
    }

    //**********************************************************************

    /**
     * Тест чека полного рачета при наличии настройки "пробивать только второй чек"
     * @covers Digitalkassa\MDK\Services\AutomaticBase::__construct
     * @covers Digitalkassa\MDK\Services\AutomaticBase::fiscalize
     */
    public function testFiscalizeSuccessGetOnly2()
    {
        $this->settings->method('getScheme')
            ->willReturn(SettingsAbstract::SCHEME_ONLY_FULL);

        $this->storage
            ->method('getCollection')
            ->willReturn(new ReceiptCollection());

        $automatic = new AutomaticBase(
            $this->settings,
            $this->storage,
            $this->transfer,
            $this->adapter,
            new ReceiptIdFactoryMeta()
        );

        $receipt = $automatic->fiscalize('0');
        $this->assertInstanceOf(Receipt::class, $receipt);
        $this->assertSame(ReceiptSubType::FULL, $receipt->getSubType());
        $this->assertSame(200.0, $receipt->getAmount()->getCashless());
    }

    //**********************************************************************

    /**
     * @covers Digitalkassa\MDK\Services\AutomaticBase::__construct
     * @covers Digitalkassa\MDK\Services\AutomaticBase::fiscalize
     */
    public function testFiscalizeSuccessServerError()
    {
        $this->settings->method('getScheme')
            ->willReturn(SettingsAbstract::SCHEME_PRE_FULL);

        $this->storage
            ->method('getCollection')
            ->willReturn(new ReceiptCollection());

        $this->transfer
            ->method('sendReceipt')
            ->will($this->throwException(new TransferException('', 500)));

        $automatic = new AutomaticBase(
            $this->settings,
            $this->storage,
            $this->transfer,
            $this->adapter,
            new ReceiptIdFactoryMeta()
        );

        $receipt = $automatic->fiscalize('0');
        $this->assertInstanceOf(Receipt::class, $receipt);
        $this->assertSame($receipt->getStatus()->getCode(), ReceiptStatus::PREPARED);
    }

    /**
     * @covers Digitalkassa\MDK\Services\AutomaticBase::__construct
     * @covers Digitalkassa\MDK\Services\AutomaticBase::fiscalize
     */
    public function testFiscalizeNetConnectException()
    {
        $this->settings
            ->method('getScheme')
            ->willReturn(SettingsAbstract::SCHEME_PRE_FULL);

        $this->storage
            ->method('getCollection')
            ->willReturn(new ReceiptCollection());

        $this->transfer
            ->method('sendReceipt')
            ->will($this->throwException(new NetConnectException('', 28)));

        $automatic = new AutomaticBase(
            $this->settings,
            $this->storage,
            $this->transfer,
            $this->adapter,
            new ReceiptIdFactoryMeta()
        );

        $receipt = $automatic->fiscalize('0');
        $this->assertInstanceOf(Receipt::class, $receipt);
        $this->assertSame($receipt->getStatus()->getCode(), ReceiptStatus::PREPARED);
    }

    //######################################################################

    /**
     * @covers Digitalkassa\MDK\Services\AutomaticBase::__construct
     * @covers Digitalkassa\MDK\Services\AutomaticBase::fiscalize
     */
    public function testFiscalizeFailReceipt()
    {
        $this->settings->method('getScheme')
            ->willReturn(SettingsAbstract::SCHEME_ONLY_FULL);

        $this->storage
            ->method('getCollection')
            ->willReturn(new ReceiptCollection());

        $this->transfer
            ->method('sendReceipt')
            ->will($this->throwException(new TransferException('', 400)));

        $automatic = new AutomaticBase(
            $this->settings,
            $this->storage,
            $this->transfer,
            $this->adapter,
            new ReceiptIdFactoryMeta()
        );

        $this->expectException(TransferException::class);
        $automatic->fiscalize('0');
    }

    /**
     * @covers Digitalkassa\MDK\Services\AutomaticBase::__construct
     * @covers Digitalkassa\MDK\Services\AutomaticBase::fiscalize
     */
    public function testFiscalizeFailExistsType()
    {
        $this->settings->method('getScheme')
            ->willReturn(SettingsAbstract::SCHEME_PRE_FULL);

        $receipts = new ReceiptCollection();
        $receipt = new Receipt();
        $receipt->setType(ReceiptType::COMING);
        $receipt->setSubType(ReceiptSubType::PRE);
        $receipts[] = $receipt;

        $this->storage
            ->method('getCollection')
            ->will($this->onConsecutiveCalls($receipts));

        $automatic = new AutomaticBase(
            $this->settings,
            $this->storage,
            $this->transfer,
            $this->adapter,
            new ReceiptIdFactoryMeta()
        );

        $this->expectException(AutomaticException::class);
        $automatic->fiscalize('0', '', ReceiptSubType::PRE);
    }

    /**
     * @covers Digitalkassa\MDK\Services\AutomaticBase::__construct
     * @covers Digitalkassa\MDK\Services\AutomaticBase::fiscalize
     */
    public function testFiscalizeFailExistsComingFull()
    {
        $this->settings->method('getScheme')
            ->willReturn(SettingsAbstract::SCHEME_PRE_FULL);

        $receipts = new ReceiptCollection();
        $receipt = new Receipt();
        $receipt->setType(ReceiptType::COMING);
        $receipt->setSubType(ReceiptSubType::FULL);
        $receipts[] = $receipt;

        $this->storage
            ->method('getCollection')
            ->will($this->onConsecutiveCalls($receipts));

        $automatic = new AutomaticBase(
            $this->settings,
            $this->storage,
            $this->transfer,
            $this->adapter,
            new ReceiptIdFactoryMeta()
        );

        $this->expectException(AutomaticException::class);
        $automatic->fiscalize('0');
    }

    /**
     * @covers Digitalkassa\MDK\Services\AutomaticBase::__construct
     * @covers Digitalkassa\MDK\Services\AutomaticBase::fiscalize
     */
    public function testFiscalizeFailErrorAdapter()
    {
        $this->adapter->method('getTotal')
            ->will($this->throwException(new InvalidArgumentException()));

        $this->storage
            ->method('getCollection')
            ->willReturn(new ReceiptCollection());

        $automatic = new AutomaticBase(
            $this->settings,
            $this->storage,
            $this->transfer,
            $this->adapter,
            new ReceiptIdFactoryMeta()
        );

        $this->expectException(InvalidArgumentException::class);
        $automatic->fiscalize('0');
    }
}
