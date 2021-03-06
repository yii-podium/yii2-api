<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Thread;

use Exception;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\Services\Thread\ThreadRemover;
use Podium\Tests\AppTestCase;

class ThreadRemoverTest extends AppTestCase
{
    private ThreadRemover $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ThreadRemover();
    }

    public function testRemoveShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->remove($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Thread must be instance of Podium\Api\Interfaces\ThreadRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testRemoveShouldReturnErrorWhenRemovingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isArchived')->willReturn(true);
        $thread->method('delete')->willReturn(false);
        $result = $this->service->remove($thread);

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testRemoveShouldReturnErrorWhenThreadIsNotArchived(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isArchived')->willReturn(false);
        $thread->method('delete')->willReturn(true);
        $result = $this->service->remove($thread);

        self::assertFalse($result->getResult());
        self::assertSame('thread.must.be.archived', $result->getErrors()['api']);
    }

    public function testRemoveShouldReturnSuccessWhenRemovingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isArchived')->willReturn(true);
        $thread->method('delete')->willReturn(true);
        $thread->method('getPostsCount')->willReturn(1);
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('updateCounters')->with(-1, -1)->willReturn(true);
        $thread->method('getParent')->willReturn($forum);
        $result = $this->service->remove($thread);

        self::assertTrue($result->getResult());
    }

    public function testRemoveShouldReturnErrorWhenRemovingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while deleting thread' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isArchived')->willReturn(true);
        $thread->method('delete')->willThrowException(new Exception('exc'));
        $result = $this->service->remove($thread);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testRemoveShouldReturnErrorWhenIsArchivedThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while deleting thread' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isArchived')->willThrowException(new Exception('exc'));
        $result = $this->service->remove($thread);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testRemoveShouldReturnErrorWhenUpdatingForumCountersErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while deleting thread' === $data[0]
                        && 'Error while updating forum counters!' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isArchived')->willReturn(true);
        $thread->method('delete')->willReturn(true);
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('updateCounters')->willReturn(false);
        $thread->method('getParent')->willReturn($forum);
        $result = $this->service->remove($thread);

        self::assertFalse($result->getResult());
        self::assertSame('Error while updating forum counters!', $result->getErrors()['exception']->getMessage());
    }
}
