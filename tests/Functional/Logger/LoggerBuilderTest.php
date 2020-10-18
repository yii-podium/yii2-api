<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Logger;

use Podium\Api\Events\BuildEvent;
use Podium\Api\Interfaces\LogRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Services\Log\LogBuilder;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class LoggerBuilderTest extends AppTestCase
{
    private LogBuilder $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LogBuilder();
        $this->eventsRaised = [];
    }

    public function testCreateShouldTriggerBeforeAndAfterEventsWhenCreatingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[LogBuilder::EVENT_BEFORE_CREATING] = $event instanceof BuildEvent;
        };
        Event::on(LogBuilder::class, LogBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[LogBuilder::EVENT_AFTER_CREATING] = $event instanceof BuildEvent
                && 99 === $event->repository->getId();
        };
        Event::on(LogBuilder::class, LogBuilder::EVENT_AFTER_CREATING, $afterHandler);

        $log = $this->createMock(LogRepositoryInterface::class);
        $log->method('create')->willReturn(true);
        $log->method('getId')->willReturn(99);
        $this->service->create($log, $this->createMock(MemberRepositoryInterface::class), 'action');

        self::assertTrue($this->eventsRaised[LogBuilder::EVENT_BEFORE_CREATING]);
        self::assertTrue($this->eventsRaised[LogBuilder::EVENT_AFTER_CREATING]);

        Event::off(LogBuilder::class, LogBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        Event::off(LogBuilder::class, LogBuilder::EVENT_AFTER_CREATING, $afterHandler);
    }

    public function testCreateShouldOnlyTriggerBeforeEventWhenCreatingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[LogBuilder::EVENT_BEFORE_CREATING] = true;
        };
        Event::on(LogBuilder::class, LogBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[LogBuilder::EVENT_AFTER_CREATING] = true;
        };
        Event::on(LogBuilder::class, LogBuilder::EVENT_AFTER_CREATING, $afterHandler);

        $log = $this->createMock(LogRepositoryInterface::class);
        $log->method('create')->willReturn(false);
        $this->service->create($log, $this->createMock(MemberRepositoryInterface::class), 'action');

        self::assertTrue($this->eventsRaised[LogBuilder::EVENT_BEFORE_CREATING]);
        self::assertArrayNotHasKey(LogBuilder::EVENT_AFTER_CREATING, $this->eventsRaised);

        Event::off(LogBuilder::class, LogBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        Event::off(LogBuilder::class, LogBuilder::EVENT_AFTER_CREATING, $afterHandler);
    }

    public function testCreateShouldReturnErrorWhenEventPreventsCreating(): void
    {
        $handler = static function (BuildEvent $event) {
            $event->canCreate = false;
        };
        Event::on(LogBuilder::class, LogBuilder::EVENT_BEFORE_CREATING, $handler);

        $result = $this->service->create(
            $this->createMock(LogRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class),
            'action'
        );
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(LogBuilder::class, LogBuilder::EVENT_BEFORE_CREATING, $handler);
    }
}
