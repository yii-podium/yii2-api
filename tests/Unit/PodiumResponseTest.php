<?php

declare(strict_types=1);

namespace Podium\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Podium\Api\PodiumResponse;

class PodiumResponseTest extends TestCase
{
    public function testEmptySuccess(): void
    {
        $response = PodiumResponse::success();

        self::assertTrue($response->getResult());
        self::assertSame([], $response->getErrors());
        self::assertSame([], $response->getData());
    }

    public function testDataSuccess(): void
    {
        $response = PodiumResponse::success([1]);

        self::assertTrue($response->getResult());
        self::assertSame([], $response->getErrors());
        self::assertSame([1], $response->getData());
    }

    public function testEmptyError(): void
    {
        $response = PodiumResponse::error();

        self::assertFalse($response->getResult());
        self::assertSame([], $response->getErrors());
        self::assertSame([], $response->getData());
    }

    public function testDataError(): void
    {
        $response = PodiumResponse::error([1]);

        self::assertFalse($response->getResult());
        self::assertSame([1], $response->getErrors());
        self::assertSame([], $response->getData());
    }
}
