<?php

namespace Omnipay\YM;

use Omnipay\Tests\GatewayTestCase;

class ExternalGatewayTest extends GatewayTestCase {

    /**
     * @var ExternalGateway
     */
    protected $gateway;

    public function setUp()
    {
        $this->gateway = new ExternalGateway($this->getHttpClient(), $this->getHttpRequest());

        $this->gateway->setWalletId('w123');
        $this->gateway->setInstanceId('i123');

        $this->processOptions = array(
            'transactionReference' => 't123',
            'returnUrl' => 'success',
            'cancelUrl' => 'fail',
        );
    }

    public function testParameters()
    {
        $this->assertEquals('w123', $this->gateway->getWalletId());
        $this->assertEquals('i123', $this->gateway->getInstanceId());
    }

    public function testObtainInstanceId()
    {
        $this->setMockHttpResponse('InstanceIdRequestSuccess.txt');

        $response = $this->gateway->obtainInstanceId('123');

        $this->assertNotEmpty($response);
        $this->assertInternalType('string', $response);
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidResponseException
     */
    public function testObtainInstanceIdFails()
    {
        $this->setMockHttpResponse('InstanceIdRequestFailure.txt');

        $this->gateway->obtainInstanceId('123');
    }

    public function testRequestPayment()
    {
        $this->setMockHttpResponse('RequestExternalPaymentSuccess.txt');

        $response = $this->gateway->requestPayment(array('amount' => 10.0))->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertNotEmpty($response->getTransactionReference());
    }

    public function testProcessSuccess()
    {
        $this->setMockHttpResponse('ProcessPaymentSuccess.txt');

        $response = $this->gateway->processPayment($this->processOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertNotEmpty($response->getTransactionReference());
    }

    public function testProcessRedirect()
    {
        $this->setMockHttpResponse('ProcessPaymentRedirect.txt');

        $response = $this->gateway->processPayment($this->processOptions)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNotEmpty($response->getRedirectUrl());
    }

    public function testProcessInProgress()
    {
        $this->setMockHttpResponse(array('ProcessPaymentInProgress.txt', 'ProcessPaymentSuccess.txt'));

        $response = $this->gateway->processPayment($this->processOptions)->send();

        $this->assertTrue($response->isSuccessful());
    }

    public function testProcessMoneySource()
    {
        $this->setMockHttpResponse('ProcessPaymentMoneySource.txt');

        $response = $this->gateway->createCard($this->processOptions)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertNotEmpty($response->getCardReference());
    }
}