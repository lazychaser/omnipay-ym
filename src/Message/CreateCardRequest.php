<?php

namespace Omnipay\YM\Message;

class CreateCardRequest extends ProcessExternalPaymentRequest {

    /**
     * @return array
     */
    public function getData()
    {
        return array_merge(parent::getData(), array('request_token' => 'true'));
    }

}