<?php
namespace ReceiptValidator\Amazon;

use ReceiptValidator\Abstracts\AbstractResponse;
use ReceiptValidator\RunTimeException;

class Response extends AbstractResponse
{
  /**
   * Response Codes
   *
   * @var int
   */
    const RESULT_OK = 200;

  // Amazon RVS Error: Invalid receiptID
    const RESULT_INVALID_RECEIPT = 400;

  // Amazon RVS Error: Invalid developerSecret
    const RESULT_INVALID_DEVELOPER_SECRET = 496;

  // Amazon RVS Error: Invalid userId
    const RESULT_INVALID_USER_ID = 497;

  // Amazon RVS Error: Internal Server Error
    const RESULT_INTERNAL_ERROR = 500;


  /**
   * Result Code
   *
   * @var int
   */
    protected $code;

  /**
   * purchases info
   * @var PurchaseItem[]
   */
    protected $purchases = [];

  /**
   * Constructor
   *
   * @param int $httpStatusCode
   * @param array $jsonResponse
   */
    public function __construct($httpStatusCode = 200, $jsonResponse = null)
    {
        $this->code = $httpStatusCode;

        if ($jsonResponse !== null) {
            $this->parseJsonResponse($jsonResponse);
        }
    }

  /**
   * Get Result Code
   *
   * @return int
   */
    public function getResultCode()
    {
        return $this->code;
    }

  /**
   * Get purchases info
   *
   * @return PurchaseItem[]
   */
    public function getPurchases()
    {
        return $this->purchases;
    }

  /**
   * returns if the purchase is valid or not
   *
   * @return boolean
   */
    public function isValid()
    {
        if ($this->code == self::RESULT_OK) {
            return true;
        }

        return false;
    }

  /**
   * Parse JSON Response
   *
   * @param string $responseData
   *
   * @throws RunTimeException
   * @return $this
   */
    public function parseJsonResponse($responseData = null)
    {
        if (!is_array($responseData)) {
            throw new RunTimeException('Response must be a scalar value');
        }

        $this->response = $responseData;
        $this->purchases = [];
        $this->purchases[] = new PurchaseItem($responseData);

        return $this;
    }
}
