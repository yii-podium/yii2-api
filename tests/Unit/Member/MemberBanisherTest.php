<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Member;

use Exception;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Services\Member\MemberBanisher;
use Podium\Tests\AppTestCase;

class MemberBanisherTest extends AppTestCase
{
    private MemberBanisher $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MemberBanisher();
    }

    public function testBeforeBanShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeBan());
    }

    public function testBanShouldReturnErrorWhenBanningErrored(): void
    {
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $member->method('ban')->willReturn(false);
        $member->method('getErrors')->willReturn([1]);
        $result = $this->service->ban($member);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testBanShouldReturnErrorWhenMemberIsAlreadyBanned(): void
    {
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(true);
        $result = $this->service->ban($member);

        self::assertFalse($result->getResult());
        self::assertSame('member.already.banned', $result->getErrors()['api']);
    }

    public function testBanShouldReturnSuccessWhenBanningIsDone(): void
    {
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $member->method('ban')->willReturn(true);
        $result = $this->service->ban($member);

        self::assertTrue($result->getResult());
    }

    public function testBanShouldReturnErrorWhenBanningThrowsException(): void
    {
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $member->method('ban')->willThrowException(new Exception('exc'));
        $result = $this->service->ban($member);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testBeforeUnbanShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeUnban());
    }

    public function testUnbanShouldReturnErrorWhenUnbanningErrored(): void
    {
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(true);
        $member->method('unban')->willReturn(false);
        $member->method('getErrors')->willReturn([1]);
        $result = $this->service->unban($member);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testUnbanShouldReturnErrorWhenMemberWasNotBanned(): void
    {
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $result = $this->service->unban($member);

        self::assertFalse($result->getResult());
        self::assertSame('member.not.banned', $result->getErrors()['api']);
    }

    public function testUnbanShouldReturnSuccessWhenUnbanningIsDone(): void
    {
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(true);
        $member->method('unban')->willReturn(true);
        $result = $this->service->unban($member);

        self::assertTrue($result->getResult());
    }

    public function testUnbanShouldReturnErrorWhenUnbanningThrowsException(): void
    {
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(true);
        $member->method('unban')->willThrowException(new Exception('exc'));
        $result = $this->service->unban($member);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
