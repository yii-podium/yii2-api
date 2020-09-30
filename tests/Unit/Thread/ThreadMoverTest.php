<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Thread;

use Exception;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\Services\Thread\ThreadMover;
use Podium\Tests\AppTestCase;

class ThreadMoverTest extends AppTestCase
{
    private ThreadMover $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ThreadMover();
    }

    public function testBeforeMoveShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeMove());
    }

    public function testMoveShouldReturnErrorWhenThreadRepositoryIsWrong(): void
    {
        $result = $this->service->move(
            $this->createMock(RepositoryInterface::class),
            $this->createMock(ForumRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testMoveShouldReturnErrorWhenForumRepositoryIsWrong(): void
    {
        $result = $this->service->move(
            $this->createMock(ThreadRepositoryInterface::class),
            $this->createMock(RepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testMoveShouldReturnErrorWhenMovingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('move')->willReturn(false);
        $result = $this->service->move($thread, $this->createMock(ForumRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testMoveShouldReturnSuccessWhenMovingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('move')->willReturn(true);
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('updateCounters')->willReturn(true);
        $thread->method('getParent')->willReturn($forum);
        $thread->method('updateCounters')->willReturn(true);
        $result = $this->service->move($thread, $forum);

        self::assertTrue($result->getResult());
    }

    public function testMoveShouldReturnErrorWhenMovingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('move')->willThrowException(new Exception('exc'));
        $result = $this->service->move($thread, $this->createMock(ForumRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testMoveShouldReturnErrorWhenUpdatingOldForumCountersErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('move')->willReturn(true);
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('updateCounters')->willReturn(false);
        $thread->method('getParent')->willReturn($forum);
        $result = $this->service->move($thread, $forum);

        self::assertFalse($result->getResult());
        self::assertSame(
            'Error while updating old forum counters!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testMoveShouldReturnErrorWhenUpdatingNewForumCountersErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('move')->willReturn(true);
        $oldForum = $this->createMock(ForumRepositoryInterface::class);
        $oldForum->method('updateCounters')->willReturn(true);
        $thread->method('getParent')->willReturn($oldForum);
        $newForum = $this->createMock(ForumRepositoryInterface::class);
        $newForum->method('updateCounters')->willReturn(false);
        $result = $this->service->move($thread, $newForum);

        self::assertFalse($result->getResult());
        self::assertSame(
            'Error while updating new forum counters!',
            $result->getErrors()['exception']->getMessage()
        );
    }
}
