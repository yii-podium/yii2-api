<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Logger;

use Podium\Api\Events\RemoveEvent;
use Podium\Api\Interfaces\LogRepositoryInterface;
use Podium\Api\Services\Logger\LoggerRemover;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class LoggerRemoverTest extends AppTestCase
{
    private LoggerRemover $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LoggerRemover();
        $this->eventsRaised = [];
    }

    public function testRemoveShouldTriggerBeforeAndAfterEventsWhenRemovingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[LoggerRemover::EVENT_BEFORE_REMOVING] = $event instanceof RemoveEvent;
        };
        Event::on(LoggerRemover::class, LoggerRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[LoggerRemover::EVENT_AFTER_REMOVING] = true;
        };
        Event::on(LoggerRemover::class, LoggerRemover::EVENT_AFTER_REMOVING, $afterHandler);

        $log = $this->createMock(LogRepositoryInterface::class);
        $log->method('delete')->willReturn(true);
        $this->service->remove($log);

        self::assertTrue($this->eventsRaised[LoggerRemover::EVENT_BEFORE_REMOVING]);
        self::assertTrue($this->eventsRaised[LoggerRemover::EVENT_AFTER_REMOVING]);

        Event::off(LoggerRemover::class, LoggerRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        Event::off(LoggerRemover::class, LoggerRemover::EVENT_AFTER_REMOVING, $afterHandler);
    }

    public function testRemoveShouldOnlyTriggerBeforeEventWhenRemovingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[LoggerRemover::EVENT_BEFORE_REMOVING] = true;
        };
        Event::on(LoggerRemover::class, LoggerRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[LoggerRemover::EVENT_AFTER_REMOVING] = true;
        };
        Event::on(LoggerRemover::class, LoggerRemover::EVENT_AFTER_REMOVING, $afterHandler);

        $log = $this->createMock(LogRepositoryInterface::class);
        $log->method('delete')->willReturn(false);
        $this->service->remove($log);

        self::assertTrue($this->eventsRaised[LoggerRemover::EVENT_BEFORE_REMOVING]);
        self::assertArrayNotHasKey(LoggerRemover::EVENT_AFTER_REMOVING, $this->eventsRaised);

        Event::off(LoggerRemover::class, LoggerRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        Event::off(LoggerRemover::class, LoggerRemover::EVENT_AFTER_REMOVING, $afterHandler);
    }

    public function testRemoveShouldReturnErrorWhenEventPreventsRemoving(): void
    {
        $handler = static function (RemoveEvent $event) {
            $event->canRemove = false;
        };
        Event::on(LoggerRemover::class, LoggerRemover::EVENT_BEFORE_REMOVING, $handler);

        $result = $this->service->remove($this->createMock(LogRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(LoggerRemover::class, LoggerRemover::EVENT_BEFORE_REMOVING, $handler);
    }
}
