<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 20.03.17
 * Time: 16:13
 */

namespace ReceiptValidator\Abstracts;

use ReceiptValidator\Interfaces\IValidator;
use ReceiptValidator\RunTimeException;

abstract class AbstractValidator implements IValidator
{
    protected $purchaseToken;

    /**
     * @return mixed
     * @throws RunTimeException
     */
    protected function getPurchaseToken()
    {
        if (empty($this->purchaseToken)) {
            throw new RunTimeException("Purchase token is not set");
        }
        return $this->purchaseToken;
    }

    /**
     * @param $purchase_token
     * @return static
     */
    public function setPurchaseToken($purchase_token)
    {
        $this->purchaseToken = $purchase_token;

        return $this;
    }
}
