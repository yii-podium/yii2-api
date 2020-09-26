<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Group;

use PHPUnit\Framework\TestCase;
use Podium\Api\Events\BuildEvent;
use Podium\Api\Interfaces\GroupRepositoryInterface;
use Podium\Api\Services\Group\GroupBuilder;
use yii\base\Event;

class GroupBuilderTest extends TestCase
{
    private GroupBuilder $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        $this->service = new GroupBuilder();
        $this->eventsRaised = [];
    }

    public function testCreateShouldTriggerBeforeAndAfterEventsWhenCreatingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[GroupBuilder::EVENT_BEFORE_CREATING] = $event instanceof BuildEvent;
        };
        Event::on(GroupBuilder::class, GroupBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[GroupBuilder::EVENT_AFTER_CREATING] = $event instanceof BuildEvent
                && 99 === $event->repository->getId();
        };
        Event::on(GroupBuilder::class, GroupBuilder::EVENT_AFTER_CREATING, $afterHandler);

        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('create')->willReturn(true);
        $group->method('getId')->willReturn(99);
        $this->service->create($group);

        self::assertTrue($this->eventsRaised[GroupBuilder::EVENT_BEFORE_CREATING]);
        self::assertTrue($this->eventsRaised[GroupBuilder::EVENT_AFTER_CREATING]);

        Event::off(GroupBuilder::class, GroupBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        Event::off(GroupBuilder::class, GroupBuilder::EVENT_AFTER_CREATING, $afterHandler);
    }

    public function testCreateShouldOnlyTriggerBeforeEventWhenCreatingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[GroupBuilder::EVENT_BEFORE_CREATING] = true;
        };
        Event::on(GroupBuilder::class, GroupBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[GroupBuilder::EVENT_AFTER_CREATING] = true;
        };
        Event::on(GroupBuilder::class, GroupBuilder::EVENT_AFTER_CREATING, $afterHandler);

        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('create')->willReturn(false);
        $this->service->create($group);

        self::assertTrue($this->eventsRaised[GroupBuilder::EVENT_BEFORE_CREATING]);
        self::assertArrayNotHasKey(GroupBuilder::EVENT_AFTER_CREATING, $this->eventsRaised);

        Event::off(GroupBuilder::class, GroupBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        Event::off(GroupBuilder::class, GroupBuilder::EVENT_AFTER_CREATING, $afterHandler);
    }

    public function testCreateShouldReturnErrorWhenEventPreventsCreating(): void
    {
        $handler = static function (BuildEvent $event) {
            $event->canCreate = false;
        };
        Event::on(GroupBuilder::class, GroupBuilder::EVENT_BEFORE_CREATING, $handler);

        $result = $this->service->create($this->createMock(GroupRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(GroupBuilder::class, GroupBuilder::EVENT_BEFORE_CREATING, $handler);
    }

    public function testEditShouldTriggerBeforeAndAfterEventsWhenEditingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[GroupBuilder::EVENT_BEFORE_EDITING] = $event instanceof BuildEvent;
        };
        Event::on(GroupBuilder::class, GroupBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[GroupBuilder::EVENT_AFTER_EDITING] = $event instanceof BuildEvent
                && 101 === $event->repository->getId();
        };
        Event::on(GroupBuilder::class, GroupBuilder::EVENT_AFTER_EDITING, $afterHandler);

        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('edit')->willReturn(true);
        $group->method('getId')->willReturn(101);
        $this->service->edit($group);

        self::assertTrue($this->eventsRaised[GroupBuilder::EVENT_BEFORE_EDITING]);
        self::assertTrue($this->eventsRaised[GroupBuilder::EVENT_AFTER_EDITING]);

        Event::off(GroupBuilder::class, GroupBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        Event::off(GroupBuilder::class, GroupBuilder::EVENT_AFTER_EDITING, $afterHandler);
    }

    public function testEditShouldOnlyTriggerBeforeEventWhenEditingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[GroupBuilder::EVENT_BEFORE_EDITING] = true;
        };
        Event::on(GroupBuilder::class, GroupBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[GroupBuilder::EVENT_AFTER_EDITING] = true;
        };
        Event::on(GroupBuilder::class, GroupBuilder::EVENT_AFTER_EDITING, $afterHandler);

        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('edit')->willReturn(false);
        $this->service->edit($group);

        self::assertTrue($this->eventsRaised[GroupBuilder::EVENT_BEFORE_EDITING]);
        self::assertArrayNotHasKey(GroupBuilder::EVENT_AFTER_EDITING, $this->eventsRaised);

        Event::off(GroupBuilder::class, GroupBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        Event::off(GroupBuilder::class, GroupBuilder::EVENT_AFTER_EDITING, $afterHandler);
    }

    public function testEditShouldReturnErrorWhenEventPreventsEditing(): void
    {
        $handler = static function (BuildEvent $event) {
            $event->canEdit = false;
        };
        Event::on(GroupBuilder::class, GroupBuilder::EVENT_BEFORE_EDITING, $handler);

        $result = $this->service->edit($this->createMock(GroupRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(GroupBuilder::class, GroupBuilder::EVENT_BEFORE_EDITING, $handler);
    }
}
