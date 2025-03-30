<?php

use PHPUnit\Framework\TestCase;
use Mockery;

abstract class BaseTestCase extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function assertException(string $exceptionName, string $exceptionMessage = '', int $exceptionCode = 0): void
    {
        $this->expectException($exceptionName);
        if ($exceptionMessage) {
            $this->expectExceptionMessage($exceptionMessage);
        }
        if ($exceptionCode) {
            $this->expectExceptionCode($exceptionCode);
        }
    }
}
