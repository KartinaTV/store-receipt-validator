<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 21.03.17
 * Time: 11:11
 */

namespace ReceiptValidator\Abstracts;

use ReceiptValidator\Interfaces\IResponse;

abstract class AbstractResponse implements IResponse
{
    protected $response;

    /**
     * @return mixed
     */
    public function getRawResponse()
    {
        return $this->response;
    }
}
