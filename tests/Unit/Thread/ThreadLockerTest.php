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

    public function testBeforeLockShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeLock());
    }

    public function testLockShouldReturnErrorWhenLockingErrored(): void
    {
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('lock')->willReturn(false);
        $thread->method('getErrors')->willReturn([1]);
        $result = $this->service->lock($thread);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testLockShouldReturnSuccessWhenLockingIsDone(): void
    {
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('lock')->willReturn(true);
        $result = $this->service->lock($thread);

        self::assertTrue($result->getResult());
    }

    public function testLockShouldReturnErrorWhenLockingThrowsException(): void
    {
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('lock')->willThrowException(new Exception('exc'));
        $result = $this->service->lock($thread);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testBeforeUnlockShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeUnlock());
    }

    public function testUnlockShouldReturnErrorWhenUnlockingErrored(): void
    {
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('unlock')->willReturn(false);
        $thread->method('getErrors')->willReturn([1]);
        $result = $this->service->unlock($thread);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testUnlockShouldReturnSuccessWhenUnlockingIsDone(): void
    {
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('unlock')->willReturn(true);
        $result = $this->service->unlock($thread);

        self::assertTrue($result->getResult());
    }

    public function testUnlockShouldReturnErrorWhenUnlockingThrowsException(): void
    {
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('unlock')->willThrowException(new Exception('exc'));
        $result = $this->service->unlock($thread);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
