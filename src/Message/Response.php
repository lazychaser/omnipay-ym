<?php

namespace Omnipay\YM\Message;

use Omnipay\Common\Message\AbstractResponse as OmnipayAbstractResponse;
use Omnipay\Common\Message\RequestInterface;

class Response extends OmnipayAbstractResponse {

    /**
     * Operation is not performed, another request is needed.
     */
    const IN_PROGRESS = 'in_progress';

    /**
     * Operation succeeded.
     */
    const SUCCESS = 'success';

    /**
     * Operation refused.
     */
    const REFUSED = 'refused';

    /**
     * Redirect is required.
     */
    const REDIRECT = 'ext_auth_required';

    /**
     * @param RequestInterface $request
     * @param $data
     */
    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, json_decode($data, true));
    }

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return $this->data['status'] == self::SUCCESS;
    }

    /**
     * Get whether the operation is still in progress, meaning that retry is need after some time.
     *
     * @return bool
     */
    public function inProgress()
    {
        return $this->data['status'] == self::IN_PROGRESS;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        if (isset($this->data['error']))
        {
            return $this->data['error'];
        }

        return null;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->getCode();
    }

    /**
     * Gets the redirect target url.
     */
    public function getRedirectUrl()
    {
        if ($this->isRedirect())
        {
            return $this->data['acs_uri'].'?'.http_build_query($this->data['acs_params']);
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isRedirect()
    {
        return $this->data['status'] == self::REDIRECT;
    }

    /**
     * Get the required redirect method (either GET or POST).
     */
    public function getRedirectMethod()
    {
        return 'GET';
    }

    /**
     * Gets the redirect form data array, if the redirect method is POST.
     */
    public function getRedirectData()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getTransactionReference()
    {
        foreach (array('invoice_id', 'request_id', 'instance_id') as $key)
        {
            if (isset($this->data[$key])) return $this->data[$key];
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getCardReference()
    {
        if (isset($this->data['money_source']))
        {
            return $this->data['money_source']['money_source_token'];
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getCardType()
    {
        if (isset($this->data['money_source']))
        {
            return $this->data['money_source']['payment_card_type'];
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getCardNumber()
    {
        if (isset($this->data['money_source']))
        {
            return $this->data['money_source']['pan_fragment'];
        }

        return null;
    }

}