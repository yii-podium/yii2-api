<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Thread;

use Exception;
use Podium\Api\Interfaces\BookmarkRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\Services\Thread\ThreadBookmarker;
use Podium\Tests\AppTestCase;

class ThreadBookmarkerTest extends AppTestCase
{
    private ThreadBookmarker $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ThreadBookmarker();
    }

    public function testMarkShouldReturnTrueIfMarkingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $bookmark = $this->createMock(BookmarkRepositoryInterface::class);
        $bookmark->method('fetchOne')->willReturn(true);
        $bookmark->expects(self::never())->method('prepare');
        $bookmark->method('getLastSeen')->willReturn(1);
        $bookmark->method('mark')->willReturn(true);

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('getParent')->willReturn($this->createMock(ThreadRepositoryInterface::class));
        $post->method('getCreatedAt')->willReturn(2);

        $result = $this->service->mark($bookmark, $post, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($result->getResult());
    }

    public function testMarkShouldReturnTrueIfBookmarkIsSeenAfterPostCreation(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $bookmark = $this->createMock(BookmarkRepositoryInterface::class);
        $bookmark->method('fetchOne')->willReturn(true);
        $bookmark->expects(self::never())->method('prepare');
        $bookmark->method('getLastSeen')->willReturn(2);
        $bookmark->expects(self::never())->method('mark');

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('getParent')->willReturn($this->createMock(ThreadRepositoryInterface::class));
        $post->method('getCreatedAt')->willReturn(1);

        $result = $this->service->mark($bookmark, $post, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($result->getResult());
    }

    public function testMarkShouldReturnTrueIfBookmarkIsSeenAtTheTimeOfPostCreation(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $bookmark = $this->createMock(BookmarkRepositoryInterface::class);
        $bookmark->method('fetchOne')->willReturn(true);
        $bookmark->expects(self::never())->method('prepare');
        $bookmark->method('getLastSeen')->willReturn(2);
        $bookmark->expects(self::never())->method('mark');

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('getParent')->willReturn($this->createMock(ThreadRepositoryInterface::class));
        $post->method('getCreatedAt')->willReturn(2);

        $result = $this->service->mark($bookmark, $post, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($result->getResult());
    }

    public function testMarkShouldPrepareBookmarkWhenItDoesntExist(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $bookmark = $this->createMock(BookmarkRepositoryInterface::class);
        $bookmark->method('fetchOne')->willReturn(false);
        $bookmark->expects(self::once())->method('prepare');
        $bookmark->method('getLastSeen')->willReturn(1);
        $bookmark->method('mark')->willReturn(true);

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('getParent')->willReturn($this->createMock(ThreadRepositoryInterface::class));
        $post->method('getCreatedAt')->willReturn(2);

        $result = $this->service->mark($bookmark, $post, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($result->getResult());
    }

    public function testMarkShouldReturnErrorWhenMarkingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $bookmark = $this->createMock(BookmarkRepositoryInterface::class);
        $bookmark->method('fetchOne')->willReturn(true);
        $bookmark->method('getLastSeen')->willReturn(1);
        $bookmark->method('mark')->willReturn(false);
        $bookmark->method('getErrors')->willReturn([3]);

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('getParent')->willReturn($this->createMock(ThreadRepositoryInterface::class));
        $post->method('getCreatedAt')->willReturn(2);

        $result = $this->service->mark($bookmark, $post, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame([3], $result->getErrors());
    }

    public function testMarkShouldReturnErrorWhenMarkingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while bookmarking thread' === $data[0]
                        && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $bookmark = $this->createMock(BookmarkRepositoryInterface::class);
        $bookmark->method('fetchOne')->willReturn(true);
        $bookmark->expects(self::never())->method('prepare');
        $bookmark->method('getLastSeen')->willReturn(1);
        $bookmark->method('mark')->willThrowException(new Exception('exc'));

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('getParent')->willReturn($this->createMock(ThreadRepositoryInterface::class));
        $post->method('getCreatedAt')->willReturn(2);

        $result = $this->service->mark($bookmark, $post, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
