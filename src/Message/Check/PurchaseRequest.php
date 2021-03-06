<?php

namespace Omnipay\TalusPay\Message\Check;

class PurchaseRequest extends AuthorizeRequest
{
    protected $type = 'Sale';

    public function getEndpoint()
    {
        return $this->getParameter('baseUrl') .'/v1/customer/create';
    }
}
