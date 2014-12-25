<?php

namespace Omnipay\YM\Message;

class ProcessExternalPaymentRequest extends AbstractRequest {

    /**
     * @param $value
     *
     * @return $this
     */
    public function setCvv($value)
    {
        return $this->setParameter('cvv', $value);
    }

    /**
     * @return mixed
     */
    public function getCvv()
    {
        return $this->getParameter('cvv');
    }

    /**
     * @return mixed
     */
    protected function getMethod()
    {
        return 'process-external-payment';
    }

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validate('instanceId', 'transactionReference', 'returnUrl', 'cancelUrl');

        $params = array(
            'instance_id' => $this->getInstanceId(),
            'request_id' => $this->getTransactionReference(),
            'ext_auth_success_uri' => $this->getReturnUrl(),
            'ext_auth_fail_uri' => $this->getCancelUrl(),
        );

        if ($cardReference = $this->getCardReference())
        {
            $this->validate('cvv');

            $params['money_source_token'] = $cardReference;
            $params['csc'] = $this->getCvv();
        }

        return $params;
    }

    /**
     * @param array $data
     *
     * @return Response
     */
    public function sendData($data)
    {
        do
        {
            /** @var Response $response */
            $response = parent::sendData($data);

            if ($response->inProgress()) sleep(1);
        }

        while ($response->inProgress());

        return $response;
    }
}