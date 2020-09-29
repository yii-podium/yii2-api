<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Message;

use Podium\Api\Events\RemoveEvent;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\MessageParticipantRepositoryInterface;
use Podium\Api\Interfaces\MessageRepositoryInterface;
use Podium\Api\Services\Message\MessageRemover;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class MessageRemoverTest extends AppTestCase
{
    private MessageRemover $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MessageRemover();
        $this->eventsRaised = [];
    }

    public function testRemoveShouldTriggerBeforeAndAfterEventsWhenRemovingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[MessageRemover::EVENT_BEFORE_REMOVING] = $event instanceof RemoveEvent;
        };
        Event::on(MessageRemover::class, MessageRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MessageRemover::EVENT_AFTER_REMOVING] = true;
        };
        Event::on(MessageRemover::class, MessageRemover::EVENT_AFTER_REMOVING, $afterHandler);

        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(true);
        $messageSide->method('delete')->willReturn(true);
        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $message->method('isCompletelyDeleted')->willReturn(false);
        $this->service->remove($message, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($this->eventsRaised[MessageRemover::EVENT_BEFORE_REMOVING]);
        self::assertTrue($this->eventsRaised[MessageRemover::EVENT_AFTER_REMOVING]);

        Event::off(MessageRemover::class, MessageRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        Event::off(MessageRemover::class, MessageRemover::EVENT_AFTER_REMOVING, $afterHandler);
    }

    public function testRemoveShouldOnlyTriggerBeforeEventWhenRemovingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[MessageRemover::EVENT_BEFORE_REMOVING] = true;
        };
        Event::on(MessageRemover::class, MessageRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MessageRemover::EVENT_AFTER_REMOVING] = true;
        };
        Event::on(MessageRemover::class, MessageRemover::EVENT_AFTER_REMOVING, $afterHandler);

        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(true);
        $messageSide->method('delete')->willReturn(false);
        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $this->service->remove($message, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($this->eventsRaised[MessageRemover::EVENT_BEFORE_REMOVING]);
        self::assertArrayNotHasKey(MessageRemover::EVENT_AFTER_REMOVING, $this->eventsRaised);

        Event::off(MessageRemover::class, MessageRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        Event::off(MessageRemover::class, MessageRemover::EVENT_AFTER_REMOVING, $afterHandler);
    }

    public function testRemoveShouldOnlyTriggerBeforeEventWhenMessageIsNotArchived(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[MessageRemover::EVENT_BEFORE_REMOVING] = true;
        };
        Event::on(MessageRemover::class, MessageRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MessageRemover::EVENT_AFTER_REMOVING] = true;
        };
        Event::on(MessageRemover::class, MessageRemover::EVENT_AFTER_REMOVING, $afterHandler);

        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(false);
        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $this->service->remove($message, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($this->eventsRaised[MessageRemover::EVENT_BEFORE_REMOVING]);
        self::assertArrayNotHasKey(MessageRemover::EVENT_AFTER_REMOVING, $this->eventsRaised);

        Event::off(MessageRemover::class, MessageRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        Event::off(MessageRemover::class, MessageRemover::EVENT_AFTER_REMOVING, $afterHandler);
    }

    public function testRemoveShouldOnlyTriggerBeforeEventWhenMessageIsCompletelyDeletedAndDeletingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[MessageRemover::EVENT_BEFORE_REMOVING] = true;
        };
        Event::on(MessageRemover::class, MessageRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MessageRemover::EVENT_AFTER_REMOVING] = true;
        };
        Event::on(MessageRemover::class, MessageRemover::EVENT_AFTER_REMOVING, $afterHandler);

        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(true);
        $messageSide->method('delete')->willReturn(true);
        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $message->method('isCompletelyDeleted')->willReturn(true);
        $message->method('delete')->willReturn(false);
        $this->service->remove($message, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($this->eventsRaised[MessageRemover::EVENT_BEFORE_REMOVING]);
        self::assertArrayNotHasKey(MessageRemover::EVENT_AFTER_REMOVING, $this->eventsRaised);

        Event::off(MessageRemover::class, MessageRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        Event::off(MessageRemover::class, MessageRemover::EVENT_AFTER_REMOVING, $afterHandler);
    }

    public function testRemoveShouldReturnErrorWhenEventPreventsRemoving(): void
    {
        $handler = static function (RemoveEvent $event) {
            $event->canRemove = false;
        };
        Event::on(MessageRemover::class, MessageRemover::EVENT_BEFORE_REMOVING, $handler);

        $result = $this->service->remove(
            $this->createMock(MessageRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(MessageRemover::class, MessageRemover::EVENT_BEFORE_REMOVING, $handler);
    }
}
