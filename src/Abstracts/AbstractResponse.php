<?php

namespace ReceiptValidator\Abstracts;

use ReceiptValidator\Interfaces\IResponse;

abstract class AbstractResponse implements IResponse
{
    protected $response;

    public function getRawResponse()
    {
        return $this->response;
    }
}
