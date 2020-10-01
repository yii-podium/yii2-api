<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Thread;

use Exception;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\Services\Thread\ThreadLocker;
use Podium\Tests\AppTestCase;

class ThreadLockerTest extends AppTestCase
{
    private ThreadLocker $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ThreadLocker();
    }

    public function testLockShouldReturnErrorWhenLockingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('lock')->willReturn(false);
        $thread->method('getErrors')->willReturn([1]);
        $result = $this->service->lock($thread);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testLockShouldReturnSuccessWhenLockingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('lock')->willReturn(true);
        $result = $this->service->lock($thread);

        self::assertTrue($result->getResult());
    }

    public function testLockShouldReturnErrorWhenLockingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while locking thread' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('lock')->willThrowException(new Exception('exc'));
        $result = $this->service->lock($thread);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testUnlockShouldReturnErrorWhenUnlockingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('unlock')->willReturn(false);
        $thread->method('getErrors')->willReturn([1]);
        $result = $this->service->unlock($thread);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testUnlockShouldReturnSuccessWhenUnlockingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('unlock')->willReturn(true);
        $result = $this->service->unlock($thread);

        self::assertTrue($result->getResult());
    }

    public function testUnlockShouldReturnErrorWhenUnlockingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while unlocking thread' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('unlock')->willThrowException(new Exception('exc'));
        $result = $this->service->unlock($thread);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
