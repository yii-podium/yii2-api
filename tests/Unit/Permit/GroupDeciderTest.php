<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Permit;

use PHPUnit\Framework\TestCase;
use Podium\Api\Enums\Decision;
use Podium\Api\Interfaces\GroupRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Permit\GroupDecider;

class GroupDeciderTest extends TestCase
{
    private GroupDecider $decider;

    protected function setUp(): void
    {
        $this->decider = new GroupDecider();
    }

    public function testDeciderShouldAbstainWhenSubjectIsNull(): void
    {
        self::assertSame(Decision::ABSTAIN, $this->decider->decide()->getDecision());
    }

    public function testDeciderShouldAbstainWhenSubjectHasNoGroups(): void
    {
        $subject = $this->createMock(RepositoryInterface::class);
        $subject->method('getAllowedGroups')->willReturn([]);
        $this->decider->setSubject($subject);
        self::assertSame(Decision::ABSTAIN, $this->decider->decide()->getDecision());
    }

    public function testDeciderShouldDenyWhenMemberIsNull(): void
    {
        $subject = $this->createMock(RepositoryInterface::class);
        $subject->method('getAllowedGroups')->willReturn(
            [$this->createMock(GroupRepositoryInterface::class)]
        );
        $this->decider->setSubject($subject);
        self::assertSame(Decision::DENY, $this->decider->decide()->getDecision());
    }

    public function testDeciderShouldDenyWhenMemberIsNotInRequiredGroups(): void
    {
        $subject = $this->createMock(RepositoryInterface::class);
        $subject->method('getAllowedGroups')->willReturn(
            [$this->createMock(GroupRepositoryInterface::class)]
        );
        $this->decider->setSubject($subject);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isGroupMember')->with(
            self::callback(
                static function ($groups) {
                    return $groups[0] instanceof GroupRepositoryInterface;
                }
            )
        )->willReturn(false);
        $this->decider->setMember($member);
        self::assertSame(Decision::DENY, $this->decider->decide()->getDecision());
    }

    public function testDeciderShouldAllowWhenMemberIsInRequiredGroups(): void
    {
        $subject = $this->createMock(RepositoryInterface::class);
        $subject->method('getAllowedGroups')->willReturn(
            [$this->createMock(GroupRepositoryInterface::class)]
        );
        $this->decider->setSubject($subject);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isGroupMember')->with(
            self::callback(
                static function ($groups) {
                    return $groups[0] instanceof GroupRepositoryInterface;
                }
            )
        )->willReturn(true);
        $this->decider->setMember($member);
        self::assertSame(Decision::ALLOW, $this->decider->decide()->getDecision());
    }
}
