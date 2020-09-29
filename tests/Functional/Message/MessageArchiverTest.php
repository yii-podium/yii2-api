<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Message;

use Podium\Api\Events\ArchiveEvent;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\MessageParticipantRepositoryInterface;
use Podium\Api\Interfaces\MessageRepositoryInterface;
use Podium\Api\Services\Message\MessageArchiver;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class MessageArchiverTest extends AppTestCase
{
    private MessageArchiver $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MessageArchiver();
        $this->eventsRaised = [];
    }

    public function testArchiveShouldTriggerBeforeAndAfterEventsWhenArchivingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[MessageArchiver::EVENT_BEFORE_ARCHIVING] = $event instanceof ArchiveEvent;
        };
        Event::on(MessageArchiver::class, MessageArchiver::EVENT_BEFORE_ARCHIVING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[MessageArchiver::EVENT_AFTER_ARCHIVING] = $event instanceof ArchiveEvent
                && 99 === $event->repository->getId();
        };
        Event::on(MessageArchiver::class, MessageArchiver::EVENT_AFTER_ARCHIVING, $afterHandler);

        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(false);
        $messageSide->method('archive')->willReturn(true);
        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $message->method('getId')->willReturn(99);
        $this->service->archive($message, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($this->eventsRaised[MessageArchiver::EVENT_BEFORE_ARCHIVING]);
        self::assertTrue($this->eventsRaised[MessageArchiver::EVENT_AFTER_ARCHIVING]);

        Event::off(MessageArchiver::class, MessageArchiver::EVENT_BEFORE_ARCHIVING, $beforeHandler);
        Event::off(MessageArchiver::class, MessageArchiver::EVENT_AFTER_ARCHIVING, $afterHandler);
    }

    public function testArchiveShouldOnlyTriggerBeforeEventWhenArchivingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[MessageArchiver::EVENT_BEFORE_ARCHIVING] = true;
        };
        Event::on(MessageArchiver::class, MessageArchiver::EVENT_BEFORE_ARCHIVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MessageArchiver::EVENT_AFTER_ARCHIVING] = true;
        };
        Event::on(MessageArchiver::class, MessageArchiver::EVENT_AFTER_ARCHIVING, $afterHandler);

        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(false);
        $messageSide->method('archive')->willReturn(false);
        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $this->service->archive($message, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($this->eventsRaised[MessageArchiver::EVENT_BEFORE_ARCHIVING]);
        self::assertArrayNotHasKey(MessageArchiver::EVENT_AFTER_ARCHIVING, $this->eventsRaised);

        Event::off(MessageArchiver::class, MessageArchiver::EVENT_BEFORE_ARCHIVING, $beforeHandler);
        Event::off(MessageArchiver::class, MessageArchiver::EVENT_AFTER_ARCHIVING, $afterHandler);
    }

    public function testArchiveShouldOnlyTriggerBeforeEventWhenMessageIsAlreadyArchived(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[MessageArchiver::EVENT_BEFORE_ARCHIVING] = true;
        };
        Event::on(MessageArchiver::class, MessageArchiver::EVENT_BEFORE_ARCHIVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MessageArchiver::EVENT_AFTER_ARCHIVING] = true;
        };
        Event::on(MessageArchiver::class, MessageArchiver::EVENT_AFTER_ARCHIVING, $afterHandler);

        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(true);
        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $this->service->archive($message, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($this->eventsRaised[MessageArchiver::EVENT_BEFORE_ARCHIVING]);
        self::assertArrayNotHasKey(MessageArchiver::EVENT_AFTER_ARCHIVING, $this->eventsRaised);

        Event::off(MessageArchiver::class, MessageArchiver::EVENT_BEFORE_ARCHIVING, $beforeHandler);
        Event::off(MessageArchiver::class, MessageArchiver::EVENT_AFTER_ARCHIVING, $afterHandler);
    }

    public function testArchiveShouldReturnErrorWhenEventPreventsArchiving(): void
    {
        $handler = static function (ArchiveEvent $event) {
            $event->canArchive = false;
        };
        Event::on(MessageArchiver::class, MessageArchiver::EVENT_BEFORE_ARCHIVING, $handler);

        $result = $this->service->archive(
            $this->createMock(MessageRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(MessageArchiver::class, MessageArchiver::EVENT_BEFORE_ARCHIVING, $handler);
    }

    public function testReviveShouldTriggerBeforeAndAfterEventsWhenRevivingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[MessageArchiver::EVENT_BEFORE_REVIVING] = $event instanceof ArchiveEvent;
        };
        Event::on(MessageArchiver::class, MessageArchiver::EVENT_BEFORE_REVIVING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[MessageArchiver::EVENT_AFTER_REVIVING] = $event instanceof ArchiveEvent
                && 101 === $event->repository->getId();
        };
        Event::on(MessageArchiver::class, MessageArchiver::EVENT_AFTER_REVIVING, $afterHandler);

        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(true);
        $messageSide->method('revive')->willReturn(true);
        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $message->method('getId')->willReturn(101);
        $this->service->revive($message, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($this->eventsRaised[MessageArchiver::EVENT_BEFORE_REVIVING]);
        self::assertTrue($this->eventsRaised[MessageArchiver::EVENT_AFTER_REVIVING]);

        Event::off(MessageArchiver::class, MessageArchiver::EVENT_BEFORE_REVIVING, $beforeHandler);
        Event::off(MessageArchiver::class, MessageArchiver::EVENT_AFTER_REVIVING, $afterHandler);
    }

    public function testReviveShouldOnlyTriggerBeforeEventWhenRevivingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[MessageArchiver::EVENT_BEFORE_REVIVING] = true;
        };
        Event::on(MessageArchiver::class, MessageArchiver::EVENT_BEFORE_REVIVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MessageArchiver::EVENT_AFTER_REVIVING] = true;
        };
        Event::on(MessageArchiver::class, MessageArchiver::EVENT_AFTER_REVIVING, $afterHandler);

        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(true);
        $messageSide->method('revive')->willReturn(false);
        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $this->service->revive($message, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($this->eventsRaised[MessageArchiver::EVENT_BEFORE_REVIVING]);
        self::assertArrayNotHasKey(MessageArchiver::EVENT_AFTER_REVIVING, $this->eventsRaised);

        Event::off(MessageArchiver::class, MessageArchiver::EVENT_BEFORE_REVIVING, $beforeHandler);
        Event::off(MessageArchiver::class, MessageArchiver::EVENT_AFTER_REVIVING, $afterHandler);
    }

    public function testReviveShouldOnlyTriggerBeforeEventWhenMessageIsNotArchived(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[MessageArchiver::EVENT_BEFORE_REVIVING] = true;
        };
        Event::on(MessageArchiver::class, MessageArchiver::EVENT_BEFORE_REVIVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MessageArchiver::EVENT_AFTER_REVIVING] = true;
        };
        Event::on(MessageArchiver::class, MessageArchiver::EVENT_AFTER_REVIVING, $afterHandler);

        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(false);
        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $this->service->revive($message, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($this->eventsRaised[MessageArchiver::EVENT_BEFORE_REVIVING]);
        self::assertArrayNotHasKey(MessageArchiver::EVENT_AFTER_REVIVING, $this->eventsRaised);

        Event::off(MessageArchiver::class, MessageArchiver::EVENT_BEFORE_REVIVING, $beforeHandler);
        Event::off(MessageArchiver::class, MessageArchiver::EVENT_AFTER_REVIVING, $afterHandler);
    }

    public function testReviveShouldReturnErrorWhenEventPreventsReviving(): void
    {
        $handler = static function (ArchiveEvent $event) {
            $event->canRevive = false;
        };
        Event::on(MessageArchiver::class, MessageArchiver::EVENT_BEFORE_REVIVING, $handler);

        $result = $this->service->revive(
            $this->createMock(MessageRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(MessageArchiver::class, MessageArchiver::EVENT_BEFORE_REVIVING, $handler);
    }
}
