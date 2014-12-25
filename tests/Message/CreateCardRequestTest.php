<?php

namespace Omnipay\YM\Message;

use Omnipay\Tests\TestCase;

class CreateCardRequestTest extends TestCase {

    /**
     * @var CreateCardRequest
     */
    protected $request;

    public function setUp()
    {
        $this->request = new CreateCardRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testData()
    {
        $this->request->initialize(array(
            'transactionReference' => 'r123',
            'returnUrl' => 'success',
            'cancelUrl' => 'fail',
            'instanceId' => 'i123',
        ));

        $expected = array(
            'instance_id' => 'i123',
            'request_id' => 'r123',
            'ext_auth_success_uri' => 'success',
            'ext_auth_fail_uri' => 'fail',
            'request_token' => 'true',
        );

        $this->assertEquals($expected, $this->request->getData());
    }
}