<?php

namespace Omnipay\Paytrace\Message;

use Omnipay\Common\Http\Exception;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $method;
    protected $type;
    protected $responseClass;

    public function sendData($data)
    {
//        var_dump($data);
//        debug_print_backtrace();
//        die(__FILE__.':'.__LINE__)
        ;

        $token = $this->getToken();
        $accessToken = $token['access_token'];
        $headers = [
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ];


        $httpResponse = $this->httpClient->request(
            'POST',
            $this->getEndpoint(),
            $headers,
            json_encode($data)
        );
        $responseClass = $this->responseClass;
        return $this->response = new $responseClass($this, $httpResponse->getBody());
    }

    public function getUserName()
    {
        return $this->getParameter('username');
    }

    public function setUserName($value)
    {
        return $this->setParameter('username', $value);
    }

    public function getPassword()
    {
        return $this->getParameter('password');
    }

    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    public function getIntegratorId()
    {
        return $this->getParameter('integratorId');
    }

    public function setIntegratorId($value)
    {
        return $this->setParameter('integratorId', $value);
    }

    public function getEndpoint()
    {
        return $this->getParameter('endpoint');
    }

    public function setEndpoint($value)
    {
        return $this->setParameter('endpoint', $value);
    }

    public function getBaseUrl()
    {
        return $this->getParameter('baseUrl');
    }

    public function setBaseUrl($value)
    {
        return $this->setParameter('baseUrl', $value);
    }

    public function getInvoiceId()
    {
        return $this->getParameter('invoiceId');
    }

    public function setInvoiceId($value)
    {
        return $this->setParameter('invoiceId', $value);
    }

    public function getCardReference()
    {
        return $this->getParameter('custid');
    }

    public function setCardReference($value)
    {
        return $this->setParameter('custid', $value);
    }

    /**
     * @return \Omnipay\Common\CreditCard|\Omnipay\Paytrace\Check
     */
    protected function getBillingSource()
    {
        return null; // @codeCoverageIgnore
    }

    protected function getBillingData()
    {
        $data = [
            'amount' => $this->getAmount(),
            'description' => $this->getDescription(),
            'invoice_id' => $this->getInvoiceId() ?? '',
        ];

        $source = $this->getBillingSource();
        if (!$source) {
            return $data; // @codeCoverageIgnore
        }

        $data['phone'] = $source->getPhone();
        $data['email'] = $source->getEmail();

        $billingAddress = [];
        $billingAddress['name'] = $source->getBillingName();
        $billingAddress['street_address'] = $source->getBillingAddress1();
        $billingAddress['street_address2'] = $source->getBillingAddress2();
        $billingAddress['city'] = $source->getBillingCity();
        $billingAddress['country'] = $source->getBillingCountry();
        $billingAddress['state'] = $source->getBillingState();
        $billingAddress['zip'] = $source->getBillingPostcode();
        $data['billing_address'] = array_filter($billingAddress);

        $shippingAddress = [];
        $shippingAddress['street_address'] = $source->getShippingAddress1();
        $shippingAddress['street_address2'] = $source->getShippingAddress2();
        $shippingAddress['city'] = $source->getShippingCity();
        $shippingAddress['city'] = $source->getShippingCountry();
        $shippingAddress['state'] = $source->getShippingState();
        $shippingAddress['zip'] = $source->getShippingPostcode();
        $data['shipping_address'] = array_filter($shippingAddress);

        return $data;
    }


    public function getToken(): array
    {
        try {
        $response = $this->httpClient->request(
            'POST',
            $this->getBaseUrl() . '/oauth/token',
            [
                'Accept' => '*/*'
            ],
            http_build_query([
                'grant_type' => 'password',
                'username' => $this->getUserName(),
                'password' => $this->getPassword(),
            ])
        );} catch (Exception $httpEx) {
            var_dump($httpEx->getMessage());
            die(__FILE__.':'.__LINE__);
        }

//        debug_print_backtrace();

        $result =  \GuzzleHttp\json_decode($response->getBody(), true);

//        die(__FILE__.':'.__LINE__);

        return $result;
    }



}
