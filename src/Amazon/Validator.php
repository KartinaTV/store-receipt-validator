<?php
namespace ReceiptValidator\Amazon;

use ReceiptValidator\Abstracts\AbstractValidator;
use ReceiptValidator\RunTimeException as RunTimeException;
use GuzzleHttp\Exception\RequestException;

class Validator extends AbstractValidator
{

    const ENDPOINT_SANDBOX = 'http://localhost:8080/RVSSandbox/';
    const ENDPOINT_PRODUCTION = 'https://appstore-sdk.amazon.com/version/1.0/verifyReceiptId/';

  /**
   * endpoint url
   *
   * @var string
   */
    protected $endpoint;

  /**
   * Guzzle http client
   *
   * @var \GuzzleHttp\Client
   */
    protected $client = null;


  /**
   * @var string
   */
    protected $userId = null;

  /**
   * @var string
   */
    protected $developerSecret = null;

  /**
   * @var string
   */
    protected $productId = null;

    public function __construct($endpoint = self::ENDPOINT_PRODUCTION)
    {
        if ($endpoint != self::ENDPOINT_PRODUCTION && $endpoint != self::ENDPOINT_SANDBOX) {
            throw new RunTimeException("Invalid endpoint '{$endpoint}'");
        }

        $this->endpoint = $endpoint;
    }


  /**
   *
   * @param string $userId
   * @return \ReceiptValidator\Amazon\Validator
   */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }


  /**
   * get developer secret
   *
   * @return string
   */
    public function getDeveloperSecret()
    {
        return $this->developerSecret;
    }

  /**
   *
   * @param int $developerSecret
   * @return \ReceiptValidator\Amazon\Validator
   */
    public function setDeveloperSecret($developerSecret)
    {
        $this->developerSecret = $developerSecret;

        return $this;
    }


  /**
   * returns the Guzzle client
   *
   * @return \GuzzleHttp\Client
   */
    protected function getClient()
    {
        if ($this->client == null) {
            $this->client = new \GuzzleHttp\Client(['base_uri' => $this->endpoint]);
        }

        return $this->client;
    }

  /**
   * validate the receipt data
   *
   * @return Response
   */
    public function validate()
    {
        try {
            $params = sprintf(
                "developer/%s/user/%s/receiptId/%s",
                $this->developerSecret,
                $this->userId,
                $this->getPurchaseToken()
            );
            $httpResponse = $this->getClient()->request('GET', $params);

            return new Response($httpResponse->getStatusCode(), json_decode($httpResponse->getBody(), true));
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $body = $e->getResponse()->getBody();
                return new Response($e->getResponse()->getStatusCode(), json_decode($body, true));
            }
        }

        return new Response(Response::RESULT_INVALID_RECEIPT);
    }
}
