<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Member;

use Podium\Api\Events\BuildEvent;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Services\Member\MemberBuilder;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class MemberBuilderTest extends AppTestCase
{
    private MemberBuilder $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MemberBuilder();
        $this->eventsRaised = [];
    }

    public function testRegisterShouldTriggerBeforeAndAfterEventsWhenRegisteringIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[MemberBuilder::EVENT_BEFORE_REGISTERING] = $event instanceof BuildEvent;
        };
        Event::on(MemberBuilder::class, MemberBuilder::EVENT_BEFORE_REGISTERING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[MemberBuilder::EVENT_AFTER_REGISTERING] = $event instanceof BuildEvent
                && 99 === $event->repository->getId();
        };
        Event::on(MemberBuilder::class, MemberBuilder::EVENT_AFTER_REGISTERING, $afterHandler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('register')->willReturn(true);
        $member->method('getId')->willReturn(99);
        $this->service->register($member, 1);

        self::assertTrue($this->eventsRaised[MemberBuilder::EVENT_BEFORE_REGISTERING]);
        self::assertTrue($this->eventsRaised[MemberBuilder::EVENT_AFTER_REGISTERING]);

        Event::off(MemberBuilder::class, MemberBuilder::EVENT_BEFORE_REGISTERING, $beforeHandler);
        Event::off(MemberBuilder::class, MemberBuilder::EVENT_AFTER_REGISTERING, $afterHandler);
    }

    public function testRegisterShouldOnlyTriggerBeforeEventWhenRegisteringErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[MemberBuilder::EVENT_BEFORE_REGISTERING] = true;
        };
        Event::on(MemberBuilder::class, MemberBuilder::EVENT_BEFORE_REGISTERING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MemberBuilder::EVENT_AFTER_REGISTERING] = true;
        };
        Event::on(MemberBuilder::class, MemberBuilder::EVENT_AFTER_REGISTERING, $afterHandler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('register')->willReturn(false);
        $this->service->register($member, 1);

        self::assertTrue($this->eventsRaised[MemberBuilder::EVENT_BEFORE_REGISTERING]);
        self::assertArrayNotHasKey(MemberBuilder::EVENT_AFTER_REGISTERING, $this->eventsRaised);

        Event::off(MemberBuilder::class, MemberBuilder::EVENT_BEFORE_REGISTERING, $beforeHandler);
        Event::off(MemberBuilder::class, MemberBuilder::EVENT_AFTER_REGISTERING, $afterHandler);
    }

    public function testRegisterShouldReturnErrorWhenEventPreventsCreating(): void
    {
        $handler = static function (BuildEvent $event) {
            $event->canCreate = false;
        };
        Event::on(MemberBuilder::class, MemberBuilder::EVENT_BEFORE_REGISTERING, $handler);

        $result = $this->service->register($this->createMock(MemberRepositoryInterface::class), 1);
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(MemberBuilder::class, MemberBuilder::EVENT_BEFORE_REGISTERING, $handler);
    }

    public function testEditShouldTriggerBeforeAndAfterEventsWhenEditingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[MemberBuilder::EVENT_BEFORE_EDITING] = $event instanceof BuildEvent;
        };
        Event::on(MemberBuilder::class, MemberBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[MemberBuilder::EVENT_AFTER_EDITING] = $event instanceof BuildEvent
                && 101 === $event->repository->getId();
        };
        Event::on(MemberBuilder::class, MemberBuilder::EVENT_AFTER_EDITING, $afterHandler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('edit')->willReturn(true);
        $member->method('getId')->willReturn(101);
        $this->service->edit($member);

        self::assertTrue($this->eventsRaised[MemberBuilder::EVENT_BEFORE_EDITING]);
        self::assertTrue($this->eventsRaised[MemberBuilder::EVENT_AFTER_EDITING]);

        Event::off(MemberBuilder::class, MemberBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        Event::off(MemberBuilder::class, MemberBuilder::EVENT_AFTER_EDITING, $afterHandler);
    }

    public function testEditShouldOnlyTriggerBeforeEventWhenEditingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[MemberBuilder::EVENT_BEFORE_EDITING] = true;
        };
        Event::on(MemberBuilder::class, MemberBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[MemberBuilder::EVENT_AFTER_EDITING] = true;
        };
        Event::on(MemberBuilder::class, MemberBuilder::EVENT_AFTER_EDITING, $afterHandler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('edit')->willReturn(false);
        $this->service->edit($member);

        self::assertTrue($this->eventsRaised[MemberBuilder::EVENT_BEFORE_EDITING]);
        self::assertArrayNotHasKey(MemberBuilder::EVENT_AFTER_EDITING, $this->eventsRaised);

        Event::off(MemberBuilder::class, MemberBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        Event::off(MemberBuilder::class, MemberBuilder::EVENT_AFTER_EDITING, $afterHandler);
    }

    public function testEditShouldReturnErrorWhenEventPreventsEditing(): void
    {
        $handler = static function (BuildEvent $event) {
            $event->canEdit = false;
        };
        Event::on(MemberBuilder::class, MemberBuilder::EVENT_BEFORE_EDITING, $handler);

        $result = $this->service->edit($this->createMock(MemberRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(MemberBuilder::class, MemberBuilder::EVENT_BEFORE_EDITING, $handler);
    }
}
