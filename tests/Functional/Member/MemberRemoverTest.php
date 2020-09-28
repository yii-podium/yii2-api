<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Member;

use Podium\Api\Events\RemoveEvent;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Services\Member\MemberRemover;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class MemberRemoverTest extends AppTestCase
{
    private MemberRemover $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MemberRemover();
        $this->eventsRaised = [];
    }

    public function testRemoveShouldTriggerBeforeAndAfterEventsWhenRemovingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[MemberRemover::EVENT_BEFORE_REMOVING] = $event instanceof RemoveEvent;
        };
        Event::on(MemberRemover::class, MemberRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MemberRemover::EVENT_AFTER_REMOVING] = true;
        };
        Event::on(MemberRemover::class, MemberRemover::EVENT_AFTER_REMOVING, $afterHandler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('delete')->willReturn(true);
        $this->service->remove($member);

        self::assertTrue($this->eventsRaised[MemberRemover::EVENT_BEFORE_REMOVING]);
        self::assertTrue($this->eventsRaised[MemberRemover::EVENT_AFTER_REMOVING]);

        Event::off(MemberRemover::class, MemberRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        Event::off(MemberRemover::class, MemberRemover::EVENT_AFTER_REMOVING, $afterHandler);
    }

    public function testRemoveShouldOnlyTriggerBeforeEventWhenRemovingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[MemberRemover::EVENT_BEFORE_REMOVING] = true;
        };
        Event::on(MemberRemover::class, MemberRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MemberRemover::EVENT_AFTER_REMOVING] = true;
        };
        Event::on(MemberRemover::class, MemberRemover::EVENT_AFTER_REMOVING, $afterHandler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('delete')->willReturn(false);
        $this->service->remove($member);

        self::assertTrue($this->eventsRaised[MemberRemover::EVENT_BEFORE_REMOVING]);
        self::assertArrayNotHasKey(MemberRemover::EVENT_AFTER_REMOVING, $this->eventsRaised);

        Event::off(MemberRemover::class, MemberRemover::EVENT_BEFORE_REMOVING, $beforeHandler);
        Event::off(MemberRemover::class, MemberRemover::EVENT_AFTER_REMOVING, $afterHandler);
    }

    public function testRemoveShouldReturnErrorWhenEventPreventsRemoving(): void
    {
        $handler = static function (RemoveEvent $event) {
            $event->canRemove = false;
        };
        Event::on(MemberRemover::class, MemberRemover::EVENT_BEFORE_REMOVING, $handler);

        $result = $this->service->remove($this->createMock(MemberRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(MemberRemover::class, MemberRemover::EVENT_BEFORE_REMOVING, $handler);
    }
}
