<?php

declare(strict_types=1);

namespace Podium\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Podium\Api\Enums\Decision;
use Podium\Api\PodiumDecision;

class PodiumDecisionTest extends TestCase
{
    public function testAllow(): void
    {
        self::assertSame(Decision::ALLOW, PodiumDecision::allow()->getDecision());
    }

    public function testDeny(): void
    {
        self::assertSame(Decision::DENY, PodiumDecision::deny()->getDecision());
    }

    public function testAbstain(): void
    {
        self::assertSame(Decision::ABSTAIN, PodiumDecision::abstain()->getDecision());
    }
}
