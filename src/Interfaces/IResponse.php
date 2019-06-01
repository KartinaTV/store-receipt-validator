<?php

namespace ReceiptValidator\Interfaces;

interface IResponse
{
    public function isValid();

    public function getRawResponse();
}
