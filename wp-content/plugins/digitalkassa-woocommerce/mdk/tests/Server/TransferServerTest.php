<?php

use Digitalkassa\MDK\Net\Transfer;
use PHPUnit\Framework\TestCase;
use Digitalkassa\MDK\Entities\Receipt;
use Digitalkassa\MDK\Net\ConverterApi;
use Digitalkassa\MDK\Net\NetClientCurl;
use Digitalkassa\MDK\Entities\ReceiptItem;
use Digitalkassa\MDK\Settings\SettingsConn;
use Digitalkassa\MDK\Entities\Atoms\Taxation;
use Digitalkassa\MDK\Entities\Atoms\ReceiptType;
use Digitalkassa\MDK\Entities\Primitives\Amount;
use Digitalkassa\MDK\Entities\Primitives\Notify;
use Digitalkassa\MDK\Entities\Atoms\ReceiptStatus;
use Digitalkassa\MDK\Entities\Primitives\Customer;
use Digitalkassa\MDK\Exceptions\TransferException;
use Digitalkassa\MDK\Entities\ReceiptId\ReceiptIdFactoryMeta;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @uses Digitalkassa\MDK\Net\Transfer
 * @uses Digitalkassa\MDK\Entities\Receipt
 * @uses Digitalkassa\MDK\Net\ConverterApi
 * @uses Digitalkassa\MDK\Entities\ConverterAbstract
 * @uses Digitalkassa\MDK\Collections\BaseCollection
 * @uses Digitalkassa\MDK\Entities\AtomAbstract
 * @uses Digitalkassa\MDK\Entities\Atoms\PaymentMethod
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptItemType
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptStatus
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptSubType
 * @uses Digitalkassa\MDK\Entities\Primitives\Amount
 * @uses Digitalkassa\MDK\Entities\Primitives\Notify
 * @uses Digitalkassa\MDK\Entities\Primitives\Customer
 * @uses Digitalkassa\MDK\Entities\ReceiptItem
 * @uses Digitalkassa\MDK\Net\NetClientCurl
 * @uses Digitalkassa\MDK\Entities\Atoms\ReceiptType
 * @uses Digitalkassa\MDK\Entities\Atoms\Taxation
 * @uses Digitalkassa\MDK\Entities\Atoms\Vat
 * @uses Digitalkassa\MDK\Exceptions\TransferException
 * @uses Digitalkassa\MDK\Settings\SettingsConn
 */
class TransferServerTest extends TestCase
{
    protected $settingsConn;

    protected function setUp(): void
    {
        $this->settingsConn = new SettingsConn(TEST_ACTOR_ID, TEST_ACTOR_TOKEN, TEST_CASHBOX_WITHOUT_AGENT);
    }

    /**
     * @covers Digitalkassa\MDK\Net\Transfer::sendReceipt
     */
    public function testSendReceipt()
    {
        $receiptIdFactory = new ReceiptIdFactoryMeta();
        $receipt = new Receipt();
        $receipt
            ->setType(ReceiptType::COMING)
            ->addItem(
                (new ReceiptItem())
                    ->setPrice(100.0)
                    ->setQuantity(2)
                    ->setName('name')
                    ->setItemId('123')
            )
            ->setTaxation(Taxation::USN)
            ->setAmount((new Amount())->setCashless(200))
            ->setNotify(new Notify('box@domain.zone'))
            ->setCustomer(new Customer('Test'))
            ->setLocation('https://example.com/')
            ->setOrderId('456');

        $receipt->setReceiptId($receiptIdFactory->build($receipt));

        $client = new NetClientCurl();
        $converter = new ConverterApi();
        $transfer = new Transfer(
            $client,
            $converter
        );

        $receiptStatus = $transfer->sendReceipt($this->settingsConn, $receipt);
        $this->assertTrue(
            $receiptStatus->getCode() == ReceiptStatus::COMPLETED
            || $receiptStatus->getCode() == ReceiptStatus::ACCEPTED
        );

        return $receipt;
    }

    //######################################################################

    /**
     * @covers Digitalkassa\MDK\Net\Transfer::sendReceipt
     */
    public function testSendReceiptFailReceipt()
    {
        $receiptIdFactory = new ReceiptIdFactoryMeta();
        $receipt = new Receipt();
        $receipt
            ->setType(ReceiptType::COMING)
            ->addItem(
                (new ReceiptItem())
                    ->setPrice(100.0)
                    ->setQuantity(2)
                    ->setName('name')
            )
            ->setTaxation(Taxation::USN)
            ->setAmount((new Amount())->setCashless(300))
            ->setNotify(new Notify('box@domain.zone'))
            ->setCustomer(new Customer('Test'))
            ->setLocation('https://example.com/');

        $receipt->setReceiptId($receiptIdFactory->build($receipt));

        $client = new NetClientCurl();
        $converter = new ConverterApi();
        $transfer = new Transfer(
            $client,
            $converter
        );

        $this->expectException(TransferException::class);
        $this->expectExceptionCode(400);
        $transfer->sendReceipt($this->settingsConn, $receipt);
    }

    //######################################################################

    /**
     * @covers Digitalkassa\MDK\Net\Transfer::getCashBox
     */
    public function testGetCashBox()
    {
        $client = new NetClientCurl();
        $converter = new ConverterApi();
        $transfer = new Transfer(
            $client,
            $converter
        );
        $cashbox = $transfer->getCashBox($this->settingsConn);

        $this->assertIsObject($cashbox);
        $this->assertObjectHasAttribute('type', $cashbox);
        $this->assertObjectHasAttribute('taxation', $cashbox);
        $this->assertObjectHasAttribute('billing_place_list', $cashbox);
    }

    /**
     * @covers Digitalkassa\MDK\Net\Transfer::getCashBox
     * @example curl -i -H 'Authorization: Basic dGVzdDojSyEtayhEN3hbUm9feTQwW3wtWA==' https://api.digitalkassa.ru/v2/c_groups/0
     */
    public function testGetCashBoxFail()
    {
        $settingsConn = new SettingsConn(TEST_ACTOR_ID, TEST_ACTOR_TOKEN, -1);

        $client = new NetClientCurl();
        $converter = new ConverterApi();
        $transfer = new Transfer($client, $converter);

        $this->expectException(TransferException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage(TransferException::CODE_404);
        $transfer->getCashBox($settingsConn);
    }
}
