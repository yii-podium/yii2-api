<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Rank;

use Podium\Api\Events\BuildEvent;
use Podium\Api\Interfaces\RankRepositoryInterface;
use Podium\Api\Services\Rank\RankBuilder;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class RankBuilderTest extends AppTestCase
{
    private RankBuilder $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RankBuilder();
        $this->eventsRaised = [];
    }

    public function testCreateShouldTriggerBeforeAndAfterEventsWhenCreatingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[RankBuilder::EVENT_BEFORE_CREATING] = $event instanceof BuildEvent;
        };
        Event::on(RankBuilder::class, RankBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[RankBuilder::EVENT_AFTER_CREATING] = $event instanceof BuildEvent
                && 99 === $event->repository->getId();
        };
        Event::on(RankBuilder::class, RankBuilder::EVENT_AFTER_CREATING, $afterHandler);

        $rank = $this->createMock(RankRepositoryInterface::class);
        $rank->method('create')->willReturn(true);
        $rank->method('getId')->willReturn(99);
        $this->service->create($rank);

        self::assertTrue($this->eventsRaised[RankBuilder::EVENT_BEFORE_CREATING]);
        self::assertTrue($this->eventsRaised[RankBuilder::EVENT_AFTER_CREATING]);

        Event::off(RankBuilder::class, RankBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        Event::off(RankBuilder::class, RankBuilder::EVENT_AFTER_CREATING, $afterHandler);
    }

    public function testCreateShouldOnlyTriggerBeforeEventWhenCreatingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[RankBuilder::EVENT_BEFORE_CREATING] = true;
        };
        Event::on(RankBuilder::class, RankBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[RankBuilder::EVENT_AFTER_CREATING] = true;
        };
        Event::on(RankBuilder::class, RankBuilder::EVENT_AFTER_CREATING, $afterHandler);

        $rank = $this->createMock(RankRepositoryInterface::class);
        $rank->method('create')->willReturn(false);
        $this->service->create($rank);

        self::assertTrue($this->eventsRaised[RankBuilder::EVENT_BEFORE_CREATING]);
        self::assertArrayNotHasKey(RankBuilder::EVENT_AFTER_CREATING, $this->eventsRaised);

        Event::off(RankBuilder::class, RankBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        Event::off(RankBuilder::class, RankBuilder::EVENT_AFTER_CREATING, $afterHandler);
    }

    public function testCreateShouldReturnErrorWhenEventPreventsCreating(): void
    {
        $handler = static function (BuildEvent $event) {
            $event->canCreate = false;
        };
        Event::on(RankBuilder::class, RankBuilder::EVENT_BEFORE_CREATING, $handler);

        $result = $this->service->create($this->createMock(RankRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(RankBuilder::class, RankBuilder::EVENT_BEFORE_CREATING, $handler);
    }

    public function testEditShouldTriggerBeforeAndAfterEventsWhenEditingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[RankBuilder::EVENT_BEFORE_EDITING] = $event instanceof BuildEvent;
        };
        Event::on(RankBuilder::class, RankBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[RankBuilder::EVENT_AFTER_EDITING] = $event instanceof BuildEvent
                && 101 === $event->repository->getId();
        };
        Event::on(RankBuilder::class, RankBuilder::EVENT_AFTER_EDITING, $afterHandler);

        $rank = $this->createMock(RankRepositoryInterface::class);
        $rank->method('edit')->willReturn(true);
        $rank->method('getId')->willReturn(101);
        $this->service->edit($rank);

        self::assertTrue($this->eventsRaised[RankBuilder::EVENT_BEFORE_EDITING]);
        self::assertTrue($this->eventsRaised[RankBuilder::EVENT_AFTER_EDITING]);

        Event::off(RankBuilder::class, RankBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        Event::off(RankBuilder::class, RankBuilder::EVENT_AFTER_EDITING, $afterHandler);
    }

    public function testEditShouldOnlyTriggerBeforeEventWhenEditingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[RankBuilder::EVENT_BEFORE_EDITING] = true;
        };
        Event::on(RankBuilder::class, RankBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[RankBuilder::EVENT_AFTER_EDITING] = true;
        };
        Event::on(RankBuilder::class, RankBuilder::EVENT_AFTER_EDITING, $afterHandler);

        $rank = $this->createMock(RankRepositoryInterface::class);
        $rank->method('edit')->willReturn(false);
        $this->service->edit($rank);

        self::assertTrue($this->eventsRaised[RankBuilder::EVENT_BEFORE_EDITING]);
        self::assertArrayNotHasKey(RankBuilder::EVENT_AFTER_EDITING, $this->eventsRaised);

        Event::off(RankBuilder::class, RankBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        Event::off(RankBuilder::class, RankBuilder::EVENT_AFTER_EDITING, $afterHandler);
    }

    public function testEditShouldReturnErrorWhenEventPreventsEditing(): void
    {
        $handler = static function (BuildEvent $event) {
            $event->canEdit = false;
        };
        Event::on(RankBuilder::class, RankBuilder::EVENT_BEFORE_EDITING, $handler);

        $result = $this->service->edit($this->createMock(RankRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(RankBuilder::class, RankBuilder::EVENT_BEFORE_EDITING, $handler);
    }
}
