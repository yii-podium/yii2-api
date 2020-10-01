<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Post;

use Exception;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\Services\Post\PostRemover;
use Podium\Tests\AppTestCase;

class PostRemoverTest extends AppTestCase
{
    private PostRemover $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PostRemover();
    }

    public function testRemoveShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->remove($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testRemoveShouldReturnErrorWhenRemovingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('isArchived')->willReturn(true);
        $post->method('delete')->willReturn(false);
        $result = $this->service->remove($post);

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testRemoveShouldReturnErrorWhenPostIsNotArchived(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('isArchived')->willReturn(false);
        $post->method('delete')->willReturn(true);
        $result = $this->service->remove($post);

        self::assertFalse($result->getResult());
        self::assertSame('post.must.be.archived', $result->getErrors()['api']);
    }

    public function testRemoveShouldReturnSuccessWhenRemovingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('isArchived')->willReturn(true);
        $post->method('delete')->willReturn(true);
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('updateCounters')->with(-1)->willReturn(true);
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('updateCounters')->with(0, -1)->willReturn(true);
        $thread->method('getParent')->willReturn($forum);
        $post->method('getParent')->willReturn($thread);
        $result = $this->service->remove($post);

        self::assertTrue($result->getResult());
    }

    public function testRemoveShouldReturnErrorWhenRemovingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while deleting post' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('isArchived')->willReturn(true);
        $post->method('delete')->willThrowException(new Exception('exc'));
        $result = $this->service->remove($post);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testRemoveShouldReturnErrorWhenIsArchivedThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while deleting post' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('isArchived')->willThrowException(new Exception('exc'));
        $result = $this->service->remove($post);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testRemoveShouldReturnErrorWhenUpdatingThreadCountersErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while deleting post' === $data[0]
                        && 'Error while updating thread counters!' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('isArchived')->willReturn(true);
        $post->method('delete')->willReturn(true);
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('updateCounters')->willReturn(false);
        $post->method('getParent')->willReturn($thread);
        $result = $this->service->remove($post);

        self::assertFalse($result->getResult());
        self::assertSame('Error while updating thread counters!', $result->getErrors()['exception']->getMessage());
    }

    public function testRemoveShouldReturnErrorWhenUpdatingForumCountersErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while deleting post' === $data[0]
                        && 'Error while updating forum counters!' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('isArchived')->willReturn(true);
        $post->method('delete')->willReturn(true);
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('updateCounters')->willReturn(true);
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('updateCounters')->willReturn(false);
        $thread->method('getParent')->willReturn($forum);
        $post->method('getParent')->willReturn($thread);
        $result = $this->service->remove($post);

        self::assertFalse($result->getResult());
        self::assertSame('Error while updating forum counters!', $result->getErrors()['exception']->getMessage());
    }
}
