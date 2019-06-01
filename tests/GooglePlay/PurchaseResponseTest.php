<?php

namespace ReceiptValidator\GooglePlay;

use PHPUnit\Framework\TestCase;

/**
 * @group library
 *
 * @internal
 * @coversNothing
 */
final class PurchaseResponseTest extends TestCase
{
    public function testParsedResponse()
    {
        $developerPayload = ['packageName' => 'testPackageName', 'etc' => 'testEtc'];
        $kind = 'testKind';
        $purchaseTimeMillis = '234346';

        // mock objects
        $productPurchaseMock = $this->getMockBuilder('\Google_Service_AndroidPublisher_ProductPurchase')
            ->disableOriginalConstructor()->getMock();

        $productPurchaseMock->consumptionState = PurchaseResponse::CONSUMPTION_STATE_YET_TO_BE_CONSUMED;
        $productPurchaseMock->developerPayload = json_encode($developerPayload);
        $productPurchaseMock->kind = $kind;
        $productPurchaseMock->purchaseState = PurchaseResponse::PURCHASE_STATE_CANCELED;
        $productPurchaseMock->purchaseTimeMillis = $purchaseTimeMillis;

        $productResponse = new PurchaseResponse($productPurchaseMock);

        // test abstract methods
        static::assertInstanceOf('ReceiptValidator\GooglePlay\AbstractResponse', $productResponse);
        static::assertEquals(PurchaseResponse::CONSUMPTION_STATE_YET_TO_BE_CONSUMED, $productResponse->getConsumptionState());
        static::assertEquals($developerPayload, $productResponse->getDeveloperPayload());
        static::assertEquals($kind, $productResponse->getKind());
        static::assertEquals(PurchaseResponse::PURCHASE_STATE_CANCELED, $productResponse->getPurchaseState());
        static::assertEquals($developerPayload['packageName'], $productResponse->getDeveloperPayloadElement('packageName'));
        static::assertEquals($developerPayload['etc'], $productResponse->getDeveloperPayloadElement('etc'));
        static::assertEquals('', $productResponse->getDeveloperPayloadElement('invalid'));
        // test own methods
        static::assertEquals($purchaseTimeMillis, $productResponse->getPurchaseTimeMillis());
        static::assertEquals($productPurchaseMock, $productResponse->getRawResponse());
    }
}
