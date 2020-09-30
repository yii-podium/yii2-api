<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Logger;

use Exception;
use Podium\Api\Interfaces\LogRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Logger\LoggerRemover;
use Podium\Tests\AppTestCase;

class LoggerRemoverTest extends AppTestCase
{
    private LoggerRemover $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LoggerRemover();
    }

    public function testBeforeRemoveShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeRemove());
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

        $log = $this->createMock(LogRepositoryInterface::class);
        $log->method('delete')->willReturn(false);
        $result = $this->service->remove($log);

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testRemoveShouldReturnSuccessWhenRemovingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $log = $this->createMock(LogRepositoryInterface::class);
        $log->method('delete')->willReturn(true);
        $result = $this->service->remove($log);

        self::assertTrue($result->getResult());
    }

    public function testRemoveShouldReturnErrorWhenRemovingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $log = $this->createMock(LogRepositoryInterface::class);
        $log->method('delete')->willThrowException(new Exception('exc'));
        $result = $this->service->remove($log);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
