<?php

namespace Omnipay\YM\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractRequest as OminpayAbstractRequest;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\YM\Exceptions\AuthorizationException;

abstract class AbstractRequest extends OminpayAbstractRequest {

    /**
     * @var string
     */
    protected static $endpoint = 'https://money.yandex.ru';

    /**
     * @return mixed
     */
    abstract protected function getMethod();

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
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     *
     * @return Response
     */
    public function sendData($data)
    {
        return $this->createResponse($this->makeApiCall($this->getMethod(), $data));
    }

    /**
     * @param string $data
     *
     * @return Response
     */
    protected function createResponse($data)
    {
        return new Response($this, $data);
    }

    /**
     * @return string
     */
    public function getEndpoint($method)
    {
        return self::$endpoint.'/api/'.$method;
    }

    /**
     * @param $data
     * @param $method
     *
     * @return array
     *
     * @throws InvalidResponseException
     */
    protected function makeApiCall($method, array $data)
    {
        return $this->httpClient->post($this->getEndpoint($method), null, $data)->send()->getBody();
    }

}