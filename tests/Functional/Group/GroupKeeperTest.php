<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Group;

use Podium\Api\Events\GroupEvent;
use Podium\Api\Interfaces\GroupRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Services\Group\GroupKeeper;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class GroupKeeperTest extends AppTestCase
{
    private GroupKeeper $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GroupKeeper();
        $this->eventsRaised = [];
    }

    public function testJoinShouldTriggerBeforeAndAfterEventsWhenAddingGroupMemberIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[GroupKeeper::EVENT_BEFORE_JOINING] = $event instanceof GroupEvent;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_JOINING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[GroupKeeper::EVENT_AFTER_JOINING] = $event instanceof GroupEvent
                && $event->repository instanceof GroupRepositoryInterface;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_AFTER_JOINING, $afterHandler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isGroupMember')->willReturn(false);
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('addMember')->willReturn(true);
        $this->service->join($group, $member);

        self::assertTrue($this->eventsRaised[GroupKeeper::EVENT_BEFORE_JOINING]);
        self::assertTrue($this->eventsRaised[GroupKeeper::EVENT_AFTER_JOINING]);

        Event::off(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_JOINING, $beforeHandler);
        Event::off(GroupKeeper::class, GroupKeeper::EVENT_AFTER_JOINING, $afterHandler);
    }

    public function testJoinShouldOnlyTriggerBeforeEventWhenMemberAlreadyJoined(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[GroupKeeper::EVENT_BEFORE_JOINING] = true;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_JOINING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[GroupKeeper::EVENT_AFTER_JOINING] = true;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_AFTER_JOINING, $afterHandler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isGroupMember')->willReturn(true);
        $this->service->join($this->createMock(GroupRepositoryInterface::class), $member);

        self::assertTrue($this->eventsRaised[GroupKeeper::EVENT_BEFORE_JOINING]);
        self::assertArrayNotHasKey(GroupKeeper::EVENT_AFTER_JOINING, $this->eventsRaised);

        Event::off(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_JOINING, $beforeHandler);
        Event::off(GroupKeeper::class, GroupKeeper::EVENT_AFTER_JOINING, $afterHandler);
    }

    public function testJoinShouldOnlyTriggerBeforeEventWhenAddingGroupMemberErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[GroupKeeper::EVENT_BEFORE_JOINING] = true;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_JOINING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[GroupKeeper::EVENT_AFTER_JOINING] = true;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_AFTER_JOINING, $afterHandler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isGroupMember')->willReturn(false);
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('addMember')->willReturn(false);
        $this->service->join($group, $member);

        self::assertTrue($this->eventsRaised[GroupKeeper::EVENT_BEFORE_JOINING]);
        self::assertArrayNotHasKey(GroupKeeper::EVENT_AFTER_JOINING, $this->eventsRaised);

        Event::off(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_JOINING, $beforeHandler);
        Event::off(GroupKeeper::class, GroupKeeper::EVENT_AFTER_JOINING, $afterHandler);
    }

    public function testJoinShouldReturnErrorWhenEventPreventsJoining(): void
    {
        $handler = static function (GroupEvent $event) {
            $event->canJoin = false;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_JOINING, $handler);

        $result = $this->service->join(
            $this->createMock(GroupRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_JOINING, $handler);
    }

    public function testLeaveShouldTriggerBeforeAndAfterEventsWhenRemovingGroupMemberIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[GroupKeeper::EVENT_BEFORE_LEAVING] = $event instanceof GroupEvent;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_LEAVING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[GroupKeeper::EVENT_AFTER_LEAVING] = $event instanceof GroupEvent;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_AFTER_LEAVING, $afterHandler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isGroupMember')->willReturn(true);
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('removeMember')->willReturn(true);
        $this->service->leave($group, $member);

        self::assertTrue($this->eventsRaised[GroupKeeper::EVENT_BEFORE_LEAVING]);
        self::assertTrue($this->eventsRaised[GroupKeeper::EVENT_AFTER_LEAVING]);

        Event::off(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_LEAVING, $beforeHandler);
        Event::off(GroupKeeper::class, GroupKeeper::EVENT_AFTER_LEAVING, $afterHandler);
    }

    public function testEditShouldOnlyTriggerBeforeEventWhenMemberNotJoinedGroupBefore(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[GroupKeeper::EVENT_BEFORE_LEAVING] = true;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_LEAVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[GroupKeeper::EVENT_AFTER_LEAVING] = true;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_AFTER_LEAVING, $afterHandler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isGroupMember')->willReturn(false);
        $this->service->leave($this->createMock(GroupRepositoryInterface::class), $member);

        self::assertTrue($this->eventsRaised[GroupKeeper::EVENT_BEFORE_LEAVING]);
        self::assertArrayNotHasKey(GroupKeeper::EVENT_AFTER_LEAVING, $this->eventsRaised);

        Event::off(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_LEAVING, $beforeHandler);
        Event::off(GroupKeeper::class, GroupKeeper::EVENT_AFTER_LEAVING, $afterHandler);
    }

    public function testEditShouldOnlyTriggerBeforeEventWhenRemovingMemberErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[GroupKeeper::EVENT_BEFORE_LEAVING] = true;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_LEAVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[GroupKeeper::EVENT_AFTER_LEAVING] = true;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_AFTER_LEAVING, $afterHandler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isGroupMember')->willReturn(true);
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('removeMember')->willReturn(false);
        $this->service->leave($group, $member);

        self::assertTrue($this->eventsRaised[GroupKeeper::EVENT_BEFORE_LEAVING]);
        self::assertArrayNotHasKey(GroupKeeper::EVENT_AFTER_LEAVING, $this->eventsRaised);

        Event::off(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_LEAVING, $beforeHandler);
        Event::off(GroupKeeper::class, GroupKeeper::EVENT_AFTER_LEAVING, $afterHandler);
    }

    public function testEditShouldReturnErrorWhenEventPreventsLeaving(): void
    {
        $handler = static function (GroupEvent $event) {
            $event->canLeave = false;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_LEAVING, $handler);

        $result = $this->service->leave(
            $this->createMock(GroupRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_LEAVING, $handler);
    }
}
