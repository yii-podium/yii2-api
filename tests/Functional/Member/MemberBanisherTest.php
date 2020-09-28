<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Member;

use Podium\Api\Events\BanEvent;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Services\Member\MemberBanisher;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class MemberBanisherTest extends AppTestCase
{
    private MemberBanisher $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MemberBanisher();
        $this->eventsRaised = [];
    }

    public function testBanShouldTriggerBeforeAndAfterEventsWhenBanningIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[MemberBanisher::EVENT_BEFORE_BANNING] = $event instanceof BanEvent;
        };
        Event::on(MemberBanisher::class, MemberBanisher::EVENT_BEFORE_BANNING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[MemberBanisher::EVENT_AFTER_BANNING] = $event instanceof BanEvent
                && 99 === $event->repository->getId();
        };
        Event::on(MemberBanisher::class, MemberBanisher::EVENT_AFTER_BANNING, $afterHandler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('ban')->willReturn(true);
        $member->method('getId')->willReturn(99);
        $this->service->ban($member);

        self::assertTrue($this->eventsRaised[MemberBanisher::EVENT_BEFORE_BANNING]);
        self::assertTrue($this->eventsRaised[MemberBanisher::EVENT_AFTER_BANNING]);

        Event::off(MemberBanisher::class, MemberBanisher::EVENT_BEFORE_BANNING, $beforeHandler);
        Event::off(MemberBanisher::class, MemberBanisher::EVENT_AFTER_BANNING, $afterHandler);
    }

    public function testBanShouldOnlyTriggerBeforeEventWhenBanningErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[MemberBanisher::EVENT_BEFORE_BANNING] = true;
        };
        Event::on(MemberBanisher::class, MemberBanisher::EVENT_BEFORE_BANNING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MemberBanisher::EVENT_AFTER_BANNING] = true;
        };
        Event::on(MemberBanisher::class, MemberBanisher::EVENT_AFTER_BANNING, $afterHandler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('ban')->willReturn(false);
        $this->service->ban($member);

        self::assertTrue($this->eventsRaised[MemberBanisher::EVENT_BEFORE_BANNING]);
        self::assertArrayNotHasKey(MemberBanisher::EVENT_AFTER_BANNING, $this->eventsRaised);

        Event::off(MemberBanisher::class, MemberBanisher::EVENT_BEFORE_BANNING, $beforeHandler);
        Event::off(MemberBanisher::class, MemberBanisher::EVENT_AFTER_BANNING, $afterHandler);
    }

    public function testBanShouldReturnErrorWhenEventPreventsBanning(): void
    {
        $handler = static function (BanEvent $event) {
            $event->canBan = false;
        };
        Event::on(MemberBanisher::class, MemberBanisher::EVENT_BEFORE_BANNING, $handler);

        $result = $this->service->ban($this->createMock(MemberRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(MemberBanisher::class, MemberBanisher::EVENT_BEFORE_BANNING, $handler);
    }

    public function testUnbanShouldTriggerBeforeAndAfterEventsWhenUnbanningIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[MemberBanisher::EVENT_BEFORE_UNBANNING] = $event instanceof BanEvent;
        };
        Event::on(MemberBanisher::class, MemberBanisher::EVENT_BEFORE_UNBANNING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[MemberBanisher::EVENT_AFTER_UNBANNING] = $event instanceof BanEvent
                && 101 === $event->repository->getId();
        };
        Event::on(MemberBanisher::class, MemberBanisher::EVENT_AFTER_UNBANNING, $afterHandler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('unban')->willReturn(true);
        $member->method('getId')->willReturn(101);
        $this->service->unban($member);

        self::assertTrue($this->eventsRaised[MemberBanisher::EVENT_BEFORE_UNBANNING]);
        self::assertTrue($this->eventsRaised[MemberBanisher::EVENT_AFTER_UNBANNING]);

        Event::off(MemberBanisher::class, MemberBanisher::EVENT_BEFORE_UNBANNING, $beforeHandler);
        Event::off(MemberBanisher::class, MemberBanisher::EVENT_AFTER_UNBANNING, $afterHandler);
    }

    public function testUnbanShouldOnlyTriggerBeforeEventWhenUnbanningErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[MemberBanisher::EVENT_BEFORE_UNBANNING] = true;
        };
        Event::on(MemberBanisher::class, MemberBanisher::EVENT_BEFORE_UNBANNING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MemberBanisher::EVENT_AFTER_UNBANNING] = true;
        };
        Event::on(MemberBanisher::class, MemberBanisher::EVENT_AFTER_UNBANNING, $afterHandler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('unban')->willReturn(false);
        $this->service->unban($member);

        self::assertTrue($this->eventsRaised[MemberBanisher::EVENT_BEFORE_UNBANNING]);
        self::assertArrayNotHasKey(MemberBanisher::EVENT_AFTER_UNBANNING, $this->eventsRaised);

        Event::off(MemberBanisher::class, MemberBanisher::EVENT_BEFORE_UNBANNING, $beforeHandler);
        Event::off(MemberBanisher::class, MemberBanisher::EVENT_AFTER_UNBANNING, $afterHandler);
    }

    public function testUnbanShouldReturnErrorWhenEventPreventsUnbanning(): void
    {
        $handler = static function (BanEvent $event) {
            $event->canUnban = false;
        };
        Event::on(MemberBanisher::class, MemberBanisher::EVENT_BEFORE_UNBANNING, $handler);

        $result = $this->service->unban($this->createMock(MemberRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(MemberBanisher::class, MemberBanisher::EVENT_BEFORE_UNBANNING, $handler);
    }
}
