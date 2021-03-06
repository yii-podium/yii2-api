<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Forum;

use Podium\Api\Events\MoveEvent;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Services\Forum\ForumMover;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class ForumMoverTest extends AppTestCase
{
    private ForumMover $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ForumMover();
        $this->eventsRaised = [];
    }

    public function testMoveShouldTriggerBeforeAndAfterEventsWhenMovingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[ForumMover::EVENT_BEFORE_MOVING] = $event instanceof MoveEvent;
        };
        Event::on(ForumMover::class, ForumMover::EVENT_BEFORE_MOVING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[ForumMover::EVENT_AFTER_MOVING] = $event instanceof MoveEvent
                && 11 === $event->repository->getId();
        };
        Event::on(ForumMover::class, ForumMover::EVENT_AFTER_MOVING, $afterHandler);

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('getId')->willReturn(11);
        $forum->method('move')->willReturn(true);
        $this->service->move($forum, $this->createMock(CategoryRepositoryInterface::class));

        self::assertTrue($this->eventsRaised[ForumMover::EVENT_BEFORE_MOVING]);
        self::assertTrue($this->eventsRaised[ForumMover::EVENT_AFTER_MOVING]);

        Event::off(ForumMover::class, ForumMover::EVENT_BEFORE_MOVING, $beforeHandler);
        Event::off(ForumMover::class, ForumMover::EVENT_AFTER_MOVING, $afterHandler);
    }

    public function testMoveShouldOnlyTriggerBeforeEventWhenMovingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[ForumMover::EVENT_BEFORE_MOVING] = true;
        };
        Event::on(ForumMover::class, ForumMover::EVENT_BEFORE_MOVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[ForumMover::EVENT_AFTER_MOVING] = true;
        };
        Event::on(ForumMover::class, ForumMover::EVENT_AFTER_MOVING, $afterHandler);

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('move')->willReturn(false);
        $this->service->move($forum, $this->createMock(CategoryRepositoryInterface::class));

        self::assertTrue($this->eventsRaised[ForumMover::EVENT_BEFORE_MOVING]);
        self::assertArrayNotHasKey(ForumMover::EVENT_AFTER_MOVING, $this->eventsRaised);

        Event::off(ForumMover::class, ForumMover::EVENT_BEFORE_MOVING, $beforeHandler);
        Event::off(ForumMover::class, ForumMover::EVENT_AFTER_MOVING, $afterHandler);
    }

    public function testMoveShouldReturnErrorWhenEventPreventsMoving(): void
    {
        $handler = static function (MoveEvent $event) {
            $event->canMove = false;
        };
        Event::on(ForumMover::class, ForumMover::EVENT_BEFORE_MOVING, $handler);

        $result = $this->service->move(
            $this->createMock(ForumRepositoryInterface::class),
            $this->createMock(CategoryRepositoryInterface::class)
        );
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(ForumMover::class, ForumMover::EVENT_BEFORE_MOVING, $handler);
    }
}
