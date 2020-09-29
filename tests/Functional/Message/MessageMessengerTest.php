<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Message;

use Podium\Api\Events\SendEvent;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\MessageRepositoryInterface;
use Podium\Api\Services\Message\MessageMessenger;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class MessageMessengerTest extends AppTestCase
{
    private MessageMessenger $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MessageMessenger();
        $this->eventsRaised = [];
    }

    public function testSendShouldTriggerBeforeAndAfterEventsWhenSendingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[MessageMessenger::EVENT_BEFORE_SENDING] = $event instanceof SendEvent;
        };
        Event::on(MessageMessenger::class, MessageMessenger::EVENT_BEFORE_SENDING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[MessageMessenger::EVENT_AFTER_SENDING] = $event instanceof SendEvent
                && 17 === $event->repository->getId();
        };
        Event::on(MessageMessenger::class, MessageMessenger::EVENT_AFTER_SENDING, $afterHandler);

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('send')->willReturn(true);
        $message->method('getId')->willReturn(17);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $this->service->send($message, $member, $member);

        self::assertTrue($this->eventsRaised[MessageMessenger::EVENT_BEFORE_SENDING]);
        self::assertTrue($this->eventsRaised[MessageMessenger::EVENT_AFTER_SENDING]);

        Event::off(MessageMessenger::class, MessageMessenger::EVENT_BEFORE_SENDING, $beforeHandler);
        Event::off(MessageMessenger::class, MessageMessenger::EVENT_AFTER_SENDING, $afterHandler);
    }

    public function testSendShouldOnlyTriggerBeforeEventWhenSendingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[MessageMessenger::EVENT_BEFORE_SENDING] = true;
        };
        Event::on(MessageMessenger::class, MessageMessenger::EVENT_BEFORE_SENDING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MessageMessenger::EVENT_AFTER_SENDING] = true;
        };
        Event::on(MessageMessenger::class, MessageMessenger::EVENT_AFTER_SENDING, $afterHandler);

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('send')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $this->service->send($message, $member, $member);

        self::assertTrue($this->eventsRaised[MessageMessenger::EVENT_BEFORE_SENDING]);
        self::assertArrayNotHasKey(MessageMessenger::EVENT_AFTER_SENDING, $this->eventsRaised);

        Event::off(MessageMessenger::class, MessageMessenger::EVENT_BEFORE_SENDING, $beforeHandler);
        Event::off(MessageMessenger::class, MessageMessenger::EVENT_AFTER_SENDING, $afterHandler);
    }

    public function testSendShouldReturnErrorWhenEventPreventsSending(): void
    {
        $handler = static function (SendEvent $event) {
            $event->canSend = false;
        };
        Event::on(MessageMessenger::class, MessageMessenger::EVENT_BEFORE_SENDING, $handler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->send(
            $this->createMock(MessageRepositoryInterface::class),
            $member,
            $member
        );
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(MessageMessenger::class, MessageMessenger::EVENT_BEFORE_SENDING, $handler);
    }
}
