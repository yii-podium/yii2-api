<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Post;

use Exception;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\Services\Post\PostMover;
use Podium\Tests\AppTestCase;

class PostMoverTest extends AppTestCase
{
    private PostMover $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PostMover();
    }

    public function testMoveShouldReturnErrorWhenPostRepositoryIsWrong(): void
    {
        $result = $this->service->move(
            $this->createMock(RepositoryInterface::class),
            $this->createMock(ThreadRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testMoveShouldReturnErrorWhenThreadRepositoryIsWrong(): void
    {
        $result = $this->service->move(
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(RepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testMoveShouldReturnErrorWhenMovingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('move')->willReturn(false);
        $result = $this->service->move($post, $this->createMock(ThreadRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testMoveShouldReturnSuccessWhenMovingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('move')->willReturn(true);
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('updateCounters')->willReturn(true);
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('updateCounters')->willReturn(true);
        $thread->method('getParent')->willReturn($forum);
        $post->method('getParent')->willReturn($thread);
        $result = $this->service->move($post, $thread);

        self::assertTrue($result->getResult());
    }

    public function testMoveShouldReturnErrorWhenMovingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while moving post' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('move')->willThrowException(new Exception('exc'));
        $result = $this->service->move($post, $this->createMock(ThreadRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testMoveShouldReturnErrorWhenUpdatingOldThreadCountersErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while moving post' === $data[0]
                        && 'Error while updating old thread counters!' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('move')->willReturn(true);
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('updateCounters')->willReturn(false);
        $post->method('getParent')->willReturn($thread);
        $result = $this->service->move($post, $thread);

        self::assertFalse($result->getResult());
        self::assertSame(
            'Error while updating old thread counters!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testMoveShouldReturnErrorWhenUpdatingOldForumCountersErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while moving post' === $data[0]
                        && 'Error while updating old forum counters!' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('move')->willReturn(true);
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('updateCounters')->willReturn(true);
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('updateCounters')->willReturn(false);
        $thread->method('getParent')->willReturn($forum);
        $post->method('getParent')->willReturn($thread);
        $result = $this->service->move($post, $thread);

        self::assertFalse($result->getResult());
        self::assertSame(
            'Error while updating old forum counters!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testMoveShouldReturnErrorWhenUpdatingNewThreadCountersErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while moving post' === $data[0]
                        && 'Error while updating new thread counters!' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('move')->willReturn(true);
        $oldThread = $this->createMock(ThreadRepositoryInterface::class);
        $oldThread->method('updateCounters')->willReturn(true);
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('updateCounters')->willReturn(true);
        $oldThread->method('getParent')->willReturn($forum);
        $post->method('getParent')->willReturn($oldThread);

        $newThread = $this->createMock(ThreadRepositoryInterface::class);
        $newThread->method('updateCounters')->willReturn(false);

        $result = $this->service->move($post, $newThread);

        self::assertFalse($result->getResult());
        self::assertSame(
            'Error while updating new thread counters!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testMoveShouldReturnErrorWhenUpdatingNewForumCountersErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while moving post' === $data[0]
                        && 'Error while updating new forum counters!' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('move')->willReturn(true);
        $oldThread = $this->createMock(ThreadRepositoryInterface::class);
        $oldThread->method('updateCounters')->willReturn(true);
        $oldForum = $this->createMock(ForumRepositoryInterface::class);
        $oldForum->method('updateCounters')->willReturn(true);
        $oldThread->method('getParent')->willReturn($oldForum);
        $post->method('getParent')->willReturn($oldThread);

        $newThread = $this->createMock(ThreadRepositoryInterface::class);
        $newThread->method('updateCounters')->willReturn(true);
        $newForum = $this->createMock(ForumRepositoryInterface::class);
        $newForum->method('updateCounters')->willReturn(false);
        $newThread->method('getParent')->willReturn($newForum);

        $result = $this->service->move($post, $newThread);

        self::assertFalse($result->getResult());
        self::assertSame(
            'Error while updating new forum counters!',
            $result->getErrors()['exception']->getMessage()
        );
    }
}
