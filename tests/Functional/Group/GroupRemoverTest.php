<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Group;

use Podium\Api\Events\RemoveEvent;
use Podium\Api\Interfaces\GroupRepositoryInterface;
use Podium\Api\Services\Group\GroupRemover;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class GroupRemoverTest extends AppTestCase
{
    private GroupRemover $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GroupRemover();
        $this->eventsRaised = [];
    }

    public function testRemoveShouldTriggerBeforeAndAfterEventsWhenRemovingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[GroupRemover::EVENT_BEFORE_REMOVING] = $event instanceof RemoveEvent;
        };
        Event::on(GroupRemover::class, GroupRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[GroupRemover::EVENT_AFTER_REMOVING] = true;
        };
        Event::on(GroupRemover::class, GroupRemover::EVENT_AFTER_REMOVING, $afterHandler);

        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('delete')->willReturn(true);
        $this->service->remove($group);

        self::assertTrue($this->eventsRaised[GroupRemover::EVENT_BEFORE_REMOVING]);
        self::assertTrue($this->eventsRaised[GroupRemover::EVENT_AFTER_REMOVING]);

        Event::off(GroupRemover::class, GroupRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        Event::off(GroupRemover::class, GroupRemover::EVENT_AFTER_REMOVING, $afterHandler);
    }

    public function testRemoveShouldOnlyTriggerBeforeEventWhenRemovingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[GroupRemover::EVENT_BEFORE_REMOVING] = true;
        };
        Event::on(GroupRemover::class, GroupRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[GroupRemover::EVENT_AFTER_REMOVING] = true;
        };
        Event::on(GroupRemover::class, GroupRemover::EVENT_AFTER_REMOVING, $afterHandler);

        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('delete')->willReturn(false);
        $this->service->remove($group);

        self::assertTrue($this->eventsRaised[GroupRemover::EVENT_BEFORE_REMOVING]);
        self::assertArrayNotHasKey(GroupRemover::EVENT_AFTER_REMOVING, $this->eventsRaised);

        Event::off(GroupRemover::class, GroupRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        Event::off(GroupRemover::class, GroupRemover::EVENT_AFTER_REMOVING, $afterHandler);
    }

    public function testRemoveShouldReturnErrorWhenEventPreventsRemoving(): void
    {
        $handler = static function (RemoveEvent $event) {
            $event->canRemove = false;
        };
        Event::on(GroupRemover::class, GroupRemover::EVENT_BEFORE_REMOVING, $handler);

        $result = $this->service->remove($this->createMock(GroupRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(GroupRemover::class, GroupRemover::EVENT_BEFORE_REMOVING, $handler);
    }
}
