<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 20.03.17
 * Time: 16:09
 */

namespace ReceiptValidator\Interfaces;

interface IValidator
{
    public function validate();

    public function setPurchaseToken($purchase_token);
}
