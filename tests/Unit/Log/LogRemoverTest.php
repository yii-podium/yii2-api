<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Log;

use Exception;
use Podium\Api\Interfaces\LogRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Log\LogRemover;
use Podium\Tests\AppTestCase;

class LogRemoverTest extends AppTestCase
{
    private LogRemover $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LogRemover();
    }

    public function testRemoveShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->remove($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Log must be instance of Podium\Api\Interfaces\LogRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
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
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while deleting log' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $log = $this->createMock(LogRepositoryInterface::class);
        $log->method('delete')->willThrowException(new Exception('exc'));
        $result = $this->service->remove($log);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
