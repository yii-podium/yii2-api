<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Permit;

use PHPUnit\Framework\TestCase;
use Podium\Api\Enums\Decision;
use Podium\Api\Enums\PermitType;
use Podium\Api\Interfaces\DeciderInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\PodiumDecision;
use Podium\Api\Services\Permit\AndDecider;

class AndDeciderTest extends TestCase
{
    private AndDecider $decider;

    protected function setUp(): void
    {
        $this->decider = new AndDecider();
    }

    private function getAllowDecider(): DeciderInterface
    {
        $decider = $this->createMock(DeciderInterface::class);
        $decider->method('decide')->willReturn(PodiumDecision::allow());

        return $decider;
    }

    private function getDenyDecider(): DeciderInterface
    {
        $decider = $this->createMock(DeciderInterface::class);
        $decider->method('decide')->willReturn(PodiumDecision::deny());

        return $decider;
    }

    private function getAbstainDecider(): DeciderInterface
    {
        $decider = $this->createMock(DeciderInterface::class);
        $decider->method('decide')->willReturn(PodiumDecision::abstain());

        return $decider;
    }

    private function getDeciders(int $allow = 0, int $deny = 0, int $abstain = 0): array
    {
        $deciders = [];

        for ($i = 0; $i < $allow; ++$i) {
            $deciders[] = $this->getAllowDecider();
        }
        for ($i = 0; $i < $deny; ++$i) {
            $deciders[] = $this->getDenyDecider();
        }
        for ($i = 0; $i < $abstain; ++$i) {
            $deciders[] = $this->getAbstainDecider();
        }

        return $deciders;
    }

    public function providerForDeciders(): array
    {
        return [
            [0, 0, 0, Decision::ALLOW],
            [1, 0, 0, Decision::ALLOW],
            [0, 0, 1, Decision::ALLOW],
            [0, 1, 0, Decision::DENY],
            [2, 0, 0, Decision::ALLOW],
            [0, 0, 2, Decision::ALLOW],
            [1, 0, 1, Decision::ALLOW],
            [1, 1, 0, Decision::DENY],
            [0, 1, 1, Decision::DENY],
            [2, 1, 0, Decision::DENY],
        ];
    }

    /**
     * @dataProvider providerForDeciders
     */
    public function testDeciderShouldReturnProperDecision(int $allow, int $deny, int $abstain, int $decision): void
    {
        $this->decider->setDeciders($this->getDeciders($allow, $deny, $abstain));

        self::assertSame($decision, $this->decider->decide()->getDecision());
    }

    public function testDeciderShouldPassDataToItDeciders(): void
    {
        $decider = $this->createMock(DeciderInterface::class);
        $decider->expects(self::once())->method('setType')->with(PermitType::READ);
        $decider->expects(self::once())->method('setSubject')->with(
            self::callback(
                static function ($subject) {
                    return $subject instanceof RepositoryInterface;
                }
            )
        );
        $decider->expects(self::once())->method('setMember')->with(
            self::callback(
                static function ($member) {
                    return $member instanceof MemberRepositoryInterface;
                }
            )
        );
        $decider->method('decide')->willReturn(PodiumDecision::deny());
        $this->decider->setDeciders([$decider]);

        $this->decider->setType(PermitType::READ);
        $this->decider->setSubject($this->createMock(RepositoryInterface::class));
        $this->decider->setMember($this->createMock(MemberRepositoryInterface::class));
        $this->decider->decide();
    }

    public function testDeciderShouldCreateInstanceOfDeciderWhenClassnameGiven(): void
    {
        $this->decider->setDeciders([DummyDecider::class]);
        self::assertSame(Decision::DENY, $this->decider->decide()->getDecision());
    }

    public function testDeciderShouldCreateInstanceOfDeciderWhenArrayConfigGiven(): void
    {
        $this->decider->setDeciders([['class' => DummyDecider::class]]);
        self::assertSame(Decision::DENY, $this->decider->decide()->getDecision());
    }
}
