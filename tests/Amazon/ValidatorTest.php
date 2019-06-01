<?php

use PHPUnit\Framework\TestCase;
use ReceiptValidator\Amazon\Validator as AmazonValidator;

/**
 * @group library
 *
 * @internal
 * @coversNothing
 */
final class AmazonValidatorTest extends TestCase
{
    /**
     * @var AmazonValidator
     */
    private $validator;

    protected function setUp()
    {
        parent::setUp();

        $this->validator = new AmazonValidator();
    }

    public function testSetEndpoint()
    {
        $this->validator->setDeveloperSecret('SECRET');

        static::assertEquals('SECRET', $this->validator->getDeveloperSecret());
    }

    public function testValidateWithNoReceiptData()
    {
        $response = $this->validator->setDeveloperSecret('NA')->setPurchaseToken('ID')->setUserId('ID')->validate();

        static::assertFalse($response->isValid(), 'receipt must be invalid');
    }
}
