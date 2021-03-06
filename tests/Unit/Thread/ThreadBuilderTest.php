<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Thread;

use Exception;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\Services\Thread\ThreadBuilder;
use Podium\Tests\AppTestCase;

class ThreadBuilderTest extends AppTestCase
{
    private ThreadBuilder $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ThreadBuilder();
    }

    public function testCreateShouldReturnErrorWhenThreadRepositoryIsWrong(): void
    {
        $result = $this->service->create(
            $this->createMock(RepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class),
            $this->createMock(ForumRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame(
            'Thread must be instance of Podium\Api\Interfaces\ThreadRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testCreateShouldReturnErrorWhenForumRepositoryIsWrong(): void
    {
        $result = $this->service->create(
            $this->createMock(ThreadRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class),
            $this->createMock(RepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame(
            'Forum must be instance of Podium\Api\Interfaces\ForumRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testCreateShouldReturnErrorWhenCreatingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(false);
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('create')->willReturn(false);
        $thread->method('getErrors')->willReturn([1]);
        $result = $this->service->create($thread, $author, $this->createMock(ForumRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testCreateShouldReturnErrorWhenAuthorIsBanned(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(true);
        $result = $this->service->create(
            $this->createMock(ThreadRepositoryInterface::class),
            $author,
            $this->createMock(ForumRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame(['api' => 'member.banned'], $result->getErrors());
    }

    public function testCreateShouldReturnSuccessWhenCreatingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(false);
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('create')->willReturn(true);
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('updateCounters')->with(1, 0)->willReturn(true);
        $result = $this->service->create($thread, $author, $forum);

        self::assertTrue($result->getResult());
    }

    public function testCreateShouldReturnErrorWhenCreatingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while creating thread' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(false);
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('create')->willThrowException(new Exception('exc'));
        $result = $this->service->create($thread, $author, $this->createMock(ForumRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testCreateShouldReturnErrorWhenUpdatingForumCountersErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while creating thread' === $data[0]
                        && 'Error while updating forum counters!' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(false);
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('create')->willReturn(true);
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('updateCounters')->willReturn(false);
        $result = $this->service->create($thread, $author, $forum);

        self::assertFalse($result->getResult());
        self::assertSame('Error while updating forum counters!', $result->getErrors()['exception']->getMessage());
    }

    public function testEditShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->edit($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Thread must be instance of Podium\Api\Interfaces\ThreadRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testEditShouldReturnErrorWhenEditingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('edit')->willReturn(false);
        $thread->method('getErrors')->willReturn([1]);
        $result = $this->service->edit($thread);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testEditShouldReturnSuccessWhenEditingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('edit')->willReturn(true);
        $result = $this->service->edit($thread);

        self::assertTrue($result->getResult());
    }

    public function testEditShouldReturnErrorWhenEditingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while editing thread' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('edit')->willThrowException(new Exception('exc'));
        $result = $this->service->edit($thread);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
