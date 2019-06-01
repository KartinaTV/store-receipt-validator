<?php

use PHPUnit\Framework\TestCase;
use ReceiptValidator\RunTimeException;

/**
 * @group library
 *
 * @internal
 * @coversNothing
 */
final class ExceptionsTest extends TestCase
{
    public function testRunTimeException()
    {
        $e = new RunTimeException();

        static::assertInstanceOf(RunTimeException::class, $e);
    }
}
