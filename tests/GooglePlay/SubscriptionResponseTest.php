<?php

namespace ReceiptValidator\GooglePlay;

use PHPUnit\Framework\TestCase;

/**
 * @group library
 *
 * @internal
 * @coversNothing
 */
final class SubscriptionResponseTest extends TestCase
{
    public function testParsedResponse()
    {
        $autoRenewing = 'testAutoRenewing';
        $cancelReason = 'testCancelReason';
        $countryCode = 'testCountryCode';
        $priceAmountMicros = 'testPriceAmountMicros';
        $priceCurrencyCode = 'testPriceCurrencyCode';
        $startTimeMillis = 'testStartTimeMillis';
        $expiryTimeMillis = 'testExpiryTimeMillis';

        // mock objects
        $subscriptionPurchaseMock = $this->getMockBuilder('\Google_Service_AndroidPublisher_SubscriptionPurchase')
            ->disableOriginalConstructor()->getMock();

        $subscriptionPurchaseMock->autoRenewing = $autoRenewing;
        $subscriptionPurchaseMock->cancelReason = $cancelReason;
        $subscriptionPurchaseMock->countryCode = $countryCode;
        $subscriptionPurchaseMock->priceAmountMicros = $priceAmountMicros;
        $subscriptionPurchaseMock->priceCurrencyCode = $priceCurrencyCode;
        $subscriptionPurchaseMock->startTimeMillis = $startTimeMillis;
        $subscriptionPurchaseMock->expiryTimeMillis = $expiryTimeMillis;

        $subscriptionResponse = new SubscriptionResponse($subscriptionPurchaseMock);

        static::assertInstanceOf('ReceiptValidator\GooglePlay\AbstractResponse', $subscriptionResponse);
        static::assertEquals($autoRenewing, $subscriptionResponse->getAutoRenewing());
        static::assertEquals($cancelReason, $subscriptionResponse->getCancelReason());
        static::assertEquals($countryCode, $subscriptionResponse->getCountryCode());
        static::assertEquals($priceAmountMicros, $subscriptionResponse->getPriceAmountMicros());
        static::assertEquals($priceCurrencyCode, $subscriptionResponse->getPriceCurrencyCode());
        static::assertEquals($startTimeMillis, $subscriptionResponse->getStartTimeMillis());
        static::assertEquals($expiryTimeMillis, $subscriptionResponse->getExpiresDate());
        static::assertEquals($subscriptionPurchaseMock, $subscriptionResponse->getRawResponse());
    }
}
