<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Thread;

use Exception;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\Services\Thread\ThreadArchiver;
use Podium\Tests\AppTestCase;

class ThreadArchiverTest extends AppTestCase
{
    private ThreadArchiver $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ThreadArchiver();
    }

    public function testArchiveShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->archive($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testArchiveShouldReturnErrorWhenArchivingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isArchived')->willReturn(false);
        $thread->method('archive')->willReturn(false);
        $thread->method('getErrors')->willReturn([1]);
        $result = $this->service->archive($thread);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testArchiveShouldReturnErrorWhenThreadIsAlreadyArchived(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isArchived')->willReturn(true);
        $result = $this->service->archive($thread);

        self::assertFalse($result->getResult());
        self::assertSame('thread.already.archived', $result->getErrors()['api']);
    }

    public function testArchiveShouldReturnSuccessWhenArchivingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isArchived')->willReturn(false);
        $thread->method('archive')->willReturn(true);
        $result = $this->service->archive($thread);

        self::assertTrue($result->getResult());
    }

    public function testArchiveShouldReturnErrorWhenArchivingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while archiving thread' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isArchived')->willReturn(false);
        $thread->method('archive')->willThrowException(new Exception('exc'));
        $result = $this->service->archive($thread);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testReviveShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->revive($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testReviveShouldReturnErrorWhenRevivingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isArchived')->willReturn(true);
        $thread->method('revive')->willReturn(false);
        $thread->method('getErrors')->willReturn([1]);
        $result = $this->service->revive($thread);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testReviveShouldReturnSuccessWhenRevivingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isArchived')->willReturn(true);
        $thread->method('revive')->willReturn(true);
        $result = $this->service->revive($thread);

        self::assertTrue($result->getResult());
    }

    public function testReviveShouldReturnErrorWhenRevivingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while reviving thread' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('isArchived')->willReturn(true);
        $thread->method('revive')->willThrowException(new Exception('exc'));
        $result = $this->service->revive($thread);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
