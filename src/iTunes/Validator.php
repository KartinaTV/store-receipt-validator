<?php

namespace ReceiptValidator\iTunes;

use GuzzleHttp\Client as HttpClient;
use ReceiptValidator\Abstracts\AbstractValidator;
use ReceiptValidator\RunTimeException;

class Validator extends AbstractValidator
{
    const ENDPOINT_SANDBOX = 'https://sandbox.itunes.apple.com/verifyReceipt';
    const ENDPOINT_PRODUCTION = 'https://buy.itunes.apple.com/verifyReceipt';

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * The shared secret is a unique code to receive your In-App Purchase receipts.
     * Without a shared secret, you will not be able to test or offer your automatically
     * renewable In-App Purchase subscriptions.
     *
     * @var string
     */
    protected $sharedSecret;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    public function __construct($endpoint = self::ENDPOINT_PRODUCTION)
    {
        if (self::ENDPOINT_PRODUCTION != $endpoint && self::ENDPOINT_SANDBOX != $endpoint) {
            throw new RunTimeException("Invalid endpoint '{$endpoint}'");
        }

        $this->endpoint = $endpoint;
    }

    /**
     * set receipt data, either in base64, or in json.
     *
     * @param string $tokenData
     *
     * @return \ReceiptValidator\iTunes\Validator;
     */
    public function setPurchaseToken($tokenData)
    {
        if (false !== strpos($tokenData, '{')) {
            $tokenData = base64_encode($tokenData);
        }
        parent::setPurchaseToken($tokenData);

        return $this;
    }

    /**
     * @return string
     */
    public function getSharedSecret()
    {
        return $this->sharedSecret;
    }

    /**
     * @param string $sharedSecret
     *
     * @return $this
     */
    public function setSharedSecret($sharedSecret)
    {
        $this->sharedSecret = $sharedSecret;

        return $this;
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param string $endpoint
     *
     * @return $this
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * validate the receipt data.
     *
     * @throws RunTimeException
     *
     * @return Response
     */
    public function validate()
    {
        $httpResponse = $this->getClient()->request('POST', null, ['body' => $this->encodeRequest()]);

        if (200 != $httpResponse->getStatusCode()) {
            throw new RunTimeException('Unable to get response from itunes server');
        }

        $response = new Response(json_decode($httpResponse->getBody(), true));

        // on a 21007 error retry the request in the sandbox environment (if the current environment is Production)
        // these are receipts from apple review team
        if (self::ENDPOINT_PRODUCTION == $this->endpoint
            && Response::RESULT_SANDBOX_RECEIPT_SENT_TO_PRODUCTION == $response->getResultCode()) {
            $client = new HttpClient(['base_uri' => self::ENDPOINT_SANDBOX]);

            $httpResponse = $client->request('POST', null, ['body' => $this->encodeRequest()]);

            if (200 != $httpResponse->getStatusCode()) {
                throw new RunTimeException('Unable to get response from itunes server');
            }

            $response = new Response(json_decode($httpResponse->getBody(), true));
        }

        return $response;
    }

    /**
     * @return \GuzzleHttp\Client
     */
    protected function getClient()
    {
        if (null == $this->client) {
            $this->client = new \GuzzleHttp\Client(['base_uri' => $this->endpoint]);
        }

        return $this->client;
    }

    /**
     * encode the request in json.
     *
     * @return string
     */
    private function encodeRequest()
    {
        $request = ['receipt-data' => $this->getPurchaseToken()];

        if (null !== $this->sharedSecret) {
            $request['password'] = $this->sharedSecret;
        }

        return json_encode($request);
    }
}
