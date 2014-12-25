<?php

namespace Omnipay\YM\Message;

use Omnipay\Tests\TestCase;

class RequestExternalPaymentRequestTest extends TestCase {

    /**
     * @var RequestExternalPaymentRequest
     */
    protected $request;

    public function setUp()
    {
        $this->request = new RequestExternalPaymentRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testData()
    {
        $this->request->setInstanceId('i123');
        $this->request->setWalletId('w123');
        $this->request->setAmount(10.0);
        $this->request->setDescription('foo');

        $expected = array(
            'instance_id' => 'i123',
            'pattern_id' => 'p2p',
            'to' => 'w123',
            'amount' => '10.00',
            'message' => 'foo',
        );

        $this->assertEquals($expected, $this->request->getData());
    }
}