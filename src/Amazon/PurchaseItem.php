<?php

namespace ReceiptValidator\Amazon;

use Carbon\Carbon;
use ReceiptValidator\RunTimeException;

class PurchaseItem
{
    /**
     * @var array
     */
    protected $response;

    /**
     * @var int
     */
    protected $quantity;

    /**
     * @var string
     */
    protected $productId;

    /**
     * @var string
     */
    protected $transactionId;

    /**
     * @var Carbon
     */
    protected $purchaseDate;

    /**
     * @var Carbon
     */
    protected $cancellationDate;

    /**
     * @param array $jsonResponse
     */
    public function __construct($jsonResponse = null)
    {
        $this->response = $jsonResponse;
        if (null !== $this->response) {
            $this->parseJsonResponse();
        }
    }

    /**
     * @return array
     */
    public function getRawResponse()
    {
        return $this->response;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return string
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @return Carbon
     */
    public function getPurchaseDate()
    {
        return $this->purchaseDate;
    }

    /**
     * @return Carbon
     */
    public function getCancellationDate()
    {
        return $this->cancellationDate;
    }

    /**
     * @throws RunTimeException
     *
     * @return PurchaseItem
     */
    public function parseJsonResponse()
    {
        $jsonResponse = $this->response;
        if (!\is_array($jsonResponse)) {
            throw new RuntimeException('Response must be a scalar value');
        }
        if (\array_key_exists('quantity', $jsonResponse)) {
            $this->quantity = $jsonResponse['quantity'];
        }
        if (\array_key_exists('receiptId', $jsonResponse)) {
            $this->transactionId = $jsonResponse['receiptId'];
        }
        if (\array_key_exists('productId', $jsonResponse)) {
            $this->productId = $jsonResponse['productId'];
        }
        if (\array_key_exists('purchaseDate', $jsonResponse) && !empty($jsonResponse['purchaseDate'])) {
            $this->purchaseDate = Carbon::createFromTimestampUTC(round($jsonResponse['purchaseDate'] / 1000));
        }
        if (\array_key_exists('cancelDate', $jsonResponse) && !empty($jsonResponse['cancelDate'])) {
            $this->cancellationDate = Carbon::createFromTimestampUTC(round($jsonResponse['cancelDate'] / 1000));
        }

        return $this;
    }
}
