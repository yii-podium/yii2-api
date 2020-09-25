<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Poll;

use Podium\Api\Events\BuildEvent;
use Podium\Api\Interfaces\PollPostRepositoryInterface;
use Podium\Api\Interfaces\PollRepositoryInterface;
use Podium\Api\Services\Poll\PollBuilder;
use Podium\Tests\AppTestCase;
use Yii;
use yii\base\Event;
use yii\db\Connection;
use yii\db\Transaction;

class PollBuilderTest extends AppTestCase
{
    private PollBuilder $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        $this->service = new PollBuilder();
        $this->eventsRaised = [];
        $connection = $this->createMock(Connection::class);
        $connection->method('beginTransaction')->willReturn($this->createMock(Transaction::class));
        Yii::$app->set('db', $connection);
    }

    public function testCreateShouldTriggerBeforeAndAfterEventsWhenCreatingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[PollBuilder::EVENT_BEFORE_CREATING] = $event instanceof BuildEvent;
        };
        Event::on(PollBuilder::class, PollBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[PollBuilder::EVENT_AFTER_CREATING] = $event instanceof BuildEvent
                && 99 === $event->repository->getId();
        };
        Event::on(PollBuilder::class, PollBuilder::EVENT_AFTER_CREATING, $afterHandler);

        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('create')->willReturn(true);
        $poll->method('getId')->willReturn(99);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);

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

        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('create')->willReturn(false);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);

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
                && 101 === $event->repository->getId();
        };
        Event::on(PollBuilder::class, PollBuilder::EVENT_AFTER_EDITING, $afterHandler);

        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('edit')->willReturn(true);
        $poll->method('getId')->willReturn(101);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
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

        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('edit')->willReturn(false);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
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
