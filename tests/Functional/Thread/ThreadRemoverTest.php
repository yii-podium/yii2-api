<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Thread;

use Podium\Api\Events\RemoveEvent;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\Services\Thread\ThreadRemover;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class ThreadRemoverTest extends AppTestCase
{
    private ThreadRemover $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ThreadRemover();
        $this->eventsRaised = [];
    }

    public function testRemoveShouldTriggerBeforeAndAfterEventsWhenRemovingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[ThreadRemover::EVENT_BEFORE_REMOVING] = $event instanceof RemoveEvent;
        };
        Event::on(ThreadRemover::class, ThreadRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[ThreadRemover::EVENT_AFTER_REMOVING] = true;
        };
        Event::on(ThreadRemover::class, ThreadRemover::EVENT_AFTER_REMOVING, $afterHandler);

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('delete')->willReturn(true);
        $thread->method('isArchived')->willReturn(true);
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('updateCounters')->willReturn(true);
        $thread->method('getParent')->willReturn($forum);
        $this->service->remove($thread);

        self::assertTrue($this->eventsRaised[ThreadRemover::EVENT_BEFORE_REMOVING]);
        self::assertTrue($this->eventsRaised[ThreadRemover::EVENT_AFTER_REMOVING]);

        Event::off(ThreadRemover::class, ThreadRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        Event::off(ThreadRemover::class, ThreadRemover::EVENT_AFTER_REMOVING, $afterHandler);
    }

    public function testRemoveShouldOnlyTriggerBeforeEventWhenRemovingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[ThreadRemover::EVENT_BEFORE_REMOVING] = true;
        };
        Event::on(ThreadRemover::class, ThreadRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[ThreadRemover::EVENT_AFTER_REMOVING] = true;
        };
        Event::on(ThreadRemover::class, ThreadRemover::EVENT_AFTER_REMOVING, $afterHandler);

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('delete')->willReturn(false);
        $thread->method('isArchived')->willReturn(true);
        $this->service->remove($thread);

        self::assertTrue($this->eventsRaised[ThreadRemover::EVENT_BEFORE_REMOVING]);
        self::assertArrayNotHasKey(ThreadRemover::EVENT_AFTER_REMOVING, $this->eventsRaised);

        Event::off(ThreadRemover::class, ThreadRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        Event::off(ThreadRemover::class, ThreadRemover::EVENT_AFTER_REMOVING, $afterHandler);
    }

    public function testRemoveShouldOnlyTriggerBeforeEventWhenThreadIsNotArchived(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[ThreadRemover::EVENT_BEFORE_REMOVING] = true;
        };
        Event::on(ThreadRemover::class, ThreadRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[ThreadRemover::EVENT_AFTER_REMOVING] = true;
        };
        Event::on(ThreadRemover::class, ThreadRemover::EVENT_AFTER_REMOVING, $afterHandler);

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('delete')->willReturn(true);
        $thread->method('isArchived')->willReturn(false);
        $this->service->remove($thread);

        self::assertTrue($this->eventsRaised[ThreadRemover::EVENT_BEFORE_REMOVING]);
        self::assertArrayNotHasKey(ThreadRemover::EVENT_AFTER_REMOVING, $this->eventsRaised);

        Event::off(ThreadRemover::class, ThreadRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        Event::off(ThreadRemover::class, ThreadRemover::EVENT_AFTER_REMOVING, $afterHandler);
    }

    public function testRemoveShouldReturnErrorWhenEventPreventsRemoving(): void
    {
        $handler = static function (RemoveEvent $event) {
            $event->canRemove = false;
        };
        Event::on(ThreadRemover::class, ThreadRemover::EVENT_BEFORE_REMOVING, $handler);

        $result = $this->service->remove($this->createMock(ThreadRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(ThreadRemover::class, ThreadRemover::EVENT_BEFORE_REMOVING, $handler);
    }
}
