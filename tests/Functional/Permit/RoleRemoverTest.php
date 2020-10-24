<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Permit;

use Podium\Api\Events\RemoveEvent;
use Podium\Api\Interfaces\RoleRepositoryInterface;
use Podium\Api\Services\Permit\RoleRemover;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class RoleRemoverTest extends AppTestCase
{
    private RoleRemover $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RoleRemover();
        $this->eventsRaised = [];
    }

    public function testRemoveShouldTriggerBeforeAndAfterEventsWhenRemovingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[RoleRemover::EVENT_BEFORE_REMOVING] = $event instanceof RemoveEvent;
        };
        Event::on(RoleRemover::class, RoleRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[RoleRemover::EVENT_AFTER_REMOVING] = true;
        };
        Event::on(RoleRemover::class, RoleRemover::EVENT_AFTER_REMOVING, $afterHandler);

        $role = $this->createMock(RoleRepositoryInterface::class);
        $role->method('delete')->willReturn(true);
        $this->service->remove($role);

        self::assertTrue($this->eventsRaised[RoleRemover::EVENT_BEFORE_REMOVING]);
        self::assertTrue($this->eventsRaised[RoleRemover::EVENT_AFTER_REMOVING]);

        Event::off(RoleRemover::class, RoleRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        Event::off(RoleRemover::class, RoleRemover::EVENT_AFTER_REMOVING, $afterHandler);
    }

    public function testRemoveShouldOnlyTriggerBeforeEventWhenRemovingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[RoleRemover::EVENT_BEFORE_REMOVING] = true;
        };
        Event::on(RoleRemover::class, RoleRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[RoleRemover::EVENT_AFTER_REMOVING] = true;
        };
        Event::on(RoleRemover::class, RoleRemover::EVENT_AFTER_REMOVING, $afterHandler);

        $role = $this->createMock(RoleRepositoryInterface::class);
        $role->method('delete')->willReturn(false);
        $this->service->remove($role);

        self::assertTrue($this->eventsRaised[RoleRemover::EVENT_BEFORE_REMOVING]);
        self::assertArrayNotHasKey(RoleRemover::EVENT_AFTER_REMOVING, $this->eventsRaised);

        Event::off(RoleRemover::class, RoleRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        Event::off(RoleRemover::class, RoleRemover::EVENT_AFTER_REMOVING, $afterHandler);
    }

    public function testRemoveShouldReturnErrorWhenEventPreventsRemoving(): void
    {
        $handler = static function (RemoveEvent $event) {
            $event->canRemove = false;
        };
        Event::on(RoleRemover::class, RoleRemover::EVENT_BEFORE_REMOVING, $handler);

        $result = $this->service->remove($this->createMock(RoleRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(RoleRemover::class, RoleRemover::EVENT_BEFORE_REMOVING, $handler);
    }
}
