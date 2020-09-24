<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Forum;

use Exception;
use PHPUnit\Framework\TestCase;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Forum\ForumArchiver;

class ForumArchiverTest extends TestCase
{
    private ForumArchiver $service;

    protected function setUp(): void
    {
        $this->service = new ForumArchiver();
    }

    public function testBeforeArchiveShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeArchive());
    }

    public function testArchiveShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->archive($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testArchiveShouldReturnErrorWhenArchivingErrored(): void
    {
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('archive')->willReturn(false);
        $forum->method('getErrors')->willReturn([1]);
        $result = $this->service->archive($forum);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testArchiveShouldReturnSuccessWhenArchivingIsDone(): void
    {
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('archive')->willReturn(true);
        $result = $this->service->archive($forum);

        self::assertTrue($result->getResult());
    }

    public function testArchiveShouldReturnErrorWhenArchivingThrowsException(): void
    {
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('archive')->willThrowException(new Exception('exc'));
        $result = $this->service->archive($forum);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testBeforeReviveShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeRevive());
    }

    public function testReviveShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->revive($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testReviveShouldReturnErrorWhenRevivingErrored(): void
    {
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('revive')->willReturn(false);
        $forum->method('getErrors')->willReturn([1]);
        $result = $this->service->revive($forum);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testReviveShouldReturnSuccessWhenRevivingIsDone(): void
    {
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('revive')->willReturn(true);
        $result = $this->service->revive($forum);

        self::assertTrue($result->getResult());
    }

    public function testReviveShouldReturnErrorWhenRevivingThrowsException(): void
    {
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('revive')->willThrowException(new Exception('exc'));
        $result = $this->service->revive($forum);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
