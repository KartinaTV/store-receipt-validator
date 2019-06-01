<?php

namespace ReceiptValidator\WindowsStore;

use ReceiptValidator\Abstracts\AbstractResponse;
use ReceiptValidator\RunTimeException;

class Response extends AbstractResponse
{
    protected $isValid;

    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * @throws RunTimeException
     *
     * @return bool
     */
    public function isValid()
    {
        if (null === $this->isValid) {
            throw new RunTimeException('You must create instance with factory method to use this method');
        }

        return $this->isValid;
    }

    /**
     * @param bool $isValid
     *
     * @return static
     */
    public static function factory($response, $isValid)
    {
        $instance = new static($response);
        $instance->isValid = (bool) $isValid;

        return $instance;
    }
}
