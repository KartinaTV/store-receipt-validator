<?php

use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\TestCase;
use ReceiptValidator\iTunes\Response;
use ReceiptValidator\RunTimeException;

/**
 * @group library
 *
 * @internal
 * @coversNothing
 */
final class iTunesResponseTest extends TestCase
{
    public function testInvalidOptionsToConstructor()
    {
        $this->expectException(RunTimeException::class);

        new Response('invalid');
    }

    public function testInvalidReceipt()
    {
        $response = new Response(['status' => Response::RESULT_DATA_MALFORMED, 'receipt' => []]);

        static::assertFalse($response->isValid(), 'receipt must be invalid');
        static::assertEquals(Response::RESULT_DATA_MALFORMED, $response->getResultCode(), 'receipt result code must match');
    }

    public function testReceiptSentToWrongEndpoint()
    {
        $response = new Response(['status' => Response::RESULT_SANDBOX_RECEIPT_SENT_TO_PRODUCTION]);

        static::assertFalse($response->isValid(), 'receipt must be invalid');
        static::assertEquals(Response::RESULT_SANDBOX_RECEIPT_SENT_TO_PRODUCTION, $response->getResultCode(), 'receipt result code must match');
    }

    public function testValidReceipt()
    {
        $response = new Response(['status' => Response::RESULT_OK, 'receipt' => ['testValue']]);

        static::assertTrue($response->isValid(), 'receipt must be valid');
        static::assertEquals(Response::RESULT_OK, $response->getResultCode(), 'receipt result code must match');
    }

    public function testReceiptWithLatestReceiptInfo()
    {
        $jsonResponseString = file_get_contents(__DIR__.'/fixtures/inAppPurchaseResponse.json');
        $jsonResponseArray = json_decode($jsonResponseString, true);

        $response = new Response($jsonResponseArray);

        static::assertInternalType(IsType::TYPE_ARRAY, $response->getLatestReceiptInfo());
        static::assertEquals($jsonResponseArray['latest_receipt_info'], $response->getLatestReceiptInfo(), 'latest receipt info must match');

        static::assertInternalType(IsType::TYPE_STRING, $response->getLatestReceipt());
        static::assertEquals($jsonResponseArray['latest_receipt'], $response->getLatestReceipt(), 'latest receipt must match');

        static::assertInternalType(IsType::TYPE_STRING, $response->getBundleId());
        static::assertEquals($jsonResponseArray['receipt']['bundle_id'], $response->getBundleId(), 'receipt bundle id must match');
    }
}
