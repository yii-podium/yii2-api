<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Category;

use Podium\Api\Events\BuildEvent;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Services\Category\CategoryBuilder;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class CategoryBuilderTest extends AppTestCase
{
    private CategoryBuilder $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CategoryBuilder();
        $this->eventsRaised = [];
    }

    public function testCreateShouldTriggerBeforeAndAfterEventsWhenCreatingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[CategoryBuilder::EVENT_BEFORE_CREATING] = $event instanceof BuildEvent;
        };
        Event::on(CategoryBuilder::class, CategoryBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[CategoryBuilder::EVENT_AFTER_CREATING] = $event instanceof BuildEvent
                && 99 === $event->repository->getId();
        };
        Event::on(CategoryBuilder::class, CategoryBuilder::EVENT_AFTER_CREATING, $afterHandler);

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('create')->willReturn(true);
        $category->method('getId')->willReturn(99);
        $this->service->create($category, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($this->eventsRaised[CategoryBuilder::EVENT_BEFORE_CREATING]);
        self::assertTrue($this->eventsRaised[CategoryBuilder::EVENT_AFTER_CREATING]);

        Event::off(CategoryBuilder::class, CategoryBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        Event::off(CategoryBuilder::class, CategoryBuilder::EVENT_AFTER_CREATING, $afterHandler);
    }

    public function testCreateShouldOnlyTriggerBeforeEventWhenCreatingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[CategoryBuilder::EVENT_BEFORE_CREATING] = true;
        };
        Event::on(CategoryBuilder::class, CategoryBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[CategoryBuilder::EVENT_AFTER_CREATING] = true;
        };
        Event::on(CategoryBuilder::class, CategoryBuilder::EVENT_AFTER_CREATING, $afterHandler);

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('create')->willReturn(false);
        $this->service->create($category, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($this->eventsRaised[CategoryBuilder::EVENT_BEFORE_CREATING]);
        self::assertArrayNotHasKey(CategoryBuilder::EVENT_AFTER_CREATING, $this->eventsRaised);

        Event::off(CategoryBuilder::class, CategoryBuilder::EVENT_BEFORE_CREATING, $beforeHandler);
        Event::off(CategoryBuilder::class, CategoryBuilder::EVENT_AFTER_CREATING, $afterHandler);
    }

    public function testCreateShouldReturnErrorWhenEventPreventsCreating(): void
    {
        $handler = static function (BuildEvent $event) {
            $event->canCreate = false;
        };
        Event::on(CategoryBuilder::class, CategoryBuilder::EVENT_BEFORE_CREATING, $handler);

        $result = $this->service->create(
            $this->createMock(CategoryRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(CategoryBuilder::class, CategoryBuilder::EVENT_BEFORE_CREATING, $handler);
    }

    public function testEditShouldTriggerBeforeAndAfterEventsWhenEditingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[CategoryBuilder::EVENT_BEFORE_EDITING] = $event instanceof BuildEvent;
        };
        Event::on(CategoryBuilder::class, CategoryBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[CategoryBuilder::EVENT_AFTER_EDITING] = $event instanceof BuildEvent
                && 101 === $event->repository->getId();
        };
        Event::on(CategoryBuilder::class, CategoryBuilder::EVENT_AFTER_EDITING, $afterHandler);

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('edit')->willReturn(true);
        $category->method('getId')->willReturn(101);
        $this->service->edit($category);

        self::assertTrue($this->eventsRaised[CategoryBuilder::EVENT_BEFORE_EDITING]);
        self::assertTrue($this->eventsRaised[CategoryBuilder::EVENT_AFTER_EDITING]);

        Event::off(CategoryBuilder::class, CategoryBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        Event::off(CategoryBuilder::class, CategoryBuilder::EVENT_AFTER_EDITING, $afterHandler);
    }

    public function testEditShouldOnlyTriggerBeforeEventWhenEditingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[CategoryBuilder::EVENT_BEFORE_EDITING] = true;
        };
        Event::on(CategoryBuilder::class, CategoryBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[CategoryBuilder::EVENT_AFTER_EDITING] = true;
        };
        Event::on(CategoryBuilder::class, CategoryBuilder::EVENT_AFTER_EDITING, $afterHandler);

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('edit')->willReturn(false);
        $this->service->edit($category);

        self::assertTrue($this->eventsRaised[CategoryBuilder::EVENT_BEFORE_EDITING]);
        self::assertArrayNotHasKey(CategoryBuilder::EVENT_AFTER_EDITING, $this->eventsRaised);

        Event::off(CategoryBuilder::class, CategoryBuilder::EVENT_BEFORE_EDITING, $beforeHandler);
        Event::off(CategoryBuilder::class, CategoryBuilder::EVENT_AFTER_EDITING, $afterHandler);
    }

    public function testEditShouldReturnErrorWhenEventPreventsEditing(): void
    {
        $handler = static function (BuildEvent $event) {
            $event->canEdit = false;
        };
        Event::on(CategoryBuilder::class, CategoryBuilder::EVENT_BEFORE_EDITING, $handler);

        $result = $this->service->edit($this->createMock(CategoryRepositoryInterface::class));
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(CategoryBuilder::class, CategoryBuilder::EVENT_BEFORE_EDITING, $handler);
    }
}
