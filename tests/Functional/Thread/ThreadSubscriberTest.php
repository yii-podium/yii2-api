<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Thread;

use PHPUnit\Framework\TestCase;
use Podium\Api\Events\SubscriptionEvent;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\SubscriptionRepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\Services\Thread\ThreadSubscriber;
use yii\base\Event;

class ThreadSubscriberTest extends TestCase
{
    private ThreadSubscriber $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        $this->service = new ThreadSubscriber();
        $this->eventsRaised = [];
    }

    public function testSubscribeShouldTriggerBeforeAndAfterEventsWhenSubscribingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[ThreadSubscriber::EVENT_BEFORE_SUBSCRIBING] = $event instanceof SubscriptionEvent;
        };
        Event::on(ThreadSubscriber::class, ThreadSubscriber::EVENT_BEFORE_SUBSCRIBING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[ThreadSubscriber::EVENT_AFTER_SUBSCRIBING] = $event instanceof SubscriptionEvent;
        };
        Event::on(ThreadSubscriber::class, ThreadSubscriber::EVENT_AFTER_SUBSCRIBING, $afterHandler);

        $subscribe = $this->createMock(SubscriptionRepositoryInterface::class);
        $subscribe->method('isMemberSubscribed')->willReturn(false);
        $subscribe->method('subscribe')->willReturn(true);
        $this->service->subscribe(
            $subscribe,
            $this->createMock(ThreadRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertTrue($this->eventsRaised[ThreadSubscriber::EVENT_BEFORE_SUBSCRIBING]);
        self::assertTrue($this->eventsRaised[ThreadSubscriber::EVENT_AFTER_SUBSCRIBING]);

        Event::off(ThreadSubscriber::class, ThreadSubscriber::EVENT_BEFORE_SUBSCRIBING, $beforeHandler);
        Event::off(ThreadSubscriber::class, ThreadSubscriber::EVENT_AFTER_SUBSCRIBING, $afterHandler);
    }

    public function testSubscribeShouldOnlyTriggerBeforeEventWhenSubscribingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[ThreadSubscriber::EVENT_BEFORE_SUBSCRIBING] = true;
        };
        Event::on(ThreadSubscriber::class, ThreadSubscriber::EVENT_BEFORE_SUBSCRIBING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[ThreadSubscriber::EVENT_AFTER_SUBSCRIBING] = true;
        };
        Event::on(ThreadSubscriber::class, ThreadSubscriber::EVENT_AFTER_SUBSCRIBING, $afterHandler);

        $subscribe = $this->createMock(SubscriptionRepositoryInterface::class);
        $subscribe->method('isMemberSubscribed')->willReturn(false);
        $subscribe->method('subscribe')->willReturn(false);
        $this->service->subscribe(
            $subscribe,
            $this->createMock(ThreadRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertTrue($this->eventsRaised[ThreadSubscriber::EVENT_BEFORE_SUBSCRIBING]);
        self::assertArrayNotHasKey(ThreadSubscriber::EVENT_AFTER_SUBSCRIBING, $this->eventsRaised);

        Event::off(ThreadSubscriber::class, ThreadSubscriber::EVENT_BEFORE_SUBSCRIBING, $beforeHandler);
        Event::off(ThreadSubscriber::class, ThreadSubscriber::EVENT_AFTER_SUBSCRIBING, $afterHandler);
    }

    public function testSubscribeShouldReturnErrorWhenEventPreventsSubscribing(): void
    {
        $handler = static function (SubscriptionEvent $event) {
            $event->canSubscribe = false;
        };
        Event::on(ThreadSubscriber::class, ThreadSubscriber::EVENT_BEFORE_SUBSCRIBING, $handler);

        $result = $this->service->subscribe(
            $this->createMock(SubscriptionRepositoryInterface::class),
            $this->createMock(ThreadRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(ThreadSubscriber::class, ThreadSubscriber::EVENT_BEFORE_SUBSCRIBING, $handler);
    }

    public function testUnsubscribeShouldTriggerBeforeAndAfterEventsWhenUnsubscribingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[ThreadSubscriber::EVENT_BEFORE_UNSUBSCRIBING] = $event instanceof SubscriptionEvent;
        };
        Event::on(ThreadSubscriber::class, ThreadSubscriber::EVENT_BEFORE_UNSUBSCRIBING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[ThreadSubscriber::EVENT_AFTER_UNSUBSCRIBING] = true;
        };
        Event::on(ThreadSubscriber::class, ThreadSubscriber::EVENT_AFTER_UNSUBSCRIBING, $afterHandler);

        $subscribe = $this->createMock(SubscriptionRepositoryInterface::class);
        $subscribe->method('fetchOne')->willReturn(true);
        $subscribe->method('delete')->willReturn(true);
        $this->service->unsubscribe(
            $subscribe,
            $this->createMock(ThreadRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertTrue($this->eventsRaised[ThreadSubscriber::EVENT_BEFORE_UNSUBSCRIBING]);
        self::assertTrue($this->eventsRaised[ThreadSubscriber::EVENT_AFTER_UNSUBSCRIBING]);

        Event::off(ThreadSubscriber::class, ThreadSubscriber::EVENT_BEFORE_UNSUBSCRIBING, $beforeHandler);
        Event::off(ThreadSubscriber::class, ThreadSubscriber::EVENT_AFTER_UNSUBSCRIBING, $afterHandler);
    }

    public function testUnsubscribeShouldOnlyTriggerBeforeEventWhenUnsubscribingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[ThreadSubscriber::EVENT_BEFORE_UNSUBSCRIBING] = true;
        };
        Event::on(ThreadSubscriber::class, ThreadSubscriber::EVENT_BEFORE_UNSUBSCRIBING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[ThreadSubscriber::EVENT_AFTER_UNSUBSCRIBING] = true;
        };
        Event::on(ThreadSubscriber::class, ThreadSubscriber::EVENT_AFTER_UNSUBSCRIBING, $afterHandler);

        $subscribe = $this->createMock(SubscriptionRepositoryInterface::class);
        $subscribe->method('fetchOne')->willReturn(true);
        $subscribe->method('delete')->willReturn(false);
        $this->service->unsubscribe(
            $subscribe,
            $this->createMock(ThreadRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertTrue($this->eventsRaised[ThreadSubscriber::EVENT_BEFORE_UNSUBSCRIBING]);
        self::assertArrayNotHasKey(ThreadSubscriber::EVENT_AFTER_UNSUBSCRIBING, $this->eventsRaised);

        Event::off(ThreadSubscriber::class, ThreadSubscriber::EVENT_BEFORE_UNSUBSCRIBING, $beforeHandler);
        Event::off(ThreadSubscriber::class, ThreadSubscriber::EVENT_AFTER_UNSUBSCRIBING, $afterHandler);
    }

    public function testUnsubscribeShouldReturnErrorWhenEventPreventsUnsubscribing(): void
    {
        $handler = static function (SubscriptionEvent $event) {
            $event->canUnsubscribe = false;
        };
        Event::on(ThreadSubscriber::class, ThreadSubscriber::EVENT_BEFORE_UNSUBSCRIBING, $handler);

        $result = $this->service->unsubscribe(
            $this->createMock(SubscriptionRepositoryInterface::class),
            $this->createMock(ThreadRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(ThreadSubscriber::class, ThreadSubscriber::EVENT_BEFORE_UNSUBSCRIBING, $handler);
    }
}
