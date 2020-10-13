<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Thread;

use Podium\Api\Events\HideEvent;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\Services\Thread\ThreadHider;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class ThreadHiderTest extends AppTestCase
{
    private ThreadHider $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ThreadHider();
        $this->eventsRaised = [];
    }

    public function testHideShouldTriggerBeforeAndAfterEventsWhenHidingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[ThreadHider::EVENT_BEFORE_HIDING] = $event instanceof HideEvent;
        };
        Event::on(ThreadHider::class, ThreadHider::EVENT_BEFORE_HIDING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[ThreadHider::EVENT_AFTER_HIDING] = $event instanceof HideEvent
                && 99 === $event->repository->getId();
        };
        Event::on(ThreadHider::class, ThreadHider::EVENT_AFTER_HIDING, $afterHandler);

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isHidden')->willReturn(false);
        $thread->method('hide')->willReturn(true);
        $thread->method('getId')->willReturn(99);
        $this->service->hide($thread);

        self::assertTrue($this->eventsRaised[ThreadHider::EVENT_BEFORE_HIDING]);
        self::assertTrue($this->eventsRaised[ThreadHider::EVENT_AFTER_HIDING]);

        Event::off(ThreadHider::class, ThreadHider::EVENT_BEFORE_HIDING, $beforeHandler);
        Event::off(ThreadHider::class, ThreadHider::EVENT_AFTER_HIDING, $afterHandler);
    }

    public function testHideShouldOnlyTriggerBeforeEventWhenHidingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[ThreadHider::EVENT_BEFORE_HIDING] = true;
        };
        Event::on(ThreadHider::class, ThreadHider::EVENT_BEFORE_HIDING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[ThreadHider::EVENT_AFTER_HIDING] = true;
        };
        Event::on(ThreadHider::class, ThreadHider::EVENT_AFTER_HIDING, $afterHandler);

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isHidden')->willReturn(false);
        $thread->method('hide')->willReturn(false);
        $this->service->hide($thread);

        self::assertTrue($this->eventsRaised[ThreadHider::EVENT_BEFORE_HIDING]);
        self::assertArrayNotHasKey(ThreadHider::EVENT_AFTER_HIDING, $this->eventsRaised);

        Event::off(ThreadHider::class, ThreadHider::EVENT_BEFORE_HIDING, $beforeHandler);
        Event::off(ThreadHider::class, ThreadHider::EVENT_AFTER_HIDING, $afterHandler);
    }

    public function testHideShouldOnlyTriggerBeforeEventWhenThreadIsAlreadyHidden(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[ThreadHider::EVENT_BEFORE_HIDING] = true;
        };
        Event::on(ThreadHider::class, ThreadHider::EVENT_BEFORE_HIDING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[ThreadHider::EVENT_AFTER_HIDING] = true;
        };
        Event::on(ThreadHider::class, ThreadHider::EVENT_AFTER_HIDING, $afterHandler);

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isHidden')->willReturn(true);
        $this->service->hide($thread);

        self::assertTrue($this->eventsRaised[ThreadHider::EVENT_BEFORE_HIDING]);
        self::assertArrayNotHasKey(ThreadHider::EVENT_AFTER_HIDING, $this->eventsRaised);

        Event::off(ThreadHider::class, ThreadHider::EVENT_BEFORE_HIDING, $beforeHandler);
        Event::off(ThreadHider::class, ThreadHider::EVENT_AFTER_HIDING, $afterHandler);
    }

    public function testHideShouldReturnErrorWhenEventPreventsHiding(): void
    {
        $handler = static function (HideEvent $event) {
            $event->canHide = false;
        };
        Event::on(ThreadHider::class, ThreadHider::EVENT_BEFORE_HIDING, $handler);

        $result = $this->service->hide($this->createMock(ThreadRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(ThreadHider::class, ThreadHider::EVENT_BEFORE_HIDING, $handler);
    }

    public function testRevealShouldTriggerBeforeAndAfterEventsWhenRevivingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[ThreadHider::EVENT_BEFORE_REVEALING] = $event instanceof HideEvent;
        };
        Event::on(ThreadHider::class, ThreadHider::EVENT_BEFORE_REVEALING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[ThreadHider::EVENT_AFTER_REVEALING] = $event instanceof HideEvent
                && 101 === $event->repository->getId();
        };
        Event::on(ThreadHider::class, ThreadHider::EVENT_AFTER_REVEALING, $afterHandler);

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isHidden')->willReturn(true);
        $thread->method('reveal')->willReturn(true);
        $thread->method('getId')->willReturn(101);
        $this->service->reveal($thread);

        self::assertTrue($this->eventsRaised[ThreadHider::EVENT_BEFORE_REVEALING]);
        self::assertTrue($this->eventsRaised[ThreadHider::EVENT_AFTER_REVEALING]);

        Event::off(ThreadHider::class, ThreadHider::EVENT_BEFORE_REVEALING, $beforeHandler);
        Event::off(ThreadHider::class, ThreadHider::EVENT_AFTER_REVEALING, $afterHandler);
    }

    public function testRevealShouldOnlyTriggerBeforeEventWhenRevealingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[ThreadHider::EVENT_BEFORE_REVEALING] = true;
        };
        Event::on(ThreadHider::class, ThreadHider::EVENT_BEFORE_REVEALING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[ThreadHider::EVENT_AFTER_REVEALING] = true;
        };
        Event::on(ThreadHider::class, ThreadHider::EVENT_AFTER_REVEALING, $afterHandler);

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isHidden')->willReturn(true);
        $thread->method('reveal')->willReturn(false);
        $this->service->reveal($thread);

        self::assertTrue($this->eventsRaised[ThreadHider::EVENT_BEFORE_REVEALING]);
        self::assertArrayNotHasKey(ThreadHider::EVENT_AFTER_REVEALING, $this->eventsRaised);

        Event::off(ThreadHider::class, ThreadHider::EVENT_BEFORE_REVEALING, $beforeHandler);
        Event::off(ThreadHider::class, ThreadHider::EVENT_AFTER_REVEALING, $afterHandler);
    }

    public function testRevealShouldOnlyTriggerBeforeEventWhenThreadIsNotHidden(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[ThreadHider::EVENT_BEFORE_REVEALING] = true;
        };
        Event::on(ThreadHider::class, ThreadHider::EVENT_BEFORE_REVEALING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[ThreadHider::EVENT_AFTER_REVEALING] = true;
        };
        Event::on(ThreadHider::class, ThreadHider::EVENT_AFTER_REVEALING, $afterHandler);

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isHidden')->willReturn(false);
        $this->service->reveal($thread);

        self::assertTrue($this->eventsRaised[ThreadHider::EVENT_BEFORE_REVEALING]);
        self::assertArrayNotHasKey(ThreadHider::EVENT_AFTER_REVEALING, $this->eventsRaised);

        Event::off(ThreadHider::class, ThreadHider::EVENT_BEFORE_REVEALING, $beforeHandler);
        Event::off(ThreadHider::class, ThreadHider::EVENT_AFTER_REVEALING, $afterHandler);
    }

    public function testRevealShouldReturnErrorWhenEventPreventsRevealing(): void
    {
        $handler = static function (HideEvent $event) {
            $event->canReveal = false;
        };
        Event::on(ThreadHider::class, ThreadHider::EVENT_BEFORE_REVEALING, $handler);

        $result = $this->service->reveal($this->createMock(ThreadRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(ThreadHider::class, ThreadHider::EVENT_BEFORE_REVEALING, $handler);
    }
}
