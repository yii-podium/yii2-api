<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Forum;

use Podium\Api\Events\RemoveEvent;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Services\Forum\ForumRemover;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class ForumRemoverTest extends AppTestCase
{
    private ForumRemover $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ForumRemover();
        $this->eventsRaised = [];
    }

    public function testRemoveShouldTriggerBeforeAndAfterEventsWhenRemovingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[ForumRemover::EVENT_BEFORE_REMOVING] = $event instanceof RemoveEvent;
        };
        Event::on(ForumRemover::class, ForumRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[ForumRemover::EVENT_AFTER_REMOVING] = true;
        };
        Event::on(ForumRemover::class, ForumRemover::EVENT_AFTER_REMOVING, $afterHandler);

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('delete')->willReturn(true);
        $forum->method('isArchived')->willReturn(true);
        $this->service->remove($forum);

        self::assertTrue($this->eventsRaised[ForumRemover::EVENT_BEFORE_REMOVING]);
        self::assertTrue($this->eventsRaised[ForumRemover::EVENT_AFTER_REMOVING]);

        Event::off(ForumRemover::class, ForumRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        Event::off(ForumRemover::class, ForumRemover::EVENT_AFTER_REMOVING, $afterHandler);
    }

    public function testRemoveShouldOnlyTriggerBeforeEventWhenRemovingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[ForumRemover::EVENT_BEFORE_REMOVING] = true;
        };
        Event::on(ForumRemover::class, ForumRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[ForumRemover::EVENT_AFTER_REMOVING] = true;
        };
        Event::on(ForumRemover::class, ForumRemover::EVENT_AFTER_REMOVING, $afterHandler);

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('delete')->willReturn(false);
        $forum->method('isArchived')->willReturn(true);
        $this->service->remove($forum);

        self::assertTrue($this->eventsRaised[ForumRemover::EVENT_BEFORE_REMOVING]);
        self::assertArrayNotHasKey(ForumRemover::EVENT_AFTER_REMOVING, $this->eventsRaised);

        Event::off(ForumRemover::class, ForumRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        Event::off(ForumRemover::class, ForumRemover::EVENT_AFTER_REMOVING, $afterHandler);
    }

    public function testRemoveShouldOnlyTriggerBeforeEventWhenForumIsNotArchived(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[ForumRemover::EVENT_BEFORE_REMOVING] = true;
        };
        Event::on(ForumRemover::class, ForumRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[ForumRemover::EVENT_AFTER_REMOVING] = true;
        };
        Event::on(ForumRemover::class, ForumRemover::EVENT_AFTER_REMOVING, $afterHandler);

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('delete')->willReturn(true);
        $forum->method('isArchived')->willReturn(false);
        $this->service->remove($forum);

        self::assertTrue($this->eventsRaised[ForumRemover::EVENT_BEFORE_REMOVING]);
        self::assertArrayNotHasKey(ForumRemover::EVENT_AFTER_REMOVING, $this->eventsRaised);

        Event::off(ForumRemover::class, ForumRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        Event::off(ForumRemover::class, ForumRemover::EVENT_AFTER_REMOVING, $afterHandler);
    }

    public function testRemoveShouldReturnErrorWhenEventPreventsRemoving(): void
    {
        $handler = static function (RemoveEvent $event) {
            $event->canRemove = false;
        };
        Event::on(ForumRemover::class, ForumRemover::EVENT_BEFORE_REMOVING, $handler);

        $result = $this->service->remove($this->createMock(ForumRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(ForumRemover::class, ForumRemover::EVENT_BEFORE_REMOVING, $handler);
    }
}
