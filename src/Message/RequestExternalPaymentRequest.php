<?php

namespace Omnipay\YM\Message;

class RequestExternalPaymentRequest extends AbstractRequest {

    /**
     * @param $value
     *
     * @return $this
     */
    public function setWalletId($value)
    {
        return $this->setParameter('walletId', $value);
    }

    /**
     * @return mixed
     */
    public function getWalletId()
    {
        return $this->getParameter('walletId');
    }

    /**
     * @return mixed
     */
    protected function getMethod()
    {
        return 'request-external-payment';
    }

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validate('instanceId', 'walletId', 'amount');

        return array(
            'instance_id' => $this->getInstanceId(),
            'pattern_id' => 'p2p',
            'to' => $this->getWalletId(),
            'amount' => $this->getAmount(),
            'message' => $this->getDescription(),
        );
    }
}