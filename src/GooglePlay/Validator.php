<?php

namespace ReceiptValidator\GooglePlay;

use ReceiptValidator\Abstracts\AbstractValidator;

class Validator extends AbstractValidator
{
    /**
     * @var \Google_Service_AndroidPublisher
     */
    protected $androidPublisherService;

    /**
     * @var string
     */
    protected $packageName;

    /**
     * @var string
     */
    protected $productId;

    /**
     * @var bool
     */
    private $validationModePurchase = true;

    /**
     * @param bool $validationModePurchase
     */
    public function __construct(\Google_Service_AndroidPublisher $googleService, $validationModePurchase = true)
    {
        $this->androidPublisherService = $googleService;
        $this->validationModePurchase = $validationModePurchase;
    }

    /**
     * @param string $package_name
     *
     * @return $this
     */
    public function setPackageName($package_name)
    {
        $this->packageName = $package_name;

        return $this;
    }

    /**
     * @param string $product_id
     *
     * @return $this
     */
    public function setProductId($product_id)
    {
        $this->productId = $product_id;

        return $this;
    }

    /**
     * @param bool $validationModePurchase
     *
     * @return Validator
     */
    public function setValidationModePurchase($validationModePurchase)
    {
        $this->validationModePurchase = $validationModePurchase;

        return $this;
    }

    /**
     * @return PurchaseResponse|SubscriptionResponse
     */
    public function validate()
    {
        return ($this->validationModePurchase) ? $this->validatePurchase() : $this->validateSubscription();
    }

    /**
     * @return SubscriptionResponse
     */
    public function validateSubscription()
    {
        return new SubscriptionResponse($this->androidPublisherService->purchases_subscriptions->get(
            $this->packageName,
            $this->productId,
            $this->getPurchaseToken()
        ));
    }

    /**
     * @return PurchaseResponse
     */
    public function validatePurchase()
    {
        return new PurchaseResponse($this->androidPublisherService->purchases_products->get(
            $this->packageName,
            $this->productId,
            $this->getPurchaseToken()
        ));
    }
}
