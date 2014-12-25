<?php

namespace Omnipay\YM\Message;

class InstanceIdRequest extends AbstractRequest {

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setClientId($value)
    {
        return $this->setParameter('clientId', $value);
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->getParameter('clientId');
    }

    /**
     * @return mixed
     */
    protected function getMethod()
    {
        return 'instance-id';
    }

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validate('clientId');

        return array(
            'client_id' => $this->getClientId(),
        );
    }
}