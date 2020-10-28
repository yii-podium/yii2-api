<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Group;

use Podium\Api\Events\GroupEvent;
use Podium\Api\Interfaces\GroupRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
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

    public function testJoinShouldTriggerBeforeAndAfterEventsWhenAddingGroupRepositoryIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[GroupKeeper::EVENT_BEFORE_JOINING] = $event instanceof GroupEvent;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_JOINING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[GroupKeeper::EVENT_AFTER_JOINING] = $event instanceof GroupEvent
                && $event->repository instanceof RepositoryInterface;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_AFTER_JOINING, $afterHandler);

        $repository = $this->createMock(RepositoryInterface::class);
        $repository->method('hasGroups')->willReturn(false);
        $repository->method('join')->willReturn(true);
        $this->service->join($this->createMock(GroupRepositoryInterface::class), $repository);

        self::assertTrue($this->eventsRaised[GroupKeeper::EVENT_BEFORE_JOINING]);
        self::assertTrue($this->eventsRaised[GroupKeeper::EVENT_AFTER_JOINING]);

        Event::off(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_JOINING, $beforeHandler);
        Event::off(GroupKeeper::class, GroupKeeper::EVENT_AFTER_JOINING, $afterHandler);
    }

    public function testJoinShouldOnlyTriggerBeforeEventWhenRepositoryAlreadyJoined(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[GroupKeeper::EVENT_BEFORE_JOINING] = true;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_JOINING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[GroupKeeper::EVENT_AFTER_JOINING] = true;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_AFTER_JOINING, $afterHandler);

        $repository = $this->createMock(RepositoryInterface::class);
        $repository->method('hasGroups')->willReturn(true);
        $this->service->join($this->createMock(GroupRepositoryInterface::class), $repository);

        self::assertTrue($this->eventsRaised[GroupKeeper::EVENT_BEFORE_JOINING]);
        self::assertArrayNotHasKey(GroupKeeper::EVENT_AFTER_JOINING, $this->eventsRaised);

        Event::off(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_JOINING, $beforeHandler);
        Event::off(GroupKeeper::class, GroupKeeper::EVENT_AFTER_JOINING, $afterHandler);
    }

    public function testJoinShouldOnlyTriggerBeforeEventWhenAddingGroupRepositoryErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[GroupKeeper::EVENT_BEFORE_JOINING] = true;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_JOINING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[GroupKeeper::EVENT_AFTER_JOINING] = true;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_AFTER_JOINING, $afterHandler);

        $repository = $this->createMock(RepositoryInterface::class);
        $repository->method('hasGroups')->willReturn(false);
        $repository->method('join')->willReturn(false);
        $this->service->join($this->createMock(GroupRepositoryInterface::class), $repository);

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

    public function testLeaveShouldTriggerBeforeAndAfterEventsWhenRemovingGroupRepositoryIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[GroupKeeper::EVENT_BEFORE_LEAVING] = $event instanceof GroupEvent;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_LEAVING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[GroupKeeper::EVENT_AFTER_LEAVING] = $event instanceof GroupEvent;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_AFTER_LEAVING, $afterHandler);

        $repository = $this->createMock(RepositoryInterface::class);
        $repository->method('hasGroups')->willReturn(true);
        $repository->method('leave')->willReturn(true);
        $this->service->leave($this->createMock(GroupRepositoryInterface::class), $repository);

        self::assertTrue($this->eventsRaised[GroupKeeper::EVENT_BEFORE_LEAVING]);
        self::assertTrue($this->eventsRaised[GroupKeeper::EVENT_AFTER_LEAVING]);

        Event::off(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_LEAVING, $beforeHandler);
        Event::off(GroupKeeper::class, GroupKeeper::EVENT_AFTER_LEAVING, $afterHandler);
    }

    public function testEditShouldOnlyTriggerBeforeEventWhenRepositoryNotJoinedGroupBefore(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[GroupKeeper::EVENT_BEFORE_LEAVING] = true;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_LEAVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[GroupKeeper::EVENT_AFTER_LEAVING] = true;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_AFTER_LEAVING, $afterHandler);

        $repository = $this->createMock(RepositoryInterface::class);
        $repository->method('hasGroups')->willReturn(false);
        $this->service->leave($this->createMock(GroupRepositoryInterface::class), $repository);

        self::assertTrue($this->eventsRaised[GroupKeeper::EVENT_BEFORE_LEAVING]);
        self::assertArrayNotHasKey(GroupKeeper::EVENT_AFTER_LEAVING, $this->eventsRaised);

        Event::off(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_LEAVING, $beforeHandler);
        Event::off(GroupKeeper::class, GroupKeeper::EVENT_AFTER_LEAVING, $afterHandler);
    }

    public function testEditShouldOnlyTriggerBeforeEventWhenRemovingRepositoryErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[GroupKeeper::EVENT_BEFORE_LEAVING] = true;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_BEFORE_LEAVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[GroupKeeper::EVENT_AFTER_LEAVING] = true;
        };
        Event::on(GroupKeeper::class, GroupKeeper::EVENT_AFTER_LEAVING, $afterHandler);

        $repository = $this->createMock(RepositoryInterface::class);
        $repository->method('hasGroups')->willReturn(true);
        $repository->method('leave')->willReturn(false);
        $this->service->leave($this->createMock(GroupRepositoryInterface::class), $repository);

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
