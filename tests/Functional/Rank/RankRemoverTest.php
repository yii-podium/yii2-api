<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Rank;

use PHPUnit\Framework\TestCase;
use Podium\Api\Events\RemoveEvent;
use Podium\Api\Interfaces\RankRepositoryInterface;
use Podium\Api\Services\Rank\RankRemover;
use yii\base\Event;

class RankRemoverTest extends TestCase
{
    private RankRemover $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        $this->service = new RankRemover();
        $this->eventsRaised = [];
    }

    public function testRemoveShouldTriggerBeforeAndAfterEventsWhenRemovingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[RankRemover::EVENT_BEFORE_REMOVING] = $event instanceof RemoveEvent;
        };
        Event::on(RankRemover::class, RankRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[RankRemover::EVENT_AFTER_REMOVING] = true;
        };
        Event::on(RankRemover::class, RankRemover::EVENT_AFTER_REMOVING, $afterHandler);

        $rank = $this->createMock(RankRepositoryInterface::class);
        $rank->method('delete')->willReturn(true);
        $this->service->remove($rank);

        self::assertTrue($this->eventsRaised[RankRemover::EVENT_BEFORE_REMOVING]);
        self::assertTrue($this->eventsRaised[RankRemover::EVENT_AFTER_REMOVING]);

        Event::off(RankRemover::class, RankRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        Event::off(RankRemover::class, RankRemover::EVENT_AFTER_REMOVING, $afterHandler);
    }

    public function testRemoveShouldOnlyTriggerBeforeEventWhenRemovingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[RankRemover::EVENT_BEFORE_REMOVING] = true;
        };
        Event::on(RankRemover::class, RankRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[RankRemover::EVENT_AFTER_REMOVING] = true;
        };
        Event::on(RankRemover::class, RankRemover::EVENT_AFTER_REMOVING, $afterHandler);

        $rank = $this->createMock(RankRepositoryInterface::class);
        $rank->method('delete')->willReturn(false);
        $this->service->remove($rank);

        self::assertTrue($this->eventsRaised[RankRemover::EVENT_BEFORE_REMOVING]);
        self::assertArrayNotHasKey(RankRemover::EVENT_AFTER_REMOVING, $this->eventsRaised);

        Event::off(RankRemover::class, RankRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        Event::off(RankRemover::class, RankRemover::EVENT_AFTER_REMOVING, $afterHandler);
    }

    public function testRemoveShouldReturnErrorWhenEventPreventsRemoving(): void
    {
        $handler = static function (RemoveEvent $event) {
            $event->canRemove = false;
        };
        Event::on(RankRemover::class, RankRemover::EVENT_BEFORE_REMOVING, $handler);

        $result = $this->service->remove($this->createMock(RankRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(RankRemover::class, RankRemover::EVENT_BEFORE_REMOVING, $handler);
    }
}
