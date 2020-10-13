<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Category;

use Podium\Api\Events\HideEvent;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Services\Category\CategoryHider;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class CategoryHiderTest extends AppTestCase
{
    private CategoryHider $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CategoryHider();
        $this->eventsRaised = [];
    }

    public function testHideShouldTriggerBeforeAndAfterEventsWhenHidingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[CategoryHider::EVENT_BEFORE_HIDING] = $event instanceof HideEvent;
        };
        Event::on(CategoryHider::class, CategoryHider::EVENT_BEFORE_HIDING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[CategoryHider::EVENT_AFTER_HIDING] = $event instanceof HideEvent
                && 99 === $event->repository->getId();
        };
        Event::on(CategoryHider::class, CategoryHider::EVENT_AFTER_HIDING, $afterHandler);

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('isHidden')->willReturn(false);
        $category->method('hide')->willReturn(true);
        $category->method('getId')->willReturn(99);
        $this->service->hide($category);

        self::assertTrue($this->eventsRaised[CategoryHider::EVENT_BEFORE_HIDING]);
        self::assertTrue($this->eventsRaised[CategoryHider::EVENT_AFTER_HIDING]);

        Event::off(CategoryHider::class, CategoryHider::EVENT_BEFORE_HIDING, $beforeHandler);
        Event::off(CategoryHider::class, CategoryHider::EVENT_AFTER_HIDING, $afterHandler);
    }

    public function testHideShouldOnlyTriggerBeforeEventWhenHidingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[CategoryHider::EVENT_BEFORE_HIDING] = true;
        };
        Event::on(CategoryHider::class, CategoryHider::EVENT_BEFORE_HIDING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[CategoryHider::EVENT_AFTER_HIDING] = true;
        };
        Event::on(CategoryHider::class, CategoryHider::EVENT_AFTER_HIDING, $afterHandler);

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('isHidden')->willReturn(false);
        $category->method('hide')->willReturn(false);
        $this->service->hide($category);

        self::assertTrue($this->eventsRaised[CategoryHider::EVENT_BEFORE_HIDING]);
        self::assertArrayNotHasKey(CategoryHider::EVENT_AFTER_HIDING, $this->eventsRaised);

        Event::off(CategoryHider::class, CategoryHider::EVENT_BEFORE_HIDING, $beforeHandler);
        Event::off(CategoryHider::class, CategoryHider::EVENT_AFTER_HIDING, $afterHandler);
    }

    public function testHideShouldOnlyTriggerBeforeEventWhenCategoryIsAlreadyHidden(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[CategoryHider::EVENT_BEFORE_HIDING] = true;
        };
        Event::on(CategoryHider::class, CategoryHider::EVENT_BEFORE_HIDING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[CategoryHider::EVENT_AFTER_HIDING] = true;
        };
        Event::on(CategoryHider::class, CategoryHider::EVENT_AFTER_HIDING, $afterHandler);

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('isHidden')->willReturn(true);
        $this->service->hide($category);

        self::assertTrue($this->eventsRaised[CategoryHider::EVENT_BEFORE_HIDING]);
        self::assertArrayNotHasKey(CategoryHider::EVENT_AFTER_HIDING, $this->eventsRaised);

        Event::off(CategoryHider::class, CategoryHider::EVENT_BEFORE_HIDING, $beforeHandler);
        Event::off(CategoryHider::class, CategoryHider::EVENT_AFTER_HIDING, $afterHandler);
    }

    public function testHideShouldReturnErrorWhenEventPreventsHiding(): void
    {
        $handler = static function (HideEvent $event) {
            $event->canHide = false;
        };
        Event::on(CategoryHider::class, CategoryHider::EVENT_BEFORE_HIDING, $handler);

        $result = $this->service->hide($this->createMock(CategoryRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(CategoryHider::class, CategoryHider::EVENT_BEFORE_HIDING, $handler);
    }

    public function testRevealShouldTriggerBeforeAndAfterEventsWhenRevivingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[CategoryHider::EVENT_BEFORE_REVEALING] = $event instanceof HideEvent;
        };
        Event::on(CategoryHider::class, CategoryHider::EVENT_BEFORE_REVEALING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[CategoryHider::EVENT_AFTER_REVEALING] = $event instanceof HideEvent
                && 101 === $event->repository->getId();
        };
        Event::on(CategoryHider::class, CategoryHider::EVENT_AFTER_REVEALING, $afterHandler);

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('isHidden')->willReturn(true);
        $category->method('reveal')->willReturn(true);
        $category->method('getId')->willReturn(101);
        $this->service->reveal($category);

        self::assertTrue($this->eventsRaised[CategoryHider::EVENT_BEFORE_REVEALING]);
        self::assertTrue($this->eventsRaised[CategoryHider::EVENT_AFTER_REVEALING]);

        Event::off(CategoryHider::class, CategoryHider::EVENT_BEFORE_REVEALING, $beforeHandler);
        Event::off(CategoryHider::class, CategoryHider::EVENT_AFTER_REVEALING, $afterHandler);
    }

    public function testRevealShouldOnlyTriggerBeforeEventWhenRevealingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[CategoryHider::EVENT_BEFORE_REVEALING] = true;
        };
        Event::on(CategoryHider::class, CategoryHider::EVENT_BEFORE_REVEALING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[CategoryHider::EVENT_AFTER_REVEALING] = true;
        };
        Event::on(CategoryHider::class, CategoryHider::EVENT_AFTER_REVEALING, $afterHandler);

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('isHidden')->willReturn(true);
        $category->method('reveal')->willReturn(false);
        $this->service->reveal($category);

        self::assertTrue($this->eventsRaised[CategoryHider::EVENT_BEFORE_REVEALING]);
        self::assertArrayNotHasKey(CategoryHider::EVENT_AFTER_REVEALING, $this->eventsRaised);

        Event::off(CategoryHider::class, CategoryHider::EVENT_BEFORE_REVEALING, $beforeHandler);
        Event::off(CategoryHider::class, CategoryHider::EVENT_AFTER_REVEALING, $afterHandler);
    }

    public function testRevealShouldOnlyTriggerBeforeEventWhenCategoryIsNotHidden(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[CategoryHider::EVENT_BEFORE_REVEALING] = true;
        };
        Event::on(CategoryHider::class, CategoryHider::EVENT_BEFORE_REVEALING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[CategoryHider::EVENT_AFTER_REVEALING] = true;
        };
        Event::on(CategoryHider::class, CategoryHider::EVENT_AFTER_REVEALING, $afterHandler);

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('isHidden')->willReturn(false);
        $this->service->reveal($category);

        self::assertTrue($this->eventsRaised[CategoryHider::EVENT_BEFORE_REVEALING]);
        self::assertArrayNotHasKey(CategoryHider::EVENT_AFTER_REVEALING, $this->eventsRaised);

        Event::off(CategoryHider::class, CategoryHider::EVENT_BEFORE_REVEALING, $beforeHandler);
        Event::off(CategoryHider::class, CategoryHider::EVENT_AFTER_REVEALING, $afterHandler);
    }

    public function testRevealShouldReturnErrorWhenEventPreventsRevealing(): void
    {
        $handler = static function (HideEvent $event) {
            $event->canReveal = false;
        };
        Event::on(CategoryHider::class, CategoryHider::EVENT_BEFORE_REVEALING, $handler);

        $result = $this->service->reveal($this->createMock(CategoryRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(CategoryHider::class, CategoryHider::EVENT_BEFORE_REVEALING, $handler);
    }
}
