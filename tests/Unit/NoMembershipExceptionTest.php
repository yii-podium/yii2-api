<?php

declare(strict_types=1);

namespace Podium\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Podium\Api\Components\NoMembershipException;

class NoMembershipExceptionTest extends TestCase
{
    public function testExceptionName(): void
    {
        self::assertSame('No Membership Exception', (new NoMembershipException())->getName());
    }
}
