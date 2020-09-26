<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Rank;

use Exception;
use PHPUnit\Framework\TestCase;
use Podium\Api\Interfaces\RankRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Rank\RankRemover;

class RankRemoverTest extends TestCase
{
    private RankRemover $service;

    protected function setUp(): void
    {
        $this->service = new RankRemover();
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
        $rank = $this->createMock(RankRepositoryInterface::class);
        $rank->method('delete')->willReturn(false);
        $result = $this->service->remove($rank);

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testRemoveShouldReturnSuccessWhenRemovingIsDone(): void
    {
        $rank = $this->createMock(RankRepositoryInterface::class);
        $rank->method('delete')->willReturn(true);
        $result = $this->service->remove($rank);

        self::assertTrue($result->getResult());
    }

    public function testRemoveShouldReturnErrorWhenRemovingThrowsException(): void
    {
        $rank = $this->createMock(RankRepositoryInterface::class);
        $rank->method('delete')->willThrowException(new Exception('exc'));
        $result = $this->service->remove($rank);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
