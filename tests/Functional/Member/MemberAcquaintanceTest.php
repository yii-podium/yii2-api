<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Member;

use Podium\Api\Events\AcquaintanceEvent;
use Podium\Api\Interfaces\AcquaintanceRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Services\Member\MemberAcquaintance;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class MemberAcquaintanceTest extends AppTestCase
{
    private MemberAcquaintance $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MemberAcquaintance();
        $this->eventsRaised = [];
    }

    public function testBefriendShouldTriggerBeforeAndAfterEventsWhenBefriendingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_BEFRIENDING] = $event instanceof AcquaintanceEvent;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_BEFRIENDING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[MemberAcquaintance::EVENT_AFTER_BEFRIENDING] = $event instanceof AcquaintanceEvent
                && $event->repository instanceof AcquaintanceRepositoryInterface;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_BEFRIENDING, $afterHandler);

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('befriend')->willReturn(true);
        $acquaintance->method('isFriend')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $this->service->befriend($acquaintance, $member, $target);

        self::assertTrue($this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_BEFRIENDING]);
        self::assertTrue($this->eventsRaised[MemberAcquaintance::EVENT_AFTER_BEFRIENDING]);

        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_BEFRIENDING, $beforeHandler);
        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_BEFRIENDING, $afterHandler);
    }

    public function testBefriendShouldOnlyTriggerBeforeEventWhenBefriendingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_BEFRIENDING] = true;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_BEFRIENDING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MemberAcquaintance::EVENT_AFTER_BEFRIENDING] = true;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_BEFRIENDING, $afterHandler);

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('befriend')->willReturn(false);
        $acquaintance->method('isFriend')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $this->service->befriend($acquaintance, $member, $target);

        self::assertTrue($this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_BEFRIENDING]);
        self::assertArrayNotHasKey(MemberAcquaintance::EVENT_AFTER_BEFRIENDING, $this->eventsRaised);

        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_BEFRIENDING, $beforeHandler);
        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_BEFRIENDING, $afterHandler);
    }

    public function testBefriendShouldReturnErrorWhenEventPreventsBefriending(): void
    {
        $handler = static function (AcquaintanceEvent $event) {
            $event->canBeFriends = false;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_BEFRIENDING, $handler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->befriend(
            $this->createMock(AcquaintanceRepositoryInterface::class),
            $member,
            $member
        );
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_BEFRIENDING, $handler);
    }

    public function testIgnoreShouldTriggerBeforeAndAfterEventsWhenIgnoringIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_IGNORING] = $event instanceof AcquaintanceEvent;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_IGNORING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[MemberAcquaintance::EVENT_AFTER_IGNORING] = $event instanceof AcquaintanceEvent
                && $event->repository instanceof AcquaintanceRepositoryInterface;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_IGNORING, $afterHandler);

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('ignore')->willReturn(true);
        $acquaintance->method('isIgnoring')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $this->service->ignore($acquaintance, $member, $target);

        self::assertTrue($this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_IGNORING]);
        self::assertTrue($this->eventsRaised[MemberAcquaintance::EVENT_AFTER_IGNORING]);

        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_IGNORING, $beforeHandler);
        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_IGNORING, $afterHandler);
    }

    public function testIgnoreShouldOnlyTriggerBeforeEventWhenIgnoringErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_IGNORING] = true;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_IGNORING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MemberAcquaintance::EVENT_AFTER_IGNORING] = true;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_IGNORING, $afterHandler);

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('ignore')->willReturn(false);
        $acquaintance->method('isIgnoring')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $this->service->ignore($acquaintance, $member, $target);

        self::assertTrue($this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_IGNORING]);
        self::assertArrayNotHasKey(MemberAcquaintance::EVENT_AFTER_IGNORING, $this->eventsRaised);

        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_IGNORING, $beforeHandler);
        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_IGNORING, $afterHandler);
    }

    public function testIgnoreShouldReturnErrorWhenEventPreventsIgnoring(): void
    {
        $handler = static function (AcquaintanceEvent $event) {
            $event->canIgnore = false;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_IGNORING, $handler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->ignore(
            $this->createMock(AcquaintanceRepositoryInterface::class),
            $member,
            $member
        );
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_IGNORING, $handler);
    }

    public function testDisconnectShouldTriggerBeforeAndAfterEventsWhenDisconnectingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_DISCONNECTING] = $event instanceof AcquaintanceEvent;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_DISCONNECTING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MemberAcquaintance::EVENT_AFTER_DISCONNECTING] = true;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_DISCONNECTING, $afterHandler);

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('delete')->willReturn(true);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $this->service->disconnect($acquaintance, $member, $target);

        self::assertTrue($this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_DISCONNECTING]);
        self::assertTrue($this->eventsRaised[MemberAcquaintance::EVENT_AFTER_DISCONNECTING]);

        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_DISCONNECTING, $beforeHandler);
        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_DISCONNECTING, $afterHandler);
    }

    public function testDisconnectShouldOnlyTriggerBeforeEventWhenDisconnectingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_DISCONNECTING] = true;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_DISCONNECTING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MemberAcquaintance::EVENT_AFTER_DISCONNECTING] = true;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_DISCONNECTING, $afterHandler);

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('delete')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $this->service->disconnect($acquaintance, $member, $target);

        self::assertTrue($this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_DISCONNECTING]);
        self::assertArrayNotHasKey(MemberAcquaintance::EVENT_AFTER_DISCONNECTING, $this->eventsRaised);

        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_DISCONNECTING, $beforeHandler);
        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_DISCONNECTING, $afterHandler);
    }

    public function testDisconnectShouldOnlyTriggerBeforeEventWhenAcquaintanceNotExists(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_DISCONNECTING] = true;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_DISCONNECTING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MemberAcquaintance::EVENT_AFTER_DISCONNECTING] = true;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_DISCONNECTING, $afterHandler);

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $this->service->disconnect($acquaintance, $member, $target);

        self::assertTrue($this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_DISCONNECTING]);
        self::assertArrayNotHasKey(MemberAcquaintance::EVENT_AFTER_DISCONNECTING, $this->eventsRaised);

        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_DISCONNECTING, $beforeHandler);
        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_DISCONNECTING, $afterHandler);
    }

    public function testDisconnectShouldReturnErrorWhenEventPreventsDisconnecting(): void
    {
        $handler = static function (AcquaintanceEvent $event) {
            $event->canDisconnect = false;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_DISCONNECTING, $handler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->disconnect(
            $this->createMock(AcquaintanceRepositoryInterface::class),
            $member,
            $member
        );
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_DISCONNECTING, $handler);
    }
}
