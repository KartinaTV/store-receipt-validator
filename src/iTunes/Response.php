<?php

namespace ReceiptValidator\iTunes;

use ReceiptValidator\Abstracts\AbstractResponse;
use ReceiptValidator\RunTimeException;

class Response extends AbstractResponse
{
    /**
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
     * @var int
     */
    protected $code;

    /**
     * bundle_id (app) belongs to the receipt.
     *
     * @var string
     */
    protected $bundleId;

    /**
     * @var array
     */
    protected $receipt = [];

    /**
     * @var string
     */
    protected $latestReceipt;

    /**
     * @var array
     */
    protected $latestReceiptInfo;

    /**
     * @var PurchaseItem[]
     */
    protected $purchases = [];

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
     * @throws RunTimeException
     *
     * @return Response
     */
    public function parseJsonResponse()
    {
        $jsonResponse = $this->response;
        if (!\is_array($jsonResponse)) {
            throw new RunTimeException('Response must be a scalar value');
        }

        // ios > 7 receipt validation
        if (\array_key_exists('receipt', $jsonResponse)
            && \is_array($jsonResponse['receipt'])
            && \array_key_exists('in_app', $jsonResponse['receipt'])
            && \is_array($jsonResponse['receipt']['in_app'])
        ) {
            $this->code = $jsonResponse['status'];
            $this->receipt = $jsonResponse['receipt'];
            $this->_app_item_id = $this->receipt['app_item_id'];
            $this->purchases = [];

            foreach ($jsonResponse['receipt']['in_app'] as $purchase_item_data) {
                $this->purchases[] = new PurchaseItem($purchase_item_data);
            }

            if (\array_key_exists('bundle_id', $jsonResponse['receipt'])) {
                $this->bundleId = $jsonResponse['receipt']['bundle_id'];
            }

            if (\array_key_exists('latest_receipt_info', $jsonResponse)) {
                $this->latestReceiptInfo = $jsonResponse['latest_receipt_info'];
            }

            if (\array_key_exists('latest_receipt', $jsonResponse)) {
                $this->latestReceipt = $jsonResponse['latest_receipt'];
            }
        } elseif (\array_key_exists('receipt', $jsonResponse)) {
            // ios <= 6.0 validation
            $this->code = $jsonResponse['status'];

            if (\array_key_exists('receipt', $jsonResponse)) {
                $this->receipt = $jsonResponse['receipt'];
                $this->purchases = [];
                $this->purchases[] = new PurchaseItem($jsonResponse['receipt']);

                if (\array_key_exists('bid', $jsonResponse['receipt'])) {
                    $this->bundleId = $jsonResponse['receipt']['bid'];
                }
            }
        } elseif (\array_key_exists('status', $jsonResponse)) {
            $this->code = $jsonResponse['status'];
        } else {
            $this->code = self::RESULT_DATA_MALFORMED;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getResultCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     *
     * @return Response
     */
    public function setResultCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return PurchaseItem[]
     */
    public function getPurchases()
    {
        return $this->purchases;
    }

    /**
     * @return array
     */
    public function getReceipt()
    {
        return $this->receipt;
    }

    /**
     * @return array
     */
    public function getLatestReceiptInfo()
    {
        return $this->latestReceiptInfo;
    }

    /**
     * @return string
     */
    public function getLatestReceipt()
    {
        return $this->latestReceipt;
    }

    /**
     * @return string
     */
    public function getBundleId()
    {
        return $this->bundleId;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return self::RESULT_OK == $this->code;
    }
}
