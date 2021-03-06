<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Log;

use Exception;
use Podium\Api\Interfaces\LogRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Services\Log\LogBuilder;
use Podium\Tests\AppTestCase;

class LogBuilderTest extends AppTestCase
{
    private LogBuilder $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LogBuilder();
    }

    public function testCreateShouldReturnErrorWhenCreatingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(false);
        $log = $this->createMock(LogRepositoryInterface::class);
        $log->method('create')->willReturn(false);
        $log->method('getErrors')->willReturn([1]);
        $result = $this->service->create($log, $author, 'action');

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testCreateShouldReturnErrorWhenAuthorIsBanned(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(true);
        $result = $this->service->create($this->createMock(LogRepositoryInterface::class), $author, 'action');

        self::assertFalse($result->getResult());
        self::assertSame(['api' => 'member.banned'], $result->getErrors());
    }

    public function testCreateShouldReturnSuccessWhenCreatingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(false);
        $log = $this->createMock(LogRepositoryInterface::class);
        $log->method('create')->willReturn(true);
        $result = $this->service->create($log, $author, 'action');

        self::assertTrue($result->getResult());
    }

    public function testCreateShouldReturnErrorWhenCreatingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while creating log' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(false);
        $log = $this->createMock(LogRepositoryInterface::class);
        $log->method('create')->willThrowException(new Exception('exc'));
        $result = $this->service->create($log, $author, 'action');

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
