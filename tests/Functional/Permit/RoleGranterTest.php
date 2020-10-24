<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Permit;

use Podium\Api\Events\GrantEvent;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RoleRepositoryInterface;
use Podium\Api\Services\Permit\RoleGranter;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class RoleGranterTest extends AppTestCase
{
    private RoleGranter $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RoleGranter();
        $this->eventsRaised = [];
    }

    public function testGrantShouldTriggerBeforeAndAfterEventsWhenGrantingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[RoleGranter::EVENT_BEFORE_GRANTING] = $event instanceof GrantEvent;
        };
        Event::on(RoleGranter::class, RoleGranter::EVENT_BEFORE_GRANTING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[RoleGranter::EVENT_AFTER_GRANTING] = $event instanceof GrantEvent
                && 99 === $event->repository->getId();
        };
        Event::on(RoleGranter::class, RoleGranter::EVENT_AFTER_GRANTING, $afterHandler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('hasRole')->willReturn(false);
        $member->method('addRole')->willReturn(true);
        $member->method('getId')->willReturn(99);
        $this->service->grant($member, $this->createMock(RoleRepositoryInterface::class));

        self::assertTrue($this->eventsRaised[RoleGranter::EVENT_BEFORE_GRANTING]);
        self::assertTrue($this->eventsRaised[RoleGranter::EVENT_AFTER_GRANTING]);

        Event::off(RoleGranter::class, RoleGranter::EVENT_BEFORE_GRANTING, $beforeHandler);
        Event::off(RoleGranter::class, RoleGranter::EVENT_AFTER_GRANTING, $afterHandler);
    }

    public function testGrantShouldOnlyTriggerBeforeEventWhenGrantingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[RoleGranter::EVENT_BEFORE_GRANTING] = true;
        };
        Event::on(RoleGranter::class, RoleGranter::EVENT_BEFORE_GRANTING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[RoleGranter::EVENT_AFTER_GRANTING] = true;
        };
        Event::on(RoleGranter::class, RoleGranter::EVENT_AFTER_GRANTING, $afterHandler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('hasRole')->willReturn(false);
        $member->method('addRole')->willReturn(false);
        $this->service->grant($member, $this->createMock(RoleRepositoryInterface::class));

        self::assertTrue($this->eventsRaised[RoleGranter::EVENT_BEFORE_GRANTING]);
        self::assertArrayNotHasKey(RoleGranter::EVENT_AFTER_GRANTING, $this->eventsRaised);

        Event::off(RoleGranter::class, RoleGranter::EVENT_BEFORE_GRANTING, $beforeHandler);
        Event::off(RoleGranter::class, RoleGranter::EVENT_AFTER_GRANTING, $afterHandler);
    }

    public function testGrantShouldOnlyTriggerBeforeEventWhenRoleIsAlreadyGranted(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[RoleGranter::EVENT_BEFORE_GRANTING] = true;
        };
        Event::on(RoleGranter::class, RoleGranter::EVENT_BEFORE_GRANTING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[RoleGranter::EVENT_AFTER_GRANTING] = true;
        };
        Event::on(RoleGranter::class, RoleGranter::EVENT_AFTER_GRANTING, $afterHandler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('hasRole')->willReturn(true);
        $this->service->grant($member, $this->createMock(RoleRepositoryInterface::class));

        self::assertTrue($this->eventsRaised[RoleGranter::EVENT_BEFORE_GRANTING]);
        self::assertArrayNotHasKey(RoleGranter::EVENT_AFTER_GRANTING, $this->eventsRaised);

        Event::off(RoleGranter::class, RoleGranter::EVENT_BEFORE_GRANTING, $beforeHandler);
        Event::off(RoleGranter::class, RoleGranter::EVENT_AFTER_GRANTING, $afterHandler);
    }

    public function testGrantShouldReturnErrorWhenEventPreventsGranting(): void
    {
        $handler = static function (GrantEvent $event) {
            $event->canGrant = false;
        };
        Event::on(RoleGranter::class, RoleGranter::EVENT_BEFORE_GRANTING, $handler);

        $result = $this->service->grant(
            $this->createMock(MemberRepositoryInterface::class),
            $this->createMock(RoleRepositoryInterface::class)
        );
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(RoleGranter::class, RoleGranter::EVENT_BEFORE_GRANTING, $handler);
    }

    public function testRevokeShouldTriggerBeforeAndAfterEventsWhenRevokingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[RoleGranter::EVENT_BEFORE_REVOKING] = $event instanceof GrantEvent;
        };
        Event::on(RoleGranter::class, RoleGranter::EVENT_BEFORE_REVOKING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[RoleGranter::EVENT_AFTER_REVOKING] = $event instanceof GrantEvent
                && 101 === $event->repository->getId();
        };
        Event::on(RoleGranter::class, RoleGranter::EVENT_AFTER_REVOKING, $afterHandler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('hasRole')->willReturn(true);
        $member->method('removeRole')->willReturn(true);
        $member->method('getId')->willReturn(101);
        $this->service->revoke($member, $this->createMock(RoleRepositoryInterface::class));

        self::assertTrue($this->eventsRaised[RoleGranter::EVENT_BEFORE_REVOKING]);
        self::assertTrue($this->eventsRaised[RoleGranter::EVENT_AFTER_REVOKING]);

        Event::off(RoleGranter::class, RoleGranter::EVENT_BEFORE_REVOKING, $beforeHandler);
        Event::off(RoleGranter::class, RoleGranter::EVENT_AFTER_REVOKING, $afterHandler);
    }

    public function testRevokeShouldOnlyTriggerBeforeEventWhenRevokingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[RoleGranter::EVENT_BEFORE_REVOKING] = true;
        };
        Event::on(RoleGranter::class, RoleGranter::EVENT_BEFORE_REVOKING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[RoleGranter::EVENT_AFTER_REVOKING] = true;
        };
        Event::on(RoleGranter::class, RoleGranter::EVENT_AFTER_REVOKING, $afterHandler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('hasRole')->willReturn(true);
        $member->method('removeRole')->willReturn(false);
        $this->service->revoke($member, $this->createMock(RoleRepositoryInterface::class));

        self::assertTrue($this->eventsRaised[RoleGranter::EVENT_BEFORE_REVOKING]);
        self::assertArrayNotHasKey(RoleGranter::EVENT_AFTER_REVOKING, $this->eventsRaised);

        Event::off(RoleGranter::class, RoleGranter::EVENT_BEFORE_REVOKING, $beforeHandler);
        Event::off(RoleGranter::class, RoleGranter::EVENT_AFTER_REVOKING, $afterHandler);
    }

    public function testRevokeShouldOnlyTriggerBeforeEventWhenRoleIsNotGranted(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[RoleGranter::EVENT_BEFORE_REVOKING] = true;
        };
        Event::on(RoleGranter::class, RoleGranter::EVENT_BEFORE_REVOKING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[RoleGranter::EVENT_AFTER_REVOKING] = true;
        };
        Event::on(RoleGranter::class, RoleGranter::EVENT_AFTER_REVOKING, $afterHandler);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('hasRole')->willReturn(false);
        $this->service->revoke($member, $this->createMock(RoleRepositoryInterface::class));

        self::assertTrue($this->eventsRaised[RoleGranter::EVENT_BEFORE_REVOKING]);
        self::assertArrayNotHasKey(RoleGranter::EVENT_AFTER_REVOKING, $this->eventsRaised);

        Event::off(RoleGranter::class, RoleGranter::EVENT_BEFORE_REVOKING, $beforeHandler);
        Event::off(RoleGranter::class, RoleGranter::EVENT_AFTER_REVOKING, $afterHandler);
    }

    public function testRevokeShouldReturnErrorWhenEventPreventsRevoking(): void
    {
        $handler = static function (GrantEvent $event) {
            $event->canRevoke = false;
        };
        Event::on(RoleGranter::class, RoleGranter::EVENT_BEFORE_REVOKING, $handler);

        $result = $this->service->revoke(
            $this->createMock(MemberRepositoryInterface::class),
            $this->createMock(RoleRepositoryInterface::class)
        );
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(RoleGranter::class, RoleGranter::EVENT_BEFORE_REVOKING, $handler);
    }
}
