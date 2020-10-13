<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Thread;

use Exception;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\Services\Thread\ThreadHider;
use Podium\Tests\AppTestCase;

use function count;

class ThreadHiderTest extends AppTestCase
{
    private ThreadHider $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ThreadHider();
    }

    public function testHideShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->hide($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Thread must be instance of Podium\Api\Interfaces\ThreadRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testHideShouldReturnErrorWhenHidingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isHidden')->willReturn(false);
        $thread->method('hide')->willReturn(false);
        $thread->method('getErrors')->willReturn([1]);
        $result = $this->service->hide($thread);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testHideShouldReturnErrorWhenThreadIsAlreadyHidden(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isHidden')->willReturn(true);
        $result = $this->service->hide($thread);

        self::assertFalse($result->getResult());
        self::assertSame('thread.already.hidden', $result->getErrors()['api']);
    }

    public function testHideShouldReturnSuccessWhenHidingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isHidden')->willReturn(false);
        $thread->method('hide')->willReturn(true);
        $result = $this->service->hide($thread);

        self::assertTrue($result->getResult());
    }

    public function testHideShouldReturnErrorWhenHidingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while hiding thread' === $data[0]
                        && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isHidden')->willReturn(false);
        $thread->method('hide')->willThrowException(new Exception('exc'));
        $result = $this->service->hide($thread);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testRevealShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->reveal($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Thread must be instance of Podium\Api\Interfaces\ThreadRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testRevealShouldReturnErrorWhenRevealingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isHidden')->willReturn(true);
        $thread->method('reveal')->willReturn(false);
        $thread->method('getErrors')->willReturn([1]);
        $result = $this->service->reveal($thread);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testRevealShouldReturnErrorWhenForumIsNotHidden(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isHidden')->willReturn(false);
        $result = $this->service->reveal($thread);

        self::assertFalse($result->getResult());
        self::assertSame('thread.not.hidden', $result->getErrors()['api']);
    }

    public function testRevealShouldReturnSuccessWhenRevealingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isHidden')->willReturn(true);
        $thread->method('reveal')->willReturn(true);
        $result = $this->service->reveal($thread);

        self::assertTrue($result->getResult());
    }

    public function testRevealShouldReturnErrorWhenRevealingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while revealing thread' === $data[0]
                        && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isHidden')->willReturn(true);
        $thread->method('reveal')->willThrowException(new Exception('exc'));
        $result = $this->service->reveal($thread);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
