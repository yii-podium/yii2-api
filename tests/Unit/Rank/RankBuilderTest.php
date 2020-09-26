<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Rank;

use Exception;
use PHPUnit\Framework\TestCase;
use Podium\Api\Interfaces\RankRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Rank\RankBuilder;

class RankBuilderTest extends TestCase
{
    private RankBuilder $service;

    protected function setUp(): void
    {
        $this->service = new RankBuilder();
    }

    public function testBeforeCreateShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeCreate());
    }

    public function testCreateShouldReturnErrorWhenCreatingErrored(): void
    {
        $rank = $this->createMock(RankRepositoryInterface::class);
        $rank->method('create')->willReturn(false);
        $rank->method('getErrors')->willReturn([1]);
        $result = $this->service->create($rank);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testCreateShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->create($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testCreateShouldReturnSuccessWhenCreatingIsDone(): void
    {
        $rank = $this->createMock(RankRepositoryInterface::class);
        $rank->method('create')->willReturn(true);
        $result = $this->service->create($rank);

        self::assertTrue($result->getResult());
    }

    public function testCreateShouldReturnErrorWhenCreatingThrowsException(): void
    {
        $rank = $this->createMock(RankRepositoryInterface::class);
        $rank->method('create')->willThrowException(new Exception('exc'));
        $result = $this->service->create($rank);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testBeforeEditShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeEdit());
    }

    public function testEditShouldReturnErrorWhenEditingErrored(): void
    {
        $rank = $this->createMock(RankRepositoryInterface::class);
        $rank->method('edit')->willReturn(false);
        $rank->method('getErrors')->willReturn([1]);
        $result = $this->service->edit($rank);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testEditShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->edit($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testEditShouldReturnSuccessWhenEditingIsDone(): void
    {
        $rank = $this->createMock(RankRepositoryInterface::class);
        $rank->method('edit')->willReturn(true);
        $result = $this->service->edit($rank);

        self::assertTrue($result->getResult());
    }

    public function testEditShouldReturnErrorWhenEditingThrowsException(): void
    {
        $rank = $this->createMock(RankRepositoryInterface::class);
        $rank->method('edit')->willThrowException(new Exception('exc'));
        $result = $this->service->edit($rank);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
