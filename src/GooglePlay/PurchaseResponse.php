<?php

namespace ReceiptValidator\GooglePlay;

class PurchaseResponse extends AbstractResponse
{
    /**
     * @var \Google_Service_AndroidPublisher_ProductPurchase
     */
    protected $response;

    /**
     * @return string
     */
    public function getPurchaseTimeMillis()
    {
        return $this->response->purchaseTimeMillis;
    }
}
