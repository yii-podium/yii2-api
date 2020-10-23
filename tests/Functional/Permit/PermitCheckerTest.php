<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Permit;

use Podium\Api\Enums\Decision;
use Podium\Api\Events\PermitEvent;
use Podium\Api\Interfaces\DeciderInterface;
use Podium\Api\PodiumDecision;
use Podium\Api\Services\Permit\PermitChecker;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class PermitCheckerTest extends AppTestCase
{
    private PermitChecker $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PermitChecker();
        $this->eventsRaised = [];
    }

    public function testCheckShouldTriggerBeforeAndAfterEventsWhenCheckingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[PermitChecker::EVENT_BEFORE_CHECKING] = $event instanceof PermitEvent;
        };
        Event::on(PermitChecker::class, PermitChecker::EVENT_BEFORE_CHECKING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[PermitChecker::EVENT_AFTER_CHECKING] = true;
        };
        Event::on(PermitChecker::class, PermitChecker::EVENT_AFTER_CHECKING, $afterHandler);

        $decider = $this->createMock(DeciderInterface::class);
        $decider->method('decide')->willReturn(PodiumDecision::allow());
        $this->service->check($decider, '');

        self::assertTrue($this->eventsRaised[PermitChecker::EVENT_BEFORE_CHECKING]);
        self::assertTrue($this->eventsRaised[PermitChecker::EVENT_AFTER_CHECKING]);

        Event::off(PermitChecker::class, PermitChecker::EVENT_BEFORE_CHECKING, $beforeHandler);
        Event::off(PermitChecker::class, PermitChecker::EVENT_AFTER_CHECKING, $afterHandler);
    }

    public function testCheckShouldDenyWhenEventPreventsChecking(): void
    {
        $handler = static function (PermitEvent $event) {
            $event->canCheck = false;
        };
        Event::on(PermitChecker::class, PermitChecker::EVENT_BEFORE_CHECKING, $handler);

        $result = $this->service->check($this->createMock(DeciderInterface::class), '');
        self::assertSame(Decision::DENY, $result->getDecision());

        Event::off(PermitChecker::class, PermitChecker::EVENT_BEFORE_CHECKING, $handler);
    }
}
