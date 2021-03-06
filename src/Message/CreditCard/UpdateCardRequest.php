<?php
namespace Omnipay\TalusPay\Message\CreditCard;

class UpdateCardRequest extends CreateCardRequest
{
    protected $type = 'UpdateCustomer';

    public function getEndpoint()
    {
        return $this->getParameter('baseUrl') .'/v1/customer/update';
    }
}
