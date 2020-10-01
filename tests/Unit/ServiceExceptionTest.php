<?php

declare(strict_types=1);

namespace Podium\Tests\Unit;

use Exception;
use PHPUnit\Framework\TestCase;
use Podium\Api\Services\ServiceException;

class ServiceExceptionTest extends TestCase
{
    public function testExceptionName(): void
    {
        self::assertSame('Service Exception', (new ServiceException())->getName());
    }

    public function testErrorList(): void
    {
        $exc = new ServiceException([1]);

        self::assertSame([1], $exc->getErrorList());
    }

    public function testDefaults(): void
    {
        $exc = new ServiceException();

        self::assertSame([], $exc->getErrorList());
        self::assertSame('', $exc->getMessage());
        self::assertSame(0, $exc->getCode());
        self::assertNull($exc->getPrevious());
    }

    public function testExceptionArguments(): void
    {
        $exc = new ServiceException([], 'test', 2, new Exception());

        self::assertSame('test', $exc->getMessage());
        self::assertSame(2, $exc->getCode());
        self::assertNotNull($exc->getPrevious());
    }
}
