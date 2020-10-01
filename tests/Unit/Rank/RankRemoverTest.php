<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Rank;

use Exception;
use Podium\Api\Interfaces\RankRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Rank\RankRemover;
use Podium\Tests\AppTestCase;

class RankRemoverTest extends AppTestCase
{
    private RankRemover $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RankRemover();
    }

    public function testRemoveShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->remove($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Rank must be instance of Podium\Api\Interfaces\RankRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testRemoveShouldReturnErrorWhenRemovingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $rank = $this->createMock(RankRepositoryInterface::class);
        $rank->method('delete')->willReturn(false);
        $result = $this->service->remove($rank);

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testRemoveShouldReturnSuccessWhenRemovingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $rank = $this->createMock(RankRepositoryInterface::class);
        $rank->method('delete')->willReturn(true);
        $result = $this->service->remove($rank);

        self::assertTrue($result->getResult());
    }

    public function testRemoveShouldReturnErrorWhenRemovingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while deleting rank' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $rank = $this->createMock(RankRepositoryInterface::class);
        $rank->method('delete')->willThrowException(new Exception('exc'));
        $result = $this->service->remove($rank);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
