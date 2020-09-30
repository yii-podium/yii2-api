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
            $this->eventsRaised[MemberAcquaintance::EVENT_AFTER_BEFRIENDING] = $event instanceof AcquaintanceEvent;
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

    public function testUnfriendShouldTriggerBeforeAndAfterEventsWhenUnfriendingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_UNFRIENDING] = $event instanceof AcquaintanceEvent;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_UNFRIENDING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MemberAcquaintance::EVENT_AFTER_UNFRIENDING] = true;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_UNFRIENDING, $afterHandler);

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isFriend')->willReturn(true);
        $acquaintance->method('delete')->willReturn(true);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $this->service->unfriend($acquaintance, $member, $target);

        self::assertTrue($this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_UNFRIENDING]);
        self::assertTrue($this->eventsRaised[MemberAcquaintance::EVENT_AFTER_UNFRIENDING]);

        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_UNFRIENDING, $beforeHandler);
        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_UNFRIENDING, $afterHandler);
    }

    public function testUnfriendShouldOnlyTriggerBeforeEventWhenUnfriendingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_UNFRIENDING] = true;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_UNFRIENDING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MemberAcquaintance::EVENT_AFTER_UNFRIENDING] = true;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_UNFRIENDING, $afterHandler);

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isFriend')->willReturn(true);
        $acquaintance->method('delete')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $this->service->unfriend($acquaintance, $member, $target);

        self::assertTrue($this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_UNFRIENDING]);
        self::assertArrayNotHasKey(MemberAcquaintance::EVENT_AFTER_UNFRIENDING, $this->eventsRaised);

        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_UNFRIENDING, $beforeHandler);
        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_UNFRIENDING, $afterHandler);
    }

    public function testUnfriendShouldOnlyTriggerBeforeEventWhenAcquaintanceNotExists(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_UNFRIENDING] = true;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_UNFRIENDING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MemberAcquaintance::EVENT_AFTER_UNFRIENDING] = true;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_UNFRIENDING, $afterHandler);

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $this->service->unfriend($acquaintance, $member, $target);

        self::assertTrue($this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_UNFRIENDING]);
        self::assertArrayNotHasKey(MemberAcquaintance::EVENT_AFTER_UNFRIENDING, $this->eventsRaised);

        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_UNFRIENDING, $beforeHandler);
        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_UNFRIENDING, $afterHandler);
    }

    public function testUnfriendShouldOnlyTriggerBeforeEventWhenTargetIsNotFriend(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_UNFRIENDING] = true;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_UNFRIENDING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MemberAcquaintance::EVENT_AFTER_UNFRIENDING] = true;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_UNFRIENDING, $afterHandler);

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isFriend')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $this->service->unfriend($acquaintance, $member, $target);

        self::assertTrue($this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_UNFRIENDING]);
        self::assertArrayNotHasKey(MemberAcquaintance::EVENT_AFTER_UNFRIENDING, $this->eventsRaised);

        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_UNFRIENDING, $beforeHandler);
        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_UNFRIENDING, $afterHandler);
    }

    public function testUnfriendShouldReturnErrorWhenEventPreventsUnfriending(): void
    {
        $handler = static function (AcquaintanceEvent $event) {
            $event->canUnfriend = false;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_UNFRIENDING, $handler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->unfriend(
            $this->createMock(AcquaintanceRepositoryInterface::class),
            $member,
            $member
        );
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_UNFRIENDING, $handler);
    }

    public function testIgnoreShouldTriggerBeforeAndAfterEventsWhenIgnoringIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_IGNORING] = $event instanceof AcquaintanceEvent;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_IGNORING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[MemberAcquaintance::EVENT_AFTER_IGNORING] = $event instanceof AcquaintanceEvent;
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

    public function testUnignoreShouldTriggerBeforeAndAfterEventsWhenUnignoringIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_UNIGNORING] = $event instanceof AcquaintanceEvent;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_UNIGNORING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MemberAcquaintance::EVENT_AFTER_UNIGNORING] = true;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_UNIGNORING, $afterHandler);

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isIgnoring')->willReturn(true);
        $acquaintance->method('delete')->willReturn(true);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $this->service->unignore($acquaintance, $member, $target);

        self::assertTrue($this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_UNIGNORING]);
        self::assertTrue($this->eventsRaised[MemberAcquaintance::EVENT_AFTER_UNIGNORING]);

        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_UNIGNORING, $beforeHandler);
        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_UNIGNORING, $afterHandler);
    }

    public function testUnignoreShouldOnlyTriggerBeforeEventWhenUnignoringErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_UNIGNORING] = true;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_UNIGNORING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MemberAcquaintance::EVENT_AFTER_UNIGNORING] = true;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_UNIGNORING, $afterHandler);

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isIgnoring')->willReturn(true);
        $acquaintance->method('delete')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $this->service->unignore($acquaintance, $member, $target);

        self::assertTrue($this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_UNIGNORING]);
        self::assertArrayNotHasKey(MemberAcquaintance::EVENT_AFTER_UNIGNORING, $this->eventsRaised);

        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_UNIGNORING, $beforeHandler);
        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_UNIGNORING, $afterHandler);
    }

    public function testUnignoreShouldOnlyTriggerBeforeEventWhenAcquaintanceNotExists(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_UNIGNORING] = true;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_UNIGNORING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MemberAcquaintance::EVENT_AFTER_UNIGNORING] = true;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_UNIGNORING, $afterHandler);

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $this->service->unignore($acquaintance, $member, $target);

        self::assertTrue($this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_UNIGNORING]);
        self::assertArrayNotHasKey(MemberAcquaintance::EVENT_AFTER_UNIGNORING, $this->eventsRaised);

        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_UNIGNORING, $beforeHandler);
        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_UNIGNORING, $afterHandler);
    }

    public function testUnignoreShouldOnlyTriggerBeforeEventWhenTargetIsNotIgnored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_UNIGNORING] = true;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_UNIGNORING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MemberAcquaintance::EVENT_AFTER_UNIGNORING] = true;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_UNIGNORING, $afterHandler);

        $acquaintance = $this->createMock(AcquaintanceRepositoryInterface::class);
        $acquaintance->method('fetchOne')->willReturn(true);
        $acquaintance->method('isIgnoring')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);
        $this->service->unignore($acquaintance, $member, $target);

        self::assertTrue($this->eventsRaised[MemberAcquaintance::EVENT_BEFORE_UNIGNORING]);
        self::assertArrayNotHasKey(MemberAcquaintance::EVENT_AFTER_UNIGNORING, $this->eventsRaised);

        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_UNIGNORING, $beforeHandler);
        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_AFTER_UNIGNORING, $afterHandler);
    }

    public function testUnignoreShouldReturnErrorWhenEventPreventsUnignoring(): void
    {
        $handler = static function (AcquaintanceEvent $event) {
            $event->canUnignore = false;
        };
        Event::on(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_UNIGNORING, $handler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->unignore(
            $this->createMock(AcquaintanceRepositoryInterface::class),
            $member,
            $member
        );
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(MemberAcquaintance::class, MemberAcquaintance::EVENT_BEFORE_UNIGNORING, $handler);
    }
}
