<?php

use Digitalkassa\MDK\Net\Transfer;
use PHPUnit\Framework\TestCase;
use Digitalkassa\MDK\Net\NetClientCurl;
use Digitalkassa\MDK\Entities\Atoms\Unit;
use Digitalkassa\MDK\Net\NetClientInterface;
use Digitalkassa\MDK\Entities\Atoms\Taxation;
use Digitalkassa\MDK\Entities\Atoms\ReceiptType;
use Digitalkassa\MDK\Entities\Atoms\PaymentMethod;
use Digitalkassa\MDK\Exceptions\NetConnectException;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @uses Digitalkassa\MDK\Net\NetClientCurl
 * @uses Digitalkassa\MDK\Exceptions\NetConnectException
 */
class NetClientCurlTest extends TestCase
{
    /**
     * @covers Digitalkassa\MDK\Net\NetClientCurl::__construct
     * @covers Digitalkassa\MDK\Net\NetClientCurl::__destruct
     * @covers Digitalkassa\MDK\Net\NetClientCurl::write
     * @covers Digitalkassa\MDK\Net\NetClientCurl::read
     * @covers Digitalkassa\MDK\Net\NetClientCurl::reset
     */
    public function testSendSuccessGet()
    {
        $client = new NetClientCurl();
        $this->assertSame($client, $client->write(NetClientInterface::PATH, 'https://api.digitalkassa.ru/v2/doc'));
        $this->assertSame($client, $client->send());

        $this->assertSame(200, $client->read(NetClientInterface::CODE));
        $this->assertIsString($client->read(NetClientInterface::BODY));

        $this->assertSame('', $client->reset()->read(NetClientInterface::BODY));

        $client = new NetClientCurl();
    }

    /**
     * @covers Digitalkassa\MDK\Net\NetClientCurl::__construct
     * @covers Digitalkassa\MDK\Net\NetClientCurl::send
     * @covers Digitalkassa\MDK\Net\NetClientCurl::write
     * @covers Digitalkassa\MDK\Net\NetClientCurl::read
     */
    public function testSendSuccessPost()
    {
        $a = [
            'type' => ReceiptType::COMING,
            'items' => [[
                'type' => 1,
                'name' => 'name',
                'price' => 100.0,
                'quantity' => 1.0,
                'amount' => 100.0,
                'payment_method' => PaymentMethod::PAYMENT_FULL,
                'vat' => 1,
                'unit' => Unit::DEFAULT
            ]],
            'taxation' => Taxation::ORN,
            'amount' => [
                'cashless' => 100.0
            ],
            'notify' => [
                [
                    'type' => 'email',
                    'value' => 'box@domain.zone'
                ]
            ],
            'customer' => [
                'name' => 'Тест Тест Тест',
                'tin' => '0000000000'
            ],
            'loc' => [
                'billing_place' => 'https://example.com/'
            ]
        ];

        $receiptId = sprintf(
            '%04x%04x',
            rand(0, 0xffff),
            rand(0, 0xffff)
        );

        $client = new NetClientCurl();
        $client
            ->write(
                NetClientInterface::PATH,
                Transfer::API_URL . "/c_groups/" . TEST_CASHBOX_WITHOUT_AGENT . "/receipts/" . $receiptId
            )
            ->write(NetClientInterface::TYPE, 'POST')
            ->write(NetClientInterface::BODY, json_encode($a))
            ->write(NetClientInterface::HEAD, [
                "Authorization: Basic " . base64_encode(TEST_ACTOR_ID . ":" . TEST_ACTOR_TOKEN),
                "Content-type: application/json; charset=utf-8"
            ])
            ->write(-1, '')
            ->send();

        $this->assertTrue(
            $client->read(NetClientInterface::CODE) > 200 && $client->read(NetClientInterface::CODE) < 600
        );
        $this->assertIsString($client->read(NetClientInterface::HEAD));
        $this->assertNull($client->read(-1));
    }

    /**
     * @covers Digitalkassa\MDK\Net\NetClientCurl::__construct
     * @covers Digitalkassa\MDK\Net\NetClientCurl::send
     */
    public function testSendFailServer1()
    {
        $client = new NetClientCurl();
        $client->write(NetClientInterface::PATH, 'https://api0.digitalkassa.ru/0');

        $this->expectException(NetConnectException::class);
        $client->send();
    }
}
