<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Group;

use Exception;
use PHPUnit\Framework\TestCase;
use Podium\Api\Interfaces\GroupMemberRepositoryInterface;
use Podium\Api\Interfaces\GroupRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Services\Group\GroupKeeper;

class GroupKeeperTest extends TestCase
{
    private GroupKeeper $service;

    protected function setUp(): void
    {
        $this->service = new GroupKeeper();
    }

    public function testBeforeJoinShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeJoin());
    }

    public function testJoinShouldReturnErrorWhenMemberAlreadyJoined(): void
    {
        $groupMember = $this->createMock(GroupMemberRepositoryInterface::class);
        $groupMember->method('fetchOne')->willReturn(true);
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('getGroupMember')->willReturn($groupMember);
        $result = $this->service->join($group, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('group.already.joined', $result->getErrors()['api']);
    }

    public function testJoinShouldReturnErrorWhenGroupMemberCreatingErrored(): void
    {
        $groupMember = $this->createMock(GroupMemberRepositoryInterface::class);
        $groupMember->method('fetchOne')->willReturn(false);
        $groupMember->method('create')->willReturn(false);
        $groupMember->method('getErrors')->willReturn([1]);
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('getGroupMember')->willReturn($groupMember);
        $result = $this->service->join($group, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testJoinShouldReturnSuccessWhenCreatingGroupMemberIsDone(): void
    {
        $groupMember = $this->createMock(GroupMemberRepositoryInterface::class);
        $groupMember->method('fetchOne')->willReturn(false);
        $groupMember->method('create')->willReturn(true);
        $groupMember->method('getErrors')->willReturn([1]);
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('getGroupMember')->willReturn($groupMember);
        $result = $this->service->join($group, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($result->getResult());
    }

    public function testCreateShouldReturnErrorWhenCreatingGroupMemberThrowsException(): void
    {
        $groupMember = $this->createMock(GroupMemberRepositoryInterface::class);
        $groupMember->method('fetchOne')->willReturn(false);
        $groupMember->method('create')->willThrowException(new Exception('exc'));
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('getGroupMember')->willReturn($groupMember);
        $result = $this->service->join($group, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testBeforeLeaveShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeLeave());
    }

    public function testEditShouldReturnErrorWhenMemberNotJoinedBefore(): void
    {
        $groupMember = $this->createMock(GroupMemberRepositoryInterface::class);
        $groupMember->method('fetchOne')->willReturn(false);
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('getGroupMember')->willReturn($groupMember);
        $result = $this->service->leave($group, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('group.not.joined', $result->getErrors()['api']);
    }

    public function testEditShouldReturnErrorWhenDeletingGroupMemberErrored(): void
    {
        $groupMember = $this->createMock(GroupMemberRepositoryInterface::class);
        $groupMember->method('fetchOne')->willReturn(true);
        $groupMember->method('delete')->willReturn(false);
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('getGroupMember')->willReturn($groupMember);
        $result = $this->service->leave($group, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testEditShouldReturnSuccessWhenDeletingGroupMemberIsDone(): void
    {
        $groupMember = $this->createMock(GroupMemberRepositoryInterface::class);
        $groupMember->method('fetchOne')->willReturn(true);
        $groupMember->method('delete')->willReturn(true);
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('getGroupMember')->willReturn($groupMember);
        $result = $this->service->leave($group, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($result->getResult());
    }

    public function testEditShouldReturnErrorWhenDeletingGroupMemberThrowsException(): void
    {
        $groupMember = $this->createMock(GroupMemberRepositoryInterface::class);
        $groupMember->method('fetchOne')->willReturn(true);
        $groupMember->method('delete')->willThrowException(new Exception('exc'));
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('getGroupMember')->willReturn($groupMember);
        $result = $this->service->leave($group, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
