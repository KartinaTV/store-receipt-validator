<?php
namespace ReceiptValidator\Amazon;

use ReceiptValidator\RunTimeException;
use Carbon\Carbon;

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
   * transaction_id
   *
   * @var string
   */
    protected $transactionId;

  /**
   * purchase_date
   *
   * @var Carbon
   */
    protected $purchaseDate;

  /**
   * cancellation_date
   *
   * @var Carbon
   */
    protected $cancellationDate;

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

        if (array_key_exists('receiptId', $jsonResponse)) {
            $this->transactionId = $jsonResponse['receiptId'];
        }

        if (array_key_exists('productId', $jsonResponse)) {
            $this->productId = $jsonResponse['productId'];
        }

        if (array_key_exists('purchaseDate', $jsonResponse) && !empty($jsonResponse['purchaseDate'])) {
            $this->purchaseDate = Carbon::createFromTimestampUTC(round($jsonResponse['purchaseDate'] / 1000));
        }

        if (array_key_exists('cancelDate', $jsonResponse) && !empty($jsonResponse['cancelDate'])) {
            $this->cancellationDate = Carbon::createFromTimestampUTC(round($jsonResponse['cancelDate'] / 1000));
        }

        return $this;
    }
}
