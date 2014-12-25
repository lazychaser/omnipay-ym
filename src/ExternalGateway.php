<?php

namespace Omnipay\YM;

use Guzzle\Http\Message\Response;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;

class ExternalGateway extends AbstractGateway {

    /**
     * Get gateway display name
     *
     * This can be used by carts to get the display name for each gateway.
     */
    public function getName()
    {
        return 'Yandex.Money External';
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setInstanceId($value)
    {
        return $this->setParameter('instanceId', $value);
    }

    /**
     * @return mixed
     */
    public function getInstanceId()
    {
        return $this->getParameter('instanceId');
    }

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
     * Get instance id that is required for other operations.
     *
     * @param $clientId
     *
     * @return string
     *
     * @throws InvalidResponseException
     */
    public function obtainInstanceId($clientId)
    {
        /** @var AbstractResponse $response */
        $response = $this->createRequest('Omnipay\YM\Message\InstanceIdRequest', compact('clientId'))->send();

        if ( ! $response->isSuccessful())
        {
            throw new InvalidResponseException($response->getMessage());
        }

        return $response->getTransactionReference();
    }

    /**
     * @param array $parameters
     *
     * @return Message\RequestExternalPaymentRequest
     */
    public function requestPayment(array $parameters = array())
    {
        return $this->createRequest('Omnipay\YM\Message\RequestExternalPaymentRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return Message\ProcessExternalPaymentRequest
     */
    public function processPayment(array $parameters = array())
    {
        return $this->createRequest('Omnipay\YM\Message\ProcessExternalPaymentRequest', $parameters);
    }

    /**
     * @param array $parameters
     *
     * @return Message\CreateCardRequest
     */
    public function createCard(array $parameters = array())
    {
        return $this->createRequest('Omnipay\YM\Message\CreateCardRequest', $parameters);
    }

}