<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Poll;

use Podium\Api\Events\RemoveEvent;
use Podium\Api\Interfaces\PollPostRepositoryInterface;
use Podium\Api\Services\Poll\PollRemover;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class PollRemoverTest extends AppTestCase
{
    private PollRemover $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PollRemover();
        $this->eventsRaised = [];
    }

    public function testRemoveShouldTriggerBeforeAndAfterEventsWhenRemovingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[PollRemover::EVENT_BEFORE_REMOVING] = $event instanceof RemoveEvent;
        };
        Event::on(PollRemover::class, PollRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[PollRemover::EVENT_AFTER_REMOVING] = true;
        };
        Event::on(PollRemover::class, PollRemover::EVENT_AFTER_REMOVING, $afterHandler);

        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('removePoll')->willReturn(true);
        $this->service->remove($post);

        self::assertTrue($this->eventsRaised[PollRemover::EVENT_BEFORE_REMOVING]);
        self::assertTrue($this->eventsRaised[PollRemover::EVENT_AFTER_REMOVING]);

        Event::off(PollRemover::class, PollRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        Event::off(PollRemover::class, PollRemover::EVENT_AFTER_REMOVING, $afterHandler);
    }

    public function testRemoveShouldOnlyTriggerBeforeEventWhenRemovingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[PollRemover::EVENT_BEFORE_REMOVING] = true;
        };
        Event::on(PollRemover::class, PollRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[PollRemover::EVENT_AFTER_REMOVING] = true;
        };
        Event::on(PollRemover::class, PollRemover::EVENT_AFTER_REMOVING, $afterHandler);

        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('removePoll')->willReturn(false);
        $this->service->remove($post);

        self::assertTrue($this->eventsRaised[PollRemover::EVENT_BEFORE_REMOVING]);
        self::assertArrayNotHasKey(PollRemover::EVENT_AFTER_REMOVING, $this->eventsRaised);

        Event::off(PollRemover::class, PollRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        Event::off(PollRemover::class, PollRemover::EVENT_AFTER_REMOVING, $afterHandler);
    }

    public function testRemoveShouldReturnErrorWhenEventPreventsRemoving(): void
    {
        $handler = static function (RemoveEvent $event) {
            $event->canRemove = false;
        };
        Event::on(PollRemover::class, PollRemover::EVENT_BEFORE_REMOVING, $handler);

        $result = $this->service->remove($this->createMock(PollPostRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(PollRemover::class, PollRemover::EVENT_BEFORE_REMOVING, $handler);
    }
}
