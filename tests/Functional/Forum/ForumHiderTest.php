<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Forum;

use Podium\Api\Events\HideEvent;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Services\Forum\ForumHider;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class ForumHiderTest extends AppTestCase
{
    private ForumHider $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ForumHider();
        $this->eventsRaised = [];
    }

    public function testHideShouldTriggerBeforeAndAfterEventsWhenHidingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[ForumHider::EVENT_BEFORE_HIDING] = $event instanceof HideEvent;
        };
        Event::on(ForumHider::class, ForumHider::EVENT_BEFORE_HIDING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[ForumHider::EVENT_AFTER_HIDING] = $event instanceof HideEvent
                && 99 === $event->repository->getId();
        };
        Event::on(ForumHider::class, ForumHider::EVENT_AFTER_HIDING, $afterHandler);

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('isHidden')->willReturn(false);
        $forum->method('hide')->willReturn(true);
        $forum->method('getId')->willReturn(99);
        $this->service->hide($forum);

        self::assertTrue($this->eventsRaised[ForumHider::EVENT_BEFORE_HIDING]);
        self::assertTrue($this->eventsRaised[ForumHider::EVENT_AFTER_HIDING]);

        Event::off(ForumHider::class, ForumHider::EVENT_BEFORE_HIDING, $beforeHandler);
        Event::off(ForumHider::class, ForumHider::EVENT_AFTER_HIDING, $afterHandler);
    }

    public function testHideShouldOnlyTriggerBeforeEventWhenHidingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[ForumHider::EVENT_BEFORE_HIDING] = true;
        };
        Event::on(ForumHider::class, ForumHider::EVENT_BEFORE_HIDING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[ForumHider::EVENT_AFTER_HIDING] = true;
        };
        Event::on(ForumHider::class, ForumHider::EVENT_AFTER_HIDING, $afterHandler);

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('isHidden')->willReturn(false);
        $forum->method('hide')->willReturn(false);
        $this->service->hide($forum);

        self::assertTrue($this->eventsRaised[ForumHider::EVENT_BEFORE_HIDING]);
        self::assertArrayNotHasKey(ForumHider::EVENT_AFTER_HIDING, $this->eventsRaised);

        Event::off(ForumHider::class, ForumHider::EVENT_BEFORE_HIDING, $beforeHandler);
        Event::off(ForumHider::class, ForumHider::EVENT_AFTER_HIDING, $afterHandler);
    }

    public function testHideShouldOnlyTriggerBeforeEventWhenForumIsAlreadyHidden(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[ForumHider::EVENT_BEFORE_HIDING] = true;
        };
        Event::on(ForumHider::class, ForumHider::EVENT_BEFORE_HIDING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[ForumHider::EVENT_AFTER_HIDING] = true;
        };
        Event::on(ForumHider::class, ForumHider::EVENT_AFTER_HIDING, $afterHandler);

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('isHidden')->willReturn(true);
        $this->service->hide($forum);

        self::assertTrue($this->eventsRaised[ForumHider::EVENT_BEFORE_HIDING]);
        self::assertArrayNotHasKey(ForumHider::EVENT_AFTER_HIDING, $this->eventsRaised);

        Event::off(ForumHider::class, ForumHider::EVENT_BEFORE_HIDING, $beforeHandler);
        Event::off(ForumHider::class, ForumHider::EVENT_AFTER_HIDING, $afterHandler);
    }

    public function testHideShouldReturnErrorWhenEventPreventsHiding(): void
    {
        $handler = static function (HideEvent $event) {
            $event->canHide = false;
        };
        Event::on(ForumHider::class, ForumHider::EVENT_BEFORE_HIDING, $handler);

        $result = $this->service->hide($this->createMock(ForumRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(ForumHider::class, ForumHider::EVENT_BEFORE_HIDING, $handler);
    }

    public function testRevealShouldTriggerBeforeAndAfterEventsWhenRevivingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[ForumHider::EVENT_BEFORE_REVEALING] = $event instanceof HideEvent;
        };
        Event::on(ForumHider::class, ForumHider::EVENT_BEFORE_REVEALING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[ForumHider::EVENT_AFTER_REVEALING] = $event instanceof HideEvent
                && 101 === $event->repository->getId();
        };
        Event::on(ForumHider::class, ForumHider::EVENT_AFTER_REVEALING, $afterHandler);

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('isHidden')->willReturn(true);
        $forum->method('reveal')->willReturn(true);
        $forum->method('getId')->willReturn(101);
        $this->service->reveal($forum);

        self::assertTrue($this->eventsRaised[ForumHider::EVENT_BEFORE_REVEALING]);
        self::assertTrue($this->eventsRaised[ForumHider::EVENT_AFTER_REVEALING]);

        Event::off(ForumHider::class, ForumHider::EVENT_BEFORE_REVEALING, $beforeHandler);
        Event::off(ForumHider::class, ForumHider::EVENT_AFTER_REVEALING, $afterHandler);
    }

    public function testRevealShouldOnlyTriggerBeforeEventWhenRevealingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[ForumHider::EVENT_BEFORE_REVEALING] = true;
        };
        Event::on(ForumHider::class, ForumHider::EVENT_BEFORE_REVEALING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[ForumHider::EVENT_AFTER_REVEALING] = true;
        };
        Event::on(ForumHider::class, ForumHider::EVENT_AFTER_REVEALING, $afterHandler);

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('isHidden')->willReturn(true);
        $forum->method('reveal')->willReturn(false);
        $this->service->reveal($forum);

        self::assertTrue($this->eventsRaised[ForumHider::EVENT_BEFORE_REVEALING]);
        self::assertArrayNotHasKey(ForumHider::EVENT_AFTER_REVEALING, $this->eventsRaised);

        Event::off(ForumHider::class, ForumHider::EVENT_BEFORE_REVEALING, $beforeHandler);
        Event::off(ForumHider::class, ForumHider::EVENT_AFTER_REVEALING, $afterHandler);
    }

    public function testRevealShouldOnlyTriggerBeforeEventWhenForumIsNotHidden(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[ForumHider::EVENT_BEFORE_REVEALING] = true;
        };
        Event::on(ForumHider::class, ForumHider::EVENT_BEFORE_REVEALING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[ForumHider::EVENT_AFTER_REVEALING] = true;
        };
        Event::on(ForumHider::class, ForumHider::EVENT_AFTER_REVEALING, $afterHandler);

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('isHidden')->willReturn(false);
        $this->service->reveal($forum);

        self::assertTrue($this->eventsRaised[ForumHider::EVENT_BEFORE_REVEALING]);
        self::assertArrayNotHasKey(ForumHider::EVENT_AFTER_REVEALING, $this->eventsRaised);

        Event::off(ForumHider::class, ForumHider::EVENT_BEFORE_REVEALING, $beforeHandler);
        Event::off(ForumHider::class, ForumHider::EVENT_AFTER_REVEALING, $afterHandler);
    }

    public function testRevealShouldReturnErrorWhenEventPreventsRevealing(): void
    {
        $handler = static function (HideEvent $event) {
            $event->canReveal = false;
        };
        Event::on(ForumHider::class, ForumHider::EVENT_BEFORE_REVEALING, $handler);

        $result = $this->service->reveal($this->createMock(ForumRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(ForumHider::class, ForumHider::EVENT_BEFORE_REVEALING, $handler);
    }
}
