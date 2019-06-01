<?php

namespace ReceiptValidator\WindowsStore;

interface CacheInterface
{
    /**
     * @param string $key
     */
    public function get($key);

    /**
     * @param string $key
     * @param int    $minutes
     */
    public function put($key, $value, $minutes);
}
