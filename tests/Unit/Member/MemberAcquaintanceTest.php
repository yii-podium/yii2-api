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
        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('befriend')->willReturn(false);
        $acquaintance->method('getErrors')->willReturn([1]);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->befriend($acquaintance, $member, $member);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testBefriendShouldPrepareAcquaintanceWhenItDoesntExist(): void
    {
        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(false);
        $acquaintance->expects(self::once())->method('prepare');
        $acquaintance->method('befriend')->willReturn(true);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->befriend($acquaintance, $member, $member);

        self::assertTrue($result->getResult());
    }

    public function testBefriendShouldReturnSuccessWhenBefriendingIsDone(): void
    {
        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('befriend')->willReturn(true);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->befriend($acquaintance, $member, $member);

        self::assertTrue($result->getResult());
    }

    public function testBefriendShouldReturnErrorWhenBefriendingThrowsException(): void
    {
        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('befriend')->willThrowException(new Exception('exc'));
        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->befriend($acquaintance, $member, $member);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testBeforeUnfriendShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeUnfriend());
    }

    public function testUnfriendShouldReturnErrorWhenUnfriendingErrored(): void
    {
        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isIgnoring')->willReturn(false);
        $acquaintance->method('delete')->willReturn(false);
        $acquaintance->method('getErrors')->willReturn([1]);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->unfriend($acquaintance, $member, $member);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testUnfriendShouldReturnSuccessWhenUnfriendingIsDone(): void
    {
        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isIgnoring')->willReturn(false);
        $acquaintance->method('delete')->willReturn(true);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->unfriend($acquaintance, $member, $member);

        self::assertTrue($result->getResult());
    }

    public function testUnfriendShouldReturnErrorWhenUnfriendingThrowsException(): void
    {
        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isIgnoring')->willReturn(false);
        $acquaintance->method('delete')->willThrowException(new Exception('exc'));
        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->unfriend($acquaintance, $member, $member);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testUnfriendShouldReturnErrorWhenAcquaintanceNotExists(): void
    {
        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->unfriend($acquaintance, $member, $member);

        self::assertFalse($result->getResult());
        self::assertSame('acquaintance.not.exists', $result->getErrors()['api']);
    }

    public function testUnfriendShouldReturnErrorWhenMemberIsIgnoringAnother(): void
    {
        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isIgnoring')->willReturn(true);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->unfriend($acquaintance, $member, $member);

        self::assertFalse($result->getResult());
        self::assertSame('member.ignores.target', $result->getErrors()['api']);
    }

    public function testBeforeIgnoreShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeIgnore());
    }

    public function testIgnoreShouldReturnErrorWhenIgnoringErrored(): void
    {
        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('ignore')->willReturn(false);
        $acquaintance->method('getErrors')->willReturn([1]);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->ignore($acquaintance, $member, $member);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testIgnoreShouldPrepareAcquaintanceWhenItDoesntExist(): void
    {
        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(false);
        $acquaintance->expects(self::once())->method('prepare');
        $acquaintance->method('ignore')->willReturn(true);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->ignore($acquaintance, $member, $member);

        self::assertTrue($result->getResult());
    }

    public function testIgnoreShouldReturnSuccessWhenIgnoringIsDone(): void
    {
        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('ignore')->willReturn(true);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->ignore($acquaintance, $member, $member);

        self::assertTrue($result->getResult());
    }

    public function testIgnoreShouldReturnErrorWhenIgnoringThrowsException(): void
    {
        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('ignore')->willThrowException(new Exception('exc'));
        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->ignore($acquaintance, $member, $member);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testBeforeUnignoreShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeUnignore());
    }

    public function testUnignoreShouldReturnErrorWhenUnignoringErrored(): void
    {
        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isFriend')->willReturn(false);
        $acquaintance->method('delete')->willReturn(false);
        $acquaintance->method('getErrors')->willReturn([1]);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->unignore($acquaintance, $member, $member);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testUnignoreShouldReturnSuccessWhenUnignoringIsDone(): void
    {
        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isFriend')->willReturn(false);
        $acquaintance->method('delete')->willReturn(true);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->unignore($acquaintance, $member, $member);

        self::assertTrue($result->getResult());
    }

    public function testUnignoreShouldReturnErrorWhenUnignoringThrowsException(): void
    {
        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isFriend')->willReturn(false);
        $acquaintance->method('delete')->willThrowException(new Exception('exc'));
        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->unignore($acquaintance, $member, $member);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testUnignoreShouldReturnErrorWhenAcquaintanceNotExists(): void
    {
        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->unignore($acquaintance, $member, $member);

        self::assertFalse($result->getResult());
        self::assertSame('acquaintance.not.exists', $result->getErrors()['api']);
    }

    public function testUnignoreShouldReturnErrorWhenMemberIsIgnoringAnother(): void
    {
        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isFriend')->willReturn(true);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->unignore($acquaintance, $member, $member);

        self::assertFalse($result->getResult());
        self::assertSame('member.befriends.target', $result->getErrors()['api']);
    }
}
