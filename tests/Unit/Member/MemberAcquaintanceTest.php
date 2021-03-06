<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Member;

use Exception;
use Podium\Api\Interfaces\AcquaintanceRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Services\Member\MemberAcquaintance;
use Podium\Tests\AppTestCase;

class MemberAcquaintanceTest extends AppTestCase
{
    private MemberAcquaintance $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MemberAcquaintance();
    }

    public function testBefriendShouldReturnErrorWhenBefriendingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('befriend')->willReturn(false);
        $acquaintance->method('isFriend')->willReturn(false);
        $acquaintance->method('getErrors')->willReturn([1]);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $member->method('isBanned')->willReturn(false);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $target->method('isBanned')->willReturn(false);
        $result = $this->service->befriend($acquaintance, $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testBefriendShouldReturnErrorWhenMemberIsBanned(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $member->method('isBanned')->willReturn(true);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $target->method('isBanned')->willReturn(false);
        $result = $this->service->befriend($this->createMock(AcquaintanceRepositoryInterface::class), $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame(['api' => 'member.banned'], $result->getErrors());
    }

    public function testBefriendShouldReturnErrorWhenTargetIsBanned(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $member->method('isBanned')->willReturn(false);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $target->method('isBanned')->willReturn(true);
        $result = $this->service->befriend($this->createMock(AcquaintanceRepositoryInterface::class), $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame(['api' => 'member.banned'], $result->getErrors());
    }

    public function testBefriendShouldReturnErrorWhenTargetIsMember(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $member->method('isBanned')->willReturn(false);
        $result = $this->service->befriend($acquaintance, $member, $member);

        self::assertFalse($result->getResult());
        self::assertSame('target.is.member', $result->getErrors()['api']);
    }

    public function testBefriendShouldReturnErrorWhenTargetIsAlreadyFriend(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isFriend')->willReturn(true);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $member->method('isBanned')->willReturn(false);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $target->method('isBanned')->willReturn(false);
        $result = $this->service->befriend($acquaintance, $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame('target.already.friend', $result->getErrors()['api']);
    }

    public function testBefriendShouldPrepareAcquaintanceWhenItDoesntExist(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(false);
        $acquaintance->expects(self::once())->method('prepare');
        $acquaintance->method('befriend')->willReturn(true);
        $acquaintance->method('isFriend')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $member->method('isBanned')->willReturn(false);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $target->method('isBanned')->willReturn(false);
        $result = $this->service->befriend($acquaintance, $member, $target);

        self::assertTrue($result->getResult());
    }

    public function testBefriendShouldReturnSuccessWhenBefriendingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isFriend')->willReturn(false);
        $acquaintance->method('befriend')->willReturn(true);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $member->method('isBanned')->willReturn(false);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $target->method('isBanned')->willReturn(false);
        $result = $this->service->befriend($acquaintance, $member, $target);

        self::assertTrue($result->getResult());
    }

    public function testBefriendShouldReturnErrorWhenBefriendingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while befriending member' === $data[0]
                        && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isFriend')->willReturn(false);
        $acquaintance->method('befriend')->willThrowException(new Exception('exc'));
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $member->method('isBanned')->willReturn(false);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $target->method('isBanned')->willReturn(false);
        $result = $this->service->befriend($acquaintance, $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testIgnoreShouldReturnErrorWhenIgnoringErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('ignore')->willReturn(false);
        $acquaintance->method('isIgnoring')->willReturn(false);
        $acquaintance->method('getErrors')->willReturn([1]);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $member->method('isBanned')->willReturn(false);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $target->method('isBanned')->willReturn(false);
        $result = $this->service->ignore($acquaintance, $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testIgnoreShouldReturnErrorWhenMemberIsBanned(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $member->method('isBanned')->willReturn(true);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $target->method('isBanned')->willReturn(false);
        $result = $this->service->ignore($this->createMock(AcquaintanceRepositoryInterface::class), $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame(['api' => 'member.banned'], $result->getErrors());
    }

    public function testIgnoreShouldReturnErrorWhenTargetIsBanned(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $member->method('isBanned')->willReturn(false);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $target->method('isBanned')->willReturn(true);
        $result = $this->service->ignore($this->createMock(AcquaintanceRepositoryInterface::class), $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame(['api' => 'member.banned'], $result->getErrors());
    }

    public function testIgnoreShouldReturnErrorWhenTargetIsAlreadyIgnored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isIgnoring')->willReturn(true);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $member->method('isBanned')->willReturn(false);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $target->method('isBanned')->willReturn(false);
        $result = $this->service->ignore($acquaintance, $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame('target.already.ignored', $result->getErrors()['api']);
    }

    public function testIgnoreShouldReturnErrorWhenTargetIsMember(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $member->method('isBanned')->willReturn(false);
        $result = $this->service->ignore($acquaintance, $member, $member);

        self::assertFalse($result->getResult());
        self::assertSame('target.is.member', $result->getErrors()['api']);
    }

    public function testIgnoreShouldPrepareAcquaintanceWhenItDoesntExist(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(false);
        $acquaintance->expects(self::once())->method('prepare');
        $acquaintance->method('ignore')->willReturn(true);
        $acquaintance->method('isIgnoring')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $member->method('isBanned')->willReturn(false);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $target->method('isBanned')->willReturn(false);
        $result = $this->service->ignore($acquaintance, $member, $target);

        self::assertTrue($result->getResult());
    }

    public function testIgnoreShouldReturnSuccessWhenIgnoringIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('ignore')->willReturn(true);
        $acquaintance->method('isIgnoring')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $member->method('isBanned')->willReturn(false);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $target->method('isBanned')->willReturn(false);
        $result = $this->service->ignore($acquaintance, $member, $target);

        self::assertTrue($result->getResult());
    }

    public function testIgnoreShouldReturnErrorWhenIgnoringThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while ignoring member' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isIgnoring')->willReturn(false);
        $acquaintance->method('ignore')->willThrowException(new Exception('exc'));
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $member->method('isBanned')->willReturn(false);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $target->method('isBanned')->willReturn(false);
        $result = $this->service->ignore($acquaintance, $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testDisconnectShouldReturnErrorWhenDisconnectingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('delete')->willReturn(false);
        $acquaintance->method('getErrors')->willReturn([1]);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $member->method('isBanned')->willReturn(false);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $target->method('isBanned')->willReturn(false);
        $result = $this->service->disconnect($acquaintance, $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testDisconnectShouldReturnErrorWhenMemberIsBanned(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $member->method('isBanned')->willReturn(true);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $target->method('isBanned')->willReturn(false);
        $result = $this->service->disconnect(
            $this->createMock(AcquaintanceRepositoryInterface::class),
            $member,
            $target
        );

        self::assertFalse($result->getResult());
        self::assertSame(['api' => 'member.banned'], $result->getErrors());
    }

    public function testDisconnectShouldReturnErrorWhenTargetIsBanned(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $member->method('isBanned')->willReturn(false);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $target->method('isBanned')->willReturn(true);
        $result = $this->service->disconnect(
            $this->createMock(AcquaintanceRepositoryInterface::class),
            $member,
            $target
        );

        self::assertFalse($result->getResult());
        self::assertSame(['api' => 'member.banned'], $result->getErrors());
    }

    public function testDisconnectShouldReturnErrorWhenTargetIsMember(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $member->method('isBanned')->willReturn(false);
        $result = $this->service->disconnect($acquaintance, $member, $member);

        self::assertFalse($result->getResult());
        self::assertSame('target.is.member', $result->getErrors()['api']);
    }

    public function testDisconnectShouldReturnSuccessWhenDisconnectingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('delete')->willReturn(true);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $member->method('isBanned')->willReturn(false);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $target->method('isBanned')->willReturn(false);
        $result = $this->service->disconnect($acquaintance, $member, $target);

        self::assertTrue($result->getResult());
    }

    public function testDisconnectShouldReturnErrorWhenDisconnectingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while disconnecting member' === $data[0]
                        && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('delete')->willThrowException(new Exception('exc'));
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $member->method('isBanned')->willReturn(false);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $target->method('isBanned')->willReturn(false);
        $result = $this->service->disconnect($acquaintance, $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testDisconnectShouldReturnErrorWhenAcquaintanceNotExists(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $member->method('isBanned')->willReturn(false);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $target->method('isBanned')->willReturn(false);
        $result = $this->service->disconnect($acquaintance, $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame('acquaintance.not.exists', $result->getErrors()['api']);
    }
}
