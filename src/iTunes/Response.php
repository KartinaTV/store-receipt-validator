<?php
namespace ReceiptValidator\iTunes;

use ReceiptValidator\Abstracts\AbstractResponse;
use ReceiptValidator\RunTimeException;

class Response extends AbstractResponse
{
    /**
     * Response Codes
     *
     * @var int
     */
    const RESULT_OK = 0;

    // The App Store could not read the JSON object you provided.
    const RESULT_APPSTORE_CANNOT_READ = 21000;

    // The data in the receipt-data property was malformed or missing.
    const RESULT_DATA_MALFORMED = 21002;

    // The receipt could not be authenticated.
    const RESULT_RECEIPT_NOT_AUTHENTICATED = 21003;

    // The shared secret you provided does not match the shared secret on file for your account.
    // Only returned for iOS 6 style transaction receipts for auto-renewable subscriptions.
    const RESULT_SHARED_SECRET_NOT_MATCH = 21004;

    // The receipt server is not currently available.
    const RESULT_RECEIPT_SERVER_UNAVAILABLE = 21005;

    // This receipt is valid but the subscription has expired. When this status code is returned to your server,
    // the receipt data is also decoded and returned as part of the response.
    // Only returned for iOS 6 style transaction receipts for auto-renewable subscriptions.
    const RESULT_RECEIPT_VALID_BUT_SUB_EXPIRED = 21006;

    // This receipt is from the test environment, but it was sent to the production environment for verification.
    // Send it to the test environment instead.
    // special case for app review handling - forward any request that is intended for the Sandbox but was sent
    // to Production, this is what the app review team does
    const RESULT_SANDBOX_RECEIPT_SENT_TO_PRODUCTION = 21007;

    // This receipt is from the production environment, but it was sent to the test environment for verification.
    // Send it to the production environment instead.
    const RESULT_PRODUCTION_RECEIPT_SENT_TO_SANDBOX = 21008;

    /**
     * Result Code
     *
     * @var int
     */
    protected $code;

    /**
     * bundle_id (app) belongs to the receipt
     *
     * @var string
     */
    protected $bundleId;

    /**
     * receipt info
     *
     * @var array
     */
    protected $receipt = [];

    /**
     * latest receipt (needs for auto-renewable subscriptions)
     *
     * @var string
     */
    protected $latestReceipt;

    /**
     * latest receipt info (needs for auto-renewable subscriptions)
     *
     * @var array
     */
    protected $latestReceiptInfo;

    /**
     * purchases info
     * @var PurchaseItem[]
     */
    protected $purchases = [];

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
     * @return Response
     * @throws RunTimeException
     */
    public function parseJsonResponse()
    {
        $jsonResponse = $this->response;
        if (!is_array($jsonResponse)) {
            throw new RunTimeException('Response must be a scalar value');
        }

        // ios > 7 receipt validation
        if (array_key_exists('receipt', $jsonResponse)
            && is_array($jsonResponse['receipt'])
            && array_key_exists('in_app', $jsonResponse['receipt'])
            && is_array($jsonResponse['receipt']['in_app'])
        ) {
            $this->code = $jsonResponse['status'];
            $this->receipt = $jsonResponse['receipt'];
            $this->_app_item_id = $this->receipt['app_item_id'];
            $this->purchases = [];

            foreach ($jsonResponse['receipt']['in_app'] as $purchase_item_data) {
                $this->purchases[] = new PurchaseItem($purchase_item_data);
            }

            if (array_key_exists('bundle_id', $jsonResponse['receipt'])) {
                $this->bundleId = $jsonResponse['receipt']['bundle_id'];
            }

            if (array_key_exists('latest_receipt_info', $jsonResponse)) {
                $this->latestReceiptInfo = $jsonResponse['latest_receipt_info'];
            }

            if (array_key_exists('latest_receipt', $jsonResponse)) {
                $this->latestReceipt = $jsonResponse['latest_receipt'];
            }
        } elseif (array_key_exists('receipt', $jsonResponse)) {
            // ios <= 6.0 validation
            $this->code = $jsonResponse['status'];

            if (array_key_exists('receipt', $jsonResponse)) {
                $this->receipt = $jsonResponse['receipt'];
                $this->purchases = [];
                $this->purchases[] = new PurchaseItem($jsonResponse['receipt']);

                if (array_key_exists('bid', $jsonResponse['receipt'])) {
                    $this->bundleId = $jsonResponse['receipt']['bid'];
                }
            }
        } elseif (array_key_exists('status', $jsonResponse)) {
            $this->code = $jsonResponse['status'];
        } else {
            $this->code = self::RESULT_DATA_MALFORMED;
        }
        return $this;
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
     * Set Result Code
     *
     * @param int $code
     * @return Response
     */
    public function setResultCode($code)
    {
        $this->code = $code;

        return $this;
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
     * Get receipt info
     *
     * @return array
     */
    public function getReceipt()
    {
        return $this->receipt;
    }

    /**
     * Get latest receipt info
     *
     * @return array
     */
    public function getLatestReceiptInfo()
    {
        return $this->latestReceiptInfo;
    }

    /**
     * Get latest receipt
     *
     * @return string
     */
    public function getLatestReceipt()
    {
        return $this->latestReceipt;
    }

    /**
     * Get the bundle id associated with the receipt
     *
     * @return string
     */
    public function getBundleId()
    {
        return $this->bundleId;
    }

    /**
     * returns if the receipt is valid or not
     *
     * @return boolean
     */
    public function isValid()
    {
        return ($this->code == self::RESULT_OK);
    }
}

