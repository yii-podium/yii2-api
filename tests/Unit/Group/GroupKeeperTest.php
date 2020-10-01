<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Group;

use Exception;
use Podium\Api\Interfaces\GroupMemberRepositoryInterface;
use Podium\Api\Interfaces\GroupRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Services\Group\GroupKeeper;
use Podium\Tests\AppTestCase;

class GroupKeeperTest extends AppTestCase
{
    private GroupKeeper $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GroupKeeper();
    }

    public function testJoinShouldReturnErrorWhenMemberAlreadyJoined(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

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
        $this->transaction->expects(self::once())->method('rollBack');

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
        $this->transaction->expects(self::once())->method('commit');

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
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while joining group' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $groupMember = $this->createMock(GroupMemberRepositoryInterface::class);
        $groupMember->method('fetchOne')->willReturn(false);
        $groupMember->method('create')->willThrowException(new Exception('exc'));
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('getGroupMember')->willReturn($groupMember);
        $result = $this->service->join($group, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testEditShouldReturnErrorWhenMemberNotJoinedBefore(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

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
        $this->transaction->expects(self::once())->method('rollBack');

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
        $this->transaction->expects(self::once())->method('commit');

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
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while leaving group' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

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
