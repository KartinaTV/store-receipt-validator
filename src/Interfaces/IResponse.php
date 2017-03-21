<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 21.03.17
 * Time: 11:09
 */

namespace ReceiptValidator\Interfaces;

interface IResponse
{
    public function isValid();
    
    public function getRawResponse();
}