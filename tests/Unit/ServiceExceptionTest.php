<?php

declare(strict_types=1);

namespace Podium\Tests\Unit;

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
}
