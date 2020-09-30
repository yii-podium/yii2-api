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

    public function testBeforeBefriendShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeBefriend());
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
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $result = $this->service->befriend($acquaintance, $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testBefriendShouldReturnErrorWhenTargetIsMember(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
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
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
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
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
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
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $result = $this->service->befriend($acquaintance, $member, $target);

        self::assertTrue($result->getResult());
    }

    public function testBefriendShouldReturnErrorWhenBefriendingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isFriend')->willReturn(false);
        $acquaintance->method('befriend')->willThrowException(new Exception('exc'));
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $result = $this->service->befriend($acquaintance, $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testBeforeUnfriendShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeUnfriend());
    }

    public function testUnfriendShouldReturnErrorWhenUnfriendingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isFriend')->willReturn(true);
        $acquaintance->method('delete')->willReturn(false);
        $acquaintance->method('getErrors')->willReturn([1]);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $result = $this->service->unfriend($acquaintance, $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testUnfriendShouldReturnErrorWhenTargetIsMember(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $result = $this->service->unfriend($acquaintance, $member, $member);

        self::assertFalse($result->getResult());
        self::assertSame('target.is.member', $result->getErrors()['api']);
    }

    public function testUnfriendShouldReturnSuccessWhenUnfriendingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isFriend')->willReturn(true);
        $acquaintance->method('delete')->willReturn(true);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $result = $this->service->unfriend($acquaintance, $member, $target);

        self::assertTrue($result->getResult());
    }

    public function testUnfriendShouldReturnErrorWhenUnfriendingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isFriend')->willReturn(true);
        $acquaintance->method('delete')->willThrowException(new Exception('exc'));
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $result = $this->service->unfriend($acquaintance, $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testUnfriendShouldReturnErrorWhenAcquaintanceNotExists(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $result = $this->service->unfriend($acquaintance, $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame('acquaintance.not.exists', $result->getErrors()['api']);
    }

    public function testUnfriendShouldReturnErrorWhenTargetIsNotFriend(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isFriend')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $result = $this->service->unfriend($acquaintance, $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame('target.is.not.friend', $result->getErrors()['api']);
    }

    public function testBeforeIgnoreShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeIgnore());
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
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $result = $this->service->ignore($acquaintance, $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testIgnoreShouldReturnErrorWhenTargetIsAlreadyIgnored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isIgnoring')->willReturn(true);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
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
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
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
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $result = $this->service->ignore($acquaintance, $member, $target);

        self::assertTrue($result->getResult());
    }

    public function testIgnoreShouldReturnErrorWhenIgnoringThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isIgnoring')->willReturn(false);
        $acquaintance->method('ignore')->willThrowException(new Exception('exc'));
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $result = $this->service->ignore($acquaintance, $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testBeforeUnignoreShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeUnignore());
    }

    public function testUnignoreShouldReturnErrorWhenUnignoringErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isIgnoring')->willReturn(true);
        $acquaintance->method('delete')->willReturn(false);
        $acquaintance->method('getErrors')->willReturn([1]);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $result = $this->service->unignore($acquaintance, $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testUnignoreShouldReturnErrorWhenTargetIsMember(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $result = $this->service->unignore($acquaintance, $member, $member);

        self::assertFalse($result->getResult());
        self::assertSame('target.is.member', $result->getErrors()['api']);
    }

    public function testUnignoreShouldReturnSuccessWhenUnignoringIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isIgnoring')->willReturn(true);
        $acquaintance->method('delete')->willReturn(true);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $result = $this->service->unignore($acquaintance, $member, $target);

        self::assertTrue($result->getResult());
    }

    public function testUnignoreShouldReturnErrorWhenUnignoringThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isIgnoring')->willReturn(true);
        $acquaintance->method('delete')->willThrowException(new Exception('exc'));
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $result = $this->service->unignore($acquaintance, $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testUnignoreShouldReturnErrorWhenAcquaintanceNotExists(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $result = $this->service->unignore($acquaintance, $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame('acquaintance.not.exists', $result->getErrors()['api']);
    }

    public function testUnignoreShouldReturnErrorWhenTargetIsNotIgnored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isIgnoring')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $result = $this->service->unignore($acquaintance, $member, $target);

        self::assertFalse($result->getResult());
        self::assertSame('target.is.not.ignored', $result->getErrors()['api']);
    }
}
