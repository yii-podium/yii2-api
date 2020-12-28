<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Permit;

use PHPUnit\Framework\TestCase;
use Podium\Api\Enums\Decision;
use Podium\Api\Enums\PermitType;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Permit\RoleDecider;

class RoleDeciderTest extends TestCase
{
    private RoleDecider $decider;

    protected function setUp(): void
    {
        $this->decider = new RoleDecider();
    }

    public function testDeciderShouldAbstainWhenRoleIsNull(): void
    {
        self::assertSame(Decision::ABSTAIN, $this->decider->decide()->getDecision());
    }

    public function testDeciderShouldDenyWhenMemberIsNull(): void
    {
        $this->decider->setSubject($this->createMock(RepositoryInterface::class));
        self::assertSame(Decision::DENY, $this->decider->decide()->getDecision());
    }

    public function testDeciderShouldAllowWhenMemberHasRole(): void
    {
        $this->decider->setType(PermitType::READ);
        $this->decider->setSubject($this->createMock(RepositoryInterface::class));

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('hasRole')->with(
            self::callback(
                static function ($subject) {
                    return $subject instanceof RepositoryInterface;
                }
            ),
            PermitType::READ
        )->willReturn(true);
        $this->decider->setMember($member);

        self::assertSame(Decision::ALLOW, $this->decider->decide()->getDecision());
    }

    public function testDeciderShouldDenyWhenMemberHasNoRole(): void
    {
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('hasRole')->willReturn(false);
        $this->decider->setMember($member);
        $this->decider->setSubject($this->createMock(RepositoryInterface::class));

        self::assertSame(Decision::DENY, $this->decider->decide()->getDecision());
    }
}
