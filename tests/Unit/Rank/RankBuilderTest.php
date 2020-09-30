<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Rank;

use Exception;
use Podium\Api\Interfaces\RankRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Rank\RankBuilder;
use Podium\Tests\AppTestCase;

class RankBuilderTest extends AppTestCase
{
    private RankBuilder $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RankBuilder();
    }

    public function testCreateShouldReturnErrorWhenCreatingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

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
        $this->transaction->expects(self::once())->method('commit');

        $rank = $this->createMock(RankRepositoryInterface::class);
        $rank->method('create')->willReturn(true);
        $result = $this->service->create($rank);

        self::assertTrue($result->getResult());
    }

    public function testCreateShouldReturnErrorWhenCreatingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $rank = $this->createMock(RankRepositoryInterface::class);
        $rank->method('create')->willThrowException(new Exception('exc'));
        $result = $this->service->create($rank);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testEditShouldReturnErrorWhenEditingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

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
        $this->transaction->expects(self::once())->method('commit');

        $rank = $this->createMock(RankRepositoryInterface::class);
        $rank->method('edit')->willReturn(true);
        $result = $this->service->edit($rank);

        self::assertTrue($result->getResult());
    }

    public function testEditShouldReturnErrorWhenEditingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $rank = $this->createMock(RankRepositoryInterface::class);
        $rank->method('edit')->willThrowException(new Exception('exc'));
        $result = $this->service->edit($rank);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
