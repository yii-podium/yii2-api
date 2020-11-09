<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Poll;

use Podium\Api\Events\BuildEvent;
use Podium\Api\Interfaces\PollPostRepositoryInterface;
use Podium\Api\Services\Poll\PollBuilder;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class PollBuilderTest extends AppTestCase
{
    private PollBuilder $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PollBuilder();
        $this->eventsRaised = [];
    }

    public function testCreateShouldTriggerBeforeAndAfterEventsWhenCreatingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[PollBuilder::EVENT_BEFORE_CREATING] = $event instanceof BuildEvent;
        };
        Event::on(PollBuilder::class, PollBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[PollBuilder::EVENT_AFTER_CREATING] = $event instanceof BuildEvent
                && 9 === $event->repository->getId();
        };
        Event::on(PollBuilder::class, PollBuilder::EVENT_AFTER_CREATING, $afterHandler);

        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('addPoll')->willReturn(true);
        $post->method('getId')->willReturn(9);

        $this->service->create($post, []);

        self::assertTrue($this->eventsRaised[PollBuilder::EVENT_BEFORE_CREATING]);
        self::assertTrue($this->eventsRaised[PollBuilder::EVENT_AFTER_CREATING]);

        Event::off(PollBuilder::class, PollBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        Event::off(PollBuilder::class, PollBuilder::EVENT_AFTER_CREATING, $afterHandler);
    }

    public function testCreateShouldOnlyTriggerBeforeEventWhenCreatingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[PollBuilder::EVENT_BEFORE_CREATING] = true;
        };
        Event::on(PollBuilder::class, PollBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[PollBuilder::EVENT_AFTER_CREATING] = true;
        };
        Event::on(PollBuilder::class, PollBuilder::EVENT_AFTER_CREATING, $afterHandler);

        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('addPoll')->willReturn(false);

        $this->service->create($post, []);

        self::assertTrue($this->eventsRaised[PollBuilder::EVENT_BEFORE_CREATING]);
        self::assertArrayNotHasKey(PollBuilder::EVENT_AFTER_CREATING, $this->eventsRaised);

        Event::off(PollBuilder::class, PollBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        Event::off(PollBuilder::class, PollBuilder::EVENT_AFTER_CREATING, $afterHandler);
    }

    public function testCreateShouldReturnErrorWhenEventPreventsCreating(): void
    {
        $handler = static function (BuildEvent $event) {
            $event->canCreate = false;
        };
        Event::on(PollBuilder::class, PollBuilder::EVENT_BEFORE_CREATING, $handler);

        $result = $this->service->create($this->createMock(PollPostRepositoryInterface::class), []);
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(PollBuilder::class, PollBuilder::EVENT_BEFORE_CREATING, $handler);
    }

    public function testEditShouldTriggerBeforeAndAfterEventsWhenEditingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[PollBuilder::EVENT_BEFORE_EDITING] = $event instanceof BuildEvent;
        };
        Event::on(PollBuilder::class, PollBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[PollBuilder::EVENT_AFTER_EDITING] = $event instanceof BuildEvent
                && 9 === $event->repository->getId();
        };
        Event::on(PollBuilder::class, PollBuilder::EVENT_AFTER_EDITING, $afterHandler);

        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('editPoll')->willReturn(true);
        $post->method('getId')->willReturn(9);
        $this->service->edit($post);

        self::assertTrue($this->eventsRaised[PollBuilder::EVENT_BEFORE_EDITING]);
        self::assertTrue($this->eventsRaised[PollBuilder::EVENT_AFTER_EDITING]);

        Event::off(PollBuilder::class, PollBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        Event::off(PollBuilder::class, PollBuilder::EVENT_AFTER_EDITING, $afterHandler);
    }

    public function testEditShouldOnlyTriggerBeforeEventWhenEditingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[PollBuilder::EVENT_BEFORE_EDITING] = true;
        };
        Event::on(PollBuilder::class, PollBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[PollBuilder::EVENT_AFTER_EDITING] = true;
        };
        Event::on(PollBuilder::class, PollBuilder::EVENT_AFTER_EDITING, $afterHandler);

        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('editPoll')->willReturn(false);
        $this->service->edit($post);

        self::assertTrue($this->eventsRaised[PollBuilder::EVENT_BEFORE_EDITING]);
        self::assertArrayNotHasKey(PollBuilder::EVENT_AFTER_EDITING, $this->eventsRaised);

        Event::off(PollBuilder::class, PollBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        Event::off(PollBuilder::class, PollBuilder::EVENT_AFTER_EDITING, $afterHandler);
    }

    public function testEditShouldReturnErrorWhenEventPreventsEditing(): void
    {
        $handler = static function (BuildEvent $event) {
            $event->canEdit = false;
        };
        Event::on(PollBuilder::class, PollBuilder::EVENT_BEFORE_EDITING, $handler);

        $result = $this->service->edit($this->createMock(PollPostRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(PollBuilder::class, PollBuilder::EVENT_BEFORE_EDITING, $handler);
    }
}
