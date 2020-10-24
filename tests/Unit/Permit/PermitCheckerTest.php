<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Permit;

use Exception;
use Podium\Api\Enums\Decision;
use Podium\Api\Interfaces\DeciderInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\PodiumDecision;
use Podium\Api\Services\Permit\PermitChecker;
use Podium\Tests\AppTestCase;

class PermitCheckerTest extends AppTestCase
{
    private PermitChecker $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PermitChecker();
    }

    public function testCheckShouldDenyWhenDeciderDenies(): void
    {
        $decider = $this->createMock(DeciderInterface::class);
        $decider->method('decide')->willReturn(PodiumDecision::deny());

        self::assertSame(Decision::DENY, $this->service->check($decider, '')->getDecision());
    }

    public function testCheckShouldAllowWhenDeciderAllows(): void
    {
        $decider = $this->createMock(DeciderInterface::class);
        $decider->method('decide')->willReturn(PodiumDecision::allow());

        self::assertSame(Decision::ALLOW, $this->service->check($decider, '')->getDecision());
    }

    public function testCheckShouldAbstainWhenDeciderAbstains(): void
    {
        $decider = $this->createMock(DeciderInterface::class);
        $decider->method('decide')->willReturn(PodiumDecision::abstain());

        self::assertSame(Decision::ABSTAIN, $this->service->check($decider, '')->getDecision());
    }

    public function testCheckShouldPassAllArgumentsToDecider(): void
    {
        $subject = $this->createMock(RepositoryInterface::class);
        $member = $this->createMock(MemberRepositoryInterface::class);

        $decider = $this->createMock(DeciderInterface::class);
        $decider->expects(self::once())->method('setType')->with('type');
        $decider->expects(self::once())->method('setSubject')->with($subject);
        $decider->expects(self::once())->method('setMember')->with($member);
        $decider->expects(self::once())->method('decide')->willReturn(PodiumDecision::allow());

        $this->service->check($decider, 'type', $subject, $member);
    }

    public function testCheckShouldDenyWhenSetTypeThrowsException(): void
    {
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while checking permit' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $decider = $this->createMock(DeciderInterface::class);
        $decider->method('setType')->willThrowException(new Exception('exc'));
        $result = $this->service->check($decider, '');

        self::assertSame(Decision::DENY, $result->getDecision());
    }

    public function testCheckShouldDenyWhenSetSubjectThrowsException(): void
    {
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while checking permit' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $decider = $this->createMock(DeciderInterface::class);
        $decider->method('setSubject')->willThrowException(new Exception('exc'));
        $result = $this->service->check($decider, '');

        self::assertSame(Decision::DENY, $result->getDecision());
    }

    public function testCheckShouldDenyWhenSetMemberThrowsException(): void
    {
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while checking permit' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $decider = $this->createMock(DeciderInterface::class);
        $decider->method('setMember')->willThrowException(new Exception('exc'));
        $result = $this->service->check($decider, '');

        self::assertSame(Decision::DENY, $result->getDecision());
    }
}
