<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Permit;

use Podium\Api\Events\BuildEvent;
use Podium\Api\Interfaces\RoleRepositoryInterface;
use Podium\Api\Services\Permit\RoleBuilder;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class RoleBuilderTest extends AppTestCase
{
    private RoleBuilder $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RoleBuilder();
        $this->eventsRaised = [];
    }

    public function testCreateShouldTriggerBeforeAndAfterEventsWhenCreatingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[RoleBuilder::EVENT_BEFORE_CREATING] = $event instanceof BuildEvent;
        };
        Event::on(RoleBuilder::class, RoleBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[RoleBuilder::EVENT_AFTER_CREATING] = $event instanceof BuildEvent
                && 99 === $event->repository->getId();
        };
        Event::on(RoleBuilder::class, RoleBuilder::EVENT_AFTER_CREATING, $afterHandler);

        $role = $this->createMock(RoleRepositoryInterface::class);
        $role->method('create')->willReturn(true);
        $role->method('getId')->willReturn(99);
        $this->service->create($role);

        self::assertTrue($this->eventsRaised[RoleBuilder::EVENT_BEFORE_CREATING]);
        self::assertTrue($this->eventsRaised[RoleBuilder::EVENT_AFTER_CREATING]);

        Event::off(RoleBuilder::class, RoleBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        Event::off(RoleBuilder::class, RoleBuilder::EVENT_AFTER_CREATING, $afterHandler);
    }

    public function testCreateShouldOnlyTriggerBeforeEventWhenCreatingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[RoleBuilder::EVENT_BEFORE_CREATING] = true;
        };
        Event::on(RoleBuilder::class, RoleBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[RoleBuilder::EVENT_AFTER_CREATING] = true;
        };
        Event::on(RoleBuilder::class, RoleBuilder::EVENT_AFTER_CREATING, $afterHandler);

        $role = $this->createMock(RoleRepositoryInterface::class);
        $role->method('create')->willReturn(false);
        $this->service->create($role);

        self::assertTrue($this->eventsRaised[RoleBuilder::EVENT_BEFORE_CREATING]);
        self::assertArrayNotHasKey(RoleBuilder::EVENT_AFTER_CREATING, $this->eventsRaised);

        Event::off(RoleBuilder::class, RoleBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        Event::off(RoleBuilder::class, RoleBuilder::EVENT_AFTER_CREATING, $afterHandler);
    }

    public function testCreateShouldReturnErrorWhenEventPreventsCreating(): void
    {
        $handler = static function (BuildEvent $event) {
            $event->canCreate = false;
        };
        Event::on(RoleBuilder::class, RoleBuilder::EVENT_BEFORE_CREATING, $handler);

        $result = $this->service->create($this->createMock(RoleRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(RoleBuilder::class, RoleBuilder::EVENT_BEFORE_CREATING, $handler);
    }

    public function testEditShouldTriggerBeforeAndAfterEventsWhenEditingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[RoleBuilder::EVENT_BEFORE_EDITING] = $event instanceof BuildEvent;
        };
        Event::on(RoleBuilder::class, RoleBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[RoleBuilder::EVENT_AFTER_EDITING] = $event instanceof BuildEvent
                && 101 === $event->repository->getId();
        };
        Event::on(RoleBuilder::class, RoleBuilder::EVENT_AFTER_EDITING, $afterHandler);

        $role = $this->createMock(RoleRepositoryInterface::class);
        $role->method('edit')->willReturn(true);
        $role->method('getId')->willReturn(101);
        $this->service->edit($role);

        self::assertTrue($this->eventsRaised[RoleBuilder::EVENT_BEFORE_EDITING]);
        self::assertTrue($this->eventsRaised[RoleBuilder::EVENT_AFTER_EDITING]);

        Event::off(RoleBuilder::class, RoleBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        Event::off(RoleBuilder::class, RoleBuilder::EVENT_AFTER_EDITING, $afterHandler);
    }

    public function testEditShouldOnlyTriggerBeforeEventWhenEditingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[RoleBuilder::EVENT_BEFORE_EDITING] = true;
        };
        Event::on(RoleBuilder::class, RoleBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[RoleBuilder::EVENT_AFTER_EDITING] = true;
        };
        Event::on(RoleBuilder::class, RoleBuilder::EVENT_AFTER_EDITING, $afterHandler);

        $role = $this->createMock(RoleRepositoryInterface::class);
        $role->method('edit')->willReturn(false);
        $this->service->edit($role);

        self::assertTrue($this->eventsRaised[RoleBuilder::EVENT_BEFORE_EDITING]);
        self::assertArrayNotHasKey(RoleBuilder::EVENT_AFTER_EDITING, $this->eventsRaised);

        Event::off(RoleBuilder::class, RoleBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        Event::off(RoleBuilder::class, RoleBuilder::EVENT_AFTER_EDITING, $afterHandler);
    }

    public function testEditShouldReturnErrorWhenEventPreventsEditing(): void
    {
        $handler = static function (BuildEvent $event) {
            $event->canEdit = false;
        };
        Event::on(RoleBuilder::class, RoleBuilder::EVENT_BEFORE_EDITING, $handler);

        $result = $this->service->edit($this->createMock(RoleRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(RoleBuilder::class, RoleBuilder::EVENT_BEFORE_EDITING, $handler);
    }
}
