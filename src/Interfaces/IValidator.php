<?php

namespace ReceiptValidator\Interfaces;

interface IValidator
{
    public function validate();

    public function setPurchaseToken($purchase_token);
}
