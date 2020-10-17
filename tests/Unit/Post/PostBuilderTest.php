<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Post;

use Exception;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\Services\Post\PostBuilder;
use Podium\Tests\AppTestCase;

class PostBuilderTest extends AppTestCase
{
    private PostBuilder $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PostBuilder();
    }

    public function testCreateShouldReturnErrorWhenPostRepositoryIsWrong(): void
    {
        $result = $this->service->create(
            $this->createMock(RepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class),
            $this->createMock(ThreadRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame(
            'Post must be instance of Podium\Api\Interfaces\PostRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testCreateShouldReturnErrorWhenThreadRepositoryIsWrong(): void
    {
        $result = $this->service->create(
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class),
            $this->createMock(RepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame(
            'Thread must be instance of Podium\Api\Interfaces\ThreadRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testCreateShouldReturnErrorWhenCreatingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(false);
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('create')->willReturn(false);
        $post->method('getErrors')->willReturn([1]);
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isLocked')->willReturn(false);
        $result = $this->service->create($post, $author, $thread);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testCreateShouldReturnErrorWhenAuthorIsBanned(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(true);
        $result = $this->service->create(
            $this->createMock(PostRepositoryInterface::class),
            $author,
            $this->createMock(ThreadRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame(['api' => 'member.banned'], $result->getErrors());
    }

    public function testCreateShouldReturnErrorWhenThreadIsLocked(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(false);
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isLocked')->willReturn(true);
        $result = $this->service->create($this->createMock(PostRepositoryInterface::class), $author, $thread);

        self::assertFalse($result->getResult());
        self::assertSame(['api' => 'thread.locked'], $result->getErrors());
    }

    public function testCreateShouldReturnSuccessWhenCreatingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(false);
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('create')->willReturn(true);
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('updateCounters')->with(1)->willReturn(true);
        $thread->method('isLocked')->willReturn(false);
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('updateCounters')->with(0, 1)->willReturn(true);
        $thread->method('getParent')->willReturn($forum);
        $result = $this->service->create($post, $author, $thread);

        self::assertTrue($result->getResult());
    }

    public function testCreateShouldReturnErrorWhenCreatingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while creating post' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(false);
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('create')->willThrowException(new Exception('exc'));
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isLocked')->willReturn(false);
        $result = $this->service->create($post, $author, $thread);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testCreateShouldReturnErrorWhenUpdatingThreadCountersErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(false);
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('create')->willReturn(true);
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('updateCounters')->willReturn(false);
        $thread->method('isLocked')->willReturn(false);
        $result = $this->service->create($post, $author, $thread);

        self::assertFalse($result->getResult());
        self::assertSame('Error while updating thread counters!', $result->getErrors()['exception']->getMessage());
    }

    public function testCreateShouldReturnErrorWhenUpdatingForumCountersErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(false);
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('create')->willReturn(true);
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('updateCounters')->willReturn(true);
        $thread->method('isLocked')->willReturn(false);
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('updateCounters')->willReturn(false);
        $thread->method('getParent')->willReturn($forum);
        $result = $this->service->create($post, $author, $thread);

        self::assertFalse($result->getResult());
        self::assertSame('Error while updating forum counters!', $result->getErrors()['exception']->getMessage());
    }

    public function testEditShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->edit($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Post must be instance of Podium\Api\Interfaces\PostRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testEditShouldReturnErrorWhenEditingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('edit')->willReturn(false);
        $post->method('getErrors')->willReturn([1]);
        $result = $this->service->edit($post);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testEditShouldReturnSuccessWhenEditingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('edit')->willReturn(true);
        $result = $this->service->edit($post);

        self::assertTrue($result->getResult());
    }

    public function testEditShouldReturnErrorWhenEditingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while editing post' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('edit')->willThrowException(new Exception('exc'));
        $result = $this->service->edit($post);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
