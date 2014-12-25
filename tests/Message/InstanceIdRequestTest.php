<?php

namespace Omnipay\YM\Message;

use Omnipay\Tests\TestCase;

class InstanceIdRequestTest extends TestCase {

    /**
     * @var InstanceIdRequest
     */
    protected $request;

    public function setUp()
    {
        $this->request = new InstanceIdRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testData()
    {
        $this->request->setClientId('123');

        $this->assertEquals(array('client_id' => '123'), $this->request->getData());
    }

}