<?php
namespace ReceiptValidator\iTunes;

use Carbon\Carbon;
use ReceiptValidator\RunTimeException;

class PurchaseItem
{

    /**
     * purchase item info
     *
     * @var array
     */
    protected $response;

    /**
     * quantity
     *
     * @var int
     */
    protected $quantity;

    /**
     * product_id
     *
     * @var string
     */
    protected $productId;

    /**
     * web_order_line_item_id
     *
     * @var string
     */
    protected $webOrderLineItemId;

    /**
     * transaction_id
     *
     * @var string
     */
    protected $transactionId;

    /**
     * original_transaction_id
     *
     * @var string
     */
    protected $originalTransactionId;

    /**
     * purchase_date
     *
     * @var Carbon
     */
    protected $purchaseDate;

    /**
     * original_purchase_date
     *
     * @var Carbon
     */
    protected $originalPurchaseDate;

    /**
     * expires_date
     *
     * @var Carbon
     */
    protected $expiresDate;

    /**
     * cancellation_date
     *
     * @var Carbon
     */
    protected $cancellationDate;

    /**
     * Constructor
     *
     * @param array $jsonResponse
     */
    public function __construct($jsonResponse = null)
    {
        $this->response = $jsonResponse;
        if ($this->response !== null) {
            $this->parseJsonResponse();
        }
    }

    /**
     * Parse JSON Response
     *
     * @return PurchaseItem
     * @throws RunTimeException
     */
    public function parseJsonResponse()
    {
        $jsonResponse = $this->response;
        if (!is_array($jsonResponse)) {
            throw new RuntimeException('Response must be a scalar value');
        }

        if (array_key_exists('quantity', $jsonResponse)) {
            $this->quantity = $jsonResponse['quantity'];
        }

        if (array_key_exists('transaction_id', $jsonResponse)) {
            $this->transactionId = $jsonResponse['transaction_id'];
        }

        if (array_key_exists('original_transaction_id', $jsonResponse)) {
            $this->originalTransactionId = $jsonResponse['original_transaction_id'];
        }

        if (array_key_exists('product_id', $jsonResponse)) {
            $this->productId = $jsonResponse['product_id'];
        }

        if (array_key_exists('web_order_line_item_id', $jsonResponse)) {
            $this->webOrderLineItemId = $jsonResponse['web_order_line_item_id'];
        }

        if (array_key_exists('purchase_date_ms', $jsonResponse)) {
            $this->purchaseDate = Carbon::createFromTimestampUTC(round($jsonResponse['purchase_date_ms'] / 1000));
        }

        if (array_key_exists('original_purchase_date_ms', $jsonResponse)) {
            $timestamp = round($jsonResponse['original_purchase_date_ms'] / 1000);
            $this->originalPurchaseDate = Carbon::createFromTimestampUTC($timestamp);
        }

        if (array_key_exists('expires_date_ms', $jsonResponse)) {
            $timestamp = round($jsonResponse['expires_date_ms'] / 1000);
            $this->expiresDate = Carbon::createFromTimestampUTC($timestamp);
        }

        if (array_key_exists('cancellation_date_ms', $jsonResponse)) {
            $timestamp = round($jsonResponse['cancellation_date_ms'] / 1000);
            $this->cancellationDate = Carbon::createFromTimestampUTC($timestamp);
        }

        return $this;
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
    public function getWebOrderLineItemId()
    {
        return $this->webOrderLineItemId;
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @return string
     */
    public function getOriginalTransactionId()
    {
        return $this->originalTransactionId;
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
    public function getOriginalPurchaseDate()
    {
        return $this->originalPurchaseDate;
    }

    /**
     * @return Carbon
     */
    public function getExpiresDate()
    {
        return $this->expiresDate;
    }

    /**
     * @return Carbon
     */
    public function getCancellationDate()
    {
        return $this->cancellationDate;
    }
}
