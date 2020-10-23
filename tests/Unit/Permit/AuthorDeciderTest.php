<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Permit;

use PHPUnit\Framework\TestCase;
use Podium\Api\Enums\Decision;
use Podium\Api\Enums\PermitType;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Permit\AuthorDecider;

class AuthorDeciderTest extends TestCase
{
    private AuthorDecider $decider;

    protected function setUp(): void
    {
        $this->decider = new AuthorDecider();
    }

    public function providerForIrrelevantTypes(): array
    {
        return [[null], [''], [PermitType::CREATE], [PermitType::READ]];
    }

    /**
     * @dataProvider providerForIrrelevantTypes
     */
    public function testDeciderShouldAbstainWhenTypeIsIrrelevant(?string $type): void
    {
        $this->decider->setType($type);
        self::assertSame(Decision::ABSTAIN, $this->decider->decide()->getDecision());
    }

    public function providerForRelevantTypes(): array
    {
        return [[PermitType::UPDATE], [PermitType::DELETE]];
    }

    /**
     * @dataProvider providerForRelevantTypes
     */
    public function testDeciderShouldDenyWhenSubjectIsNull(?string $type): void
    {
        $this->decider->setMember($this->createMock(MemberRepositoryInterface::class));
        $this->decider->setType($type);
        self::assertSame(Decision::DENY, $this->decider->decide()->getDecision());
    }

    /**
     * @dataProvider providerForRelevantTypes
     */
    public function testDeciderShouldDenyWhenMemberIsNull(?string $type): void
    {
        $this->decider->setSubject($this->createMock(RepositoryInterface::class));
        $this->decider->setType($type);
        self::assertSame(Decision::DENY, $this->decider->decide()->getDecision());
    }

    /**
     * @dataProvider providerForRelevantTypes
     */
    public function testDeciderShouldDenyWhenMemberIsNotAuthor(?string $type): void
    {
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('getId')->willReturn(2);
        $subject = $this->createMock(RepositoryInterface::class);
        $subject->method('getAuthor')->willReturn($author);

        $this->decider->setMember($member);
        $this->decider->setSubject($subject);
        $this->decider->setType($type);
        self::assertSame(Decision::DENY, $this->decider->decide()->getDecision());
    }

    /**
     * @dataProvider providerForRelevantTypes
     */
    public function testDeciderShouldAllowWhenMemberIsAuthor(?string $type): void
    {
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $subject = $this->createMock(RepositoryInterface::class);
        $subject->method('getAuthor')->willReturn($member);

        $this->decider->setMember($member);
        $this->decider->setSubject($subject);
        $this->decider->setType($type);
        self::assertSame(Decision::ALLOW, $this->decider->decide()->getDecision());
    }
}
