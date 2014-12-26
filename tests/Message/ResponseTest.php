<?php

namespace Omnipay\YM\Message;

use Omnipay\Tests\TestCase;

class ResponseTest extends TestCase {

    public function createResponse($data)
    {
        return new Response($this->getMockRequest(), $data);
    }

    public function testSuccessful()
    {
        $response = $this->createResponse('{"status": "success"}');

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
    }

    public function testFailure()
    {
        $response = $this->createResponse('{"status": "refused", "error": "foo"}');

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertEquals('foo', $response->getCode());
        $this->assertEquals('foo', $response->getMessage());
    }

    public function testRedirect()
    {
        $response = $this->createResponse('{
            "status": "ext_auth_required",
            "acs_uri": "https://example.com",
            "acs_params": {
                "foo": "bar",
                "baz": "bax"
            }
        }');

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertEquals('GET', $response->getRedirectMethod());
        $this->assertEquals('https://example.com?foo=bar&baz=bax', $response->getRedirectUrl());
        $this->assertEquals(null, $response->getRedirectData());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response->getRedirectResponse());
    }

    public function testMoneySource()
    {
        $response = $this->createResponse('{
            "status": "success",
            "invoice_id": "3000130505460",
            "money_source": {
                "type": "payment-card",
                "payment_card_type": "VISA",
                "pan_fragment": "1234",
                "money_source_token": "12345"
            }
        }');

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('12345', $response->getCardReference());
        $this->assertEquals('1234', $response->getCardNumber());
        $this->assertEquals('VISA', $response->getCardType());
    }

    public function testInstanceId()
    {
        $response = $this->createResponse('{"status": "success", "instance_id": "1234"}');

        $this->assertEquals('1234', $response->getTransactionReference());
    }

    public function testRequestId()
    {
        $response = $this->createResponse('{"request_id": "1234"}');

        $this->assertEquals('1234', $response->getTransactionReference());
    }

    public function testInvoiceId()
    {
        $response = $this->createResponse('{"invoice_id": "1234"}');

        $this->assertEquals('1234', $response->getTransactionReference());
    }

    public function testEmptyData()
    {
        $response = $this->createResponse('{"status": "success"}');

        $this->assertNull($response->getRedirectUrl());
        $this->assertNull($response->getCardReference());
        $this->assertNull($response->getCardNumber());
        $this->assertNull($response->getCardType());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getCode());
    }
}