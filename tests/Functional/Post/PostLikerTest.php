<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Post;

use Podium\Api\Events\ThumbEvent;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\ThumbRepositoryInterface;
use Podium\Api\Services\Post\PostLiker;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class PostLikerTest extends AppTestCase
{
    private PostLiker $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PostLiker();
        $this->eventsRaised = [];
    }

    public function testThumbUpShouldTriggerBeforeAndAfterEventsWhenUpIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[PostLiker::EVENT_BEFORE_THUMB_UP] = $event instanceof ThumbEvent;
        };
        Event::on(PostLiker::class, PostLiker::EVENT_BEFORE_THUMB_UP, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[PostLiker::EVENT_AFTER_THUMB_UP] = $event instanceof ThumbEvent
                && $event->repository instanceof ThumbRepositoryInterface;
        };
        Event::on(PostLiker::class, PostLiker::EVENT_AFTER_THUMB_UP, $afterHandler);

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('up')->willReturn(true);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('isUp')->willReturn(false);
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('updateCounters')->willReturn(true);
        $this->service->thumbUp($thumb, $post, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($this->eventsRaised[PostLiker::EVENT_BEFORE_THUMB_UP]);
        self::assertTrue($this->eventsRaised[PostLiker::EVENT_AFTER_THUMB_UP]);

        Event::off(PostLiker::class, PostLiker::EVENT_BEFORE_THUMB_UP, $beforeHandler);
        Event::off(PostLiker::class, PostLiker::EVENT_AFTER_THUMB_UP, $afterHandler);
    }

    public function testThumbUpShouldOnlyTriggerBeforeEventWhenUpErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[PostLiker::EVENT_BEFORE_THUMB_UP] = true;
        };
        Event::on(PostLiker::class, PostLiker::EVENT_BEFORE_THUMB_UP, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[PostLiker::EVENT_AFTER_THUMB_UP] = true;
        };
        Event::on(PostLiker::class, PostLiker::EVENT_AFTER_THUMB_UP, $afterHandler);

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('up')->willReturn(false);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('isUp')->willReturn(false);
        $this->service->thumbUp(
            $thumb,
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertTrue($this->eventsRaised[PostLiker::EVENT_BEFORE_THUMB_UP]);
        self::assertArrayNotHasKey(PostLiker::EVENT_AFTER_THUMB_UP, $this->eventsRaised);

        Event::off(PostLiker::class, PostLiker::EVENT_BEFORE_THUMB_UP, $beforeHandler);
        Event::off(PostLiker::class, PostLiker::EVENT_AFTER_THUMB_UP, $afterHandler);
    }

    public function testThumbUpShouldOnlyTriggerBeforeEventWhenIsUpIsTrue(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[PostLiker::EVENT_BEFORE_THUMB_UP] = true;
        };
        Event::on(PostLiker::class, PostLiker::EVENT_BEFORE_THUMB_UP, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[PostLiker::EVENT_AFTER_THUMB_UP] = true;
        };
        Event::on(PostLiker::class, PostLiker::EVENT_AFTER_THUMB_UP, $afterHandler);

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('isUp')->willReturn(true);
        $this->service->thumbUp(
            $thumb,
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertTrue($this->eventsRaised[PostLiker::EVENT_BEFORE_THUMB_UP]);
        self::assertArrayNotHasKey(PostLiker::EVENT_AFTER_THUMB_UP, $this->eventsRaised);

        Event::off(PostLiker::class, PostLiker::EVENT_BEFORE_THUMB_UP, $beforeHandler);
        Event::off(PostLiker::class, PostLiker::EVENT_AFTER_THUMB_UP, $afterHandler);
    }

    public function testThumbUpShouldReturnErrorWhenEventPreventsUp(): void
    {
        $handler = static function (ThumbEvent $event) {
            $event->canThumbUp = false;
        };
        Event::on(PostLiker::class, PostLiker::EVENT_BEFORE_THUMB_UP, $handler);

        $result = $this->service->thumbUp(
            $this->createMock(ThumbRepositoryInterface::class),
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(PostLiker::class, PostLiker::EVENT_BEFORE_THUMB_UP, $handler);
    }

    public function testThumbDownShouldTriggerBeforeAndAfterEventsWhenDownIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[PostLiker::EVENT_BEFORE_THUMB_DOWN] = $event instanceof ThumbEvent;
        };
        Event::on(PostLiker::class, PostLiker::EVENT_BEFORE_THUMB_DOWN, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[PostLiker::EVENT_AFTER_THUMB_DOWN] = $event instanceof ThumbEvent
                && $event->repository instanceof ThumbRepositoryInterface;
        };
        Event::on(PostLiker::class, PostLiker::EVENT_AFTER_THUMB_DOWN, $afterHandler);

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('down')->willReturn(true);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('isDown')->willReturn(false);
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('updateCounters')->willReturn(true);
        $this->service->thumbDown($thumb, $post, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($this->eventsRaised[PostLiker::EVENT_BEFORE_THUMB_DOWN]);
        self::assertTrue($this->eventsRaised[PostLiker::EVENT_AFTER_THUMB_DOWN]);

        Event::off(PostLiker::class, PostLiker::EVENT_BEFORE_THUMB_DOWN, $beforeHandler);
        Event::off(PostLiker::class, PostLiker::EVENT_AFTER_THUMB_DOWN, $afterHandler);
    }

    public function testThumbDownShouldOnlyTriggerBeforeEventWhenDownErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[PostLiker::EVENT_BEFORE_THUMB_DOWN] = true;
        };
        Event::on(PostLiker::class, PostLiker::EVENT_BEFORE_THUMB_DOWN, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[PostLiker::EVENT_AFTER_THUMB_DOWN] = true;
        };
        Event::on(PostLiker::class, PostLiker::EVENT_AFTER_THUMB_DOWN, $afterHandler);

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('down')->willReturn(false);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('isDown')->willReturn(false);
        $this->service->thumbDown(
            $thumb,
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertTrue($this->eventsRaised[PostLiker::EVENT_BEFORE_THUMB_DOWN]);
        self::assertArrayNotHasKey(PostLiker::EVENT_AFTER_THUMB_DOWN, $this->eventsRaised);

        Event::off(PostLiker::class, PostLiker::EVENT_BEFORE_THUMB_DOWN, $beforeHandler);
        Event::off(PostLiker::class, PostLiker::EVENT_AFTER_THUMB_DOWN, $afterHandler);
    }

    public function testThumbDownShouldOnlyTriggerBeforeEventWhenIsDownIsTrue(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[PostLiker::EVENT_BEFORE_THUMB_DOWN] = true;
        };
        Event::on(PostLiker::class, PostLiker::EVENT_BEFORE_THUMB_DOWN, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[PostLiker::EVENT_AFTER_THUMB_DOWN] = true;
        };
        Event::on(PostLiker::class, PostLiker::EVENT_AFTER_THUMB_DOWN, $afterHandler);

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('isDown')->willReturn(true);
        $this->service->thumbDown(
            $thumb,
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertTrue($this->eventsRaised[PostLiker::EVENT_BEFORE_THUMB_DOWN]);
        self::assertArrayNotHasKey(PostLiker::EVENT_AFTER_THUMB_DOWN, $this->eventsRaised);

        Event::off(PostLiker::class, PostLiker::EVENT_BEFORE_THUMB_DOWN, $beforeHandler);
        Event::off(PostLiker::class, PostLiker::EVENT_AFTER_THUMB_DOWN, $afterHandler);
    }

    public function testThumbDownShouldReturnErrorWhenEventPreventsDown(): void
    {
        $handler = static function (ThumbEvent $event) {
            $event->canThumbDown = false;
        };
        Event::on(PostLiker::class, PostLiker::EVENT_BEFORE_THUMB_DOWN, $handler);

        $result = $this->service->thumbDown(
            $this->createMock(ThumbRepositoryInterface::class),
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(PostLiker::class, PostLiker::EVENT_BEFORE_THUMB_DOWN, $handler);
    }

    public function testThumbResetShouldTriggerBeforeAndAfterEventsWhenResetIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[PostLiker::EVENT_BEFORE_THUMB_RESET] = $event instanceof ThumbEvent;
        };
        Event::on(PostLiker::class, PostLiker::EVENT_BEFORE_THUMB_RESET, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[PostLiker::EVENT_AFTER_THUMB_RESET] = true;
        };
        Event::on(PostLiker::class, PostLiker::EVENT_AFTER_THUMB_RESET, $afterHandler);

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('reset')->willReturn(true);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('isUp')->willReturn(true);
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('updateCounters')->willReturn(true);
        $this->service->thumbReset($thumb, $post, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($this->eventsRaised[PostLiker::EVENT_BEFORE_THUMB_RESET]);
        self::assertTrue($this->eventsRaised[PostLiker::EVENT_AFTER_THUMB_RESET]);

        Event::off(PostLiker::class, PostLiker::EVENT_BEFORE_THUMB_RESET, $beforeHandler);
        Event::off(PostLiker::class, PostLiker::EVENT_AFTER_THUMB_RESET, $afterHandler);
    }

    public function testThumbResetShouldOnlyTriggerBeforeEventWhenResetErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[PostLiker::EVENT_BEFORE_THUMB_RESET] = true;
        };
        Event::on(PostLiker::class, PostLiker::EVENT_BEFORE_THUMB_RESET, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[PostLiker::EVENT_AFTER_THUMB_RESET] = true;
        };
        Event::on(PostLiker::class, PostLiker::EVENT_AFTER_THUMB_RESET, $afterHandler);

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('reset')->willReturn(false);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('isUp')->willReturn(true);
        $this->service->thumbReset(
            $thumb,
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertTrue($this->eventsRaised[PostLiker::EVENT_BEFORE_THUMB_RESET]);
        self::assertArrayNotHasKey(PostLiker::EVENT_AFTER_THUMB_RESET, $this->eventsRaised);

        Event::off(PostLiker::class, PostLiker::EVENT_BEFORE_THUMB_RESET, $beforeHandler);
        Event::off(PostLiker::class, PostLiker::EVENT_AFTER_THUMB_RESET, $afterHandler);
    }

    public function testThumbResetShouldReturnErrorWhenEventPreventsReset(): void
    {
        $handler = static function (ThumbEvent $event) {
            $event->canThumbReset = false;
        };
        Event::on(PostLiker::class, PostLiker::EVENT_BEFORE_THUMB_RESET, $handler);

        $result = $this->service->thumbReset(
            $this->createMock(ThumbRepositoryInterface::class),
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(PostLiker::class, PostLiker::EVENT_BEFORE_THUMB_RESET, $handler);
    }
}
