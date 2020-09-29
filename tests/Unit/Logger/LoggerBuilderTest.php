<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Logger;

use Exception;
use Podium\Api\Interfaces\LogRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Services\Logger\LoggerBuilder;
use Podium\Tests\AppTestCase;

class LoggerBuilderTest extends AppTestCase
{
    private LoggerBuilder $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LoggerBuilder();
    }

    public function testBeforeCreateShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeCreate());
    }

    public function testCreateShouldReturnErrorWhenCreatingErrored(): void
    {
        $log = $this->createMock(LogRepositoryInterface::class);
        $log->method('create')->willReturn(false);
        $log->method('getErrors')->willReturn([1]);
        $result = $this->service->create($log, $this->createMock(MemberRepositoryInterface::class), 'action');

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testCreateShouldReturnSuccessWhenCreatingIsDone(): void
    {
        $log = $this->createMock(LogRepositoryInterface::class);
        $log->method('create')->willReturn(true);
        $result = $this->service->create($log, $this->createMock(MemberRepositoryInterface::class), 'action');

        self::assertTrue($result->getResult());
    }

    public function testCreateShouldReturnErrorWhenCreatingThrowsException(): void
    {
        $log = $this->createMock(LogRepositoryInterface::class);
        $log->method('create')->willThrowException(new Exception('exc'));
        $result = $this->service->create($log, $this->createMock(MemberRepositoryInterface::class), 'action');

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
