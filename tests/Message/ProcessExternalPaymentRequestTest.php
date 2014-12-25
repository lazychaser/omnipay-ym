<?php

namespace Omnipay\YM\Message;

use Omnipay\Tests\TestCase;

class ProcessExternalPaymentRequestTest extends TestCase {

    /**
     * @var ProcessExternalPaymentRequest
     */
    protected $request;

    public function setUp()
    {
        $this->request = new ProcessExternalPaymentRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request->setInstanceId('i123');
        $this->request->setTransactionReference('r123');
        $this->request->setReturnUrl('success');
        $this->request->setCancelUrl('fail');
    }

    public function testData()
    {
        $expected = array(
            'instance_id' => 'i123',
            'request_id' => 'r123',
            'ext_auth_success_uri' => 'success',
            'ext_auth_fail_uri' => 'fail',
        );

        $this->assertEquals($expected, $this->request->getData());
    }

    public function testTokenData()
    {
        $this->request->setCardReference('c123');
        $this->request->setCvv('123');

        $expected = array(
            'instance_id' => 'i123',
            'request_id' => 'r123',
            'ext_auth_success_uri' => 'success',
            'ext_auth_fail_uri' => 'fail',
            'money_source_token' => 'c123',
            'csc' => '123',
        );

        $this->assertEquals($expected, $this->request->getData());
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     */
    public function testFailsWithoutCvv()
    {
        $this->request->setCardReference('c123');

        $this->request->getData();
    }
}