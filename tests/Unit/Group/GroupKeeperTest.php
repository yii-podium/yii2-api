<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Group;

use Exception;
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

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $member->method('isGroupMember')->willReturn(true);
        $result = $this->service->join($this->createMock(GroupRepositoryInterface::class), $member);

        self::assertFalse($result->getResult());
        self::assertSame('group.already.joined', $result->getErrors()['api']);
    }

    public function testJoinShouldReturnErrorWhenAddingGroupMemberErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $member->method('isGroupMember')->willReturn(false);
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('addMember')->willReturn(false);
        $group->method('getErrors')->willReturn([1]);
        $result = $this->service->join($group, $member);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testJoinShouldReturnErrorWhenMemberIsBanned(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(true);
        $result = $this->service->join($this->createMock(GroupRepositoryInterface::class), $member);

        self::assertFalse($result->getResult());
        self::assertSame(['api' => 'member.banned'], $result->getErrors());
    }

    public function testJoinShouldReturnSuccessWhenAddingGroupMemberIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $member->method('isGroupMember')->willReturn(false);
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('addMember')->willReturn(true);
        $result = $this->service->join($group, $member);

        self::assertTrue($result->getResult());
    }

    public function testCreateShouldReturnErrorWhenAddingGroupMemberThrowsException(): void
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

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $member->method('isGroupMember')->willReturn(false);
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('addMember')->willThrowException(new Exception('exc'));
        $result = $this->service->join($group, $member);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testLeaveShouldReturnErrorWhenMemberNotJoinedBefore(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $member->method('isGroupMember')->willReturn(false);
        $result = $this->service->leave($this->createMock(GroupRepositoryInterface::class), $member);

        self::assertFalse($result->getResult());
        self::assertSame('group.not.joined', $result->getErrors()['api']);
    }

    public function testLeaveShouldReturnErrorWhenRemovingGroupMemberErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $member->method('isGroupMember')->willReturn(true);
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('removeMember')->willReturn(false);
        $group->method('getErrors')->willReturn([1]);
        $result = $this->service->leave($group, $member);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testLeaveShouldReturnErrorWhenMemberIsBanned(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(true);
        $result = $this->service->leave($this->createMock(GroupRepositoryInterface::class), $member);

        self::assertFalse($result->getResult());
        self::assertSame(['api' => 'member.banned'], $result->getErrors());
    }

    public function testLeaveShouldReturnSuccessWhenRemovingGroupMemberIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $member->method('isGroupMember')->willReturn(true);
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('removeMember')->willReturn(true);
        $result = $this->service->leave($group, $member);

        self::assertTrue($result->getResult());
    }

    public function testLeaveShouldReturnErrorWhenRemovingGroupMemberThrowsException(): void
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

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $member->method('isGroupMember')->willReturn(true);
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('removeMember')->willThrowException(new Exception('exc'));
        $result = $this->service->leave($group, $member);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
