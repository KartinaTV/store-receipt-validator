<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 21.03.17
 * Time: 11:32
 */

namespace ReceiptValidator\WindowsStore;

use ReceiptValidator\Abstracts\AbstractResponse;
use ReceiptValidator\RunTimeException;

/**
 * Class Response
 * @package ReceiptValidator\WindowsStore
 *
 * TODO: implement proxy methods for \DomDocument $response property
 */
class Response extends AbstractResponse
{
    protected $isValid = null;

    /**
     * Response constructor.
     * @param mixed $response
     */
    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * @return bool
     * @throws RunTimeException
     */
    public function isValid()
    {
        if (is_null($this->isValid)) {
            throw new RunTimeException("You must create instance with factory method to use this method");
        }
        return $this->isValid;
    }

    /**
     * @param mixed $response
     * @param bool $isValid
     * @return static
     */
    public static function factory($response, $isValid)
    {
        $instance = new static($response);
        $instance->isValid = boolval($isValid);
        return $instance;
    }
}
