<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Group;

use Exception;
use Podium\Api\Interfaces\GroupRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Group\GroupKeeper;
use Podium\Tests\AppTestCase;

class GroupKeeperTest extends AppTestCase
{
    private GroupKeeper $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GroupKeeper();
    }

    public function testJoinShouldReturnErrorWhenRepositoryAlreadyJoined(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $repository = $this->createMock(RepositoryInterface::class);
        $repository->method('hasGroups')->willReturn(true);
        $result = $this->service->join($this->createMock(GroupRepositoryInterface::class), $repository);

        self::assertFalse($result->getResult());
        self::assertSame('group.already.joined', $result->getErrors()['api']);
    }

    public function testJoinShouldReturnErrorWhenAddingGroupRepositoryErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $repository = $this->createMock(RepositoryInterface::class);
        $repository->method('hasGroups')->willReturn(false);
        $repository->method('join')->willReturn(false);
        $repository->method('getErrors')->willReturn([1]);
        $result = $this->service->join($this->createMock(GroupRepositoryInterface::class), $repository);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testJoinShouldReturnSuccessWhenAddingGroupRepositoryIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $repository = $this->createMock(RepositoryInterface::class);
        $repository->method('hasGroups')->willReturn(false);
        $repository->method('join')->willReturn(true);
        $result = $this->service->join($this->createMock(GroupRepositoryInterface::class), $repository);

        self::assertTrue($result->getResult());
    }

    public function testJoinShouldReturnErrorWhenAddingGroupRepositoryThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while joining group' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $repository = $this->createMock(RepositoryInterface::class);
        $repository->method('hasGroups')->willReturn(false);
        $repository->method('join')->willThrowException(new Exception('exc'));
        $result = $this->service->join($this->createMock(GroupRepositoryInterface::class), $repository);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testLeaveShouldReturnErrorWhenRepositoryNotJoinedBefore(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $repository = $this->createMock(RepositoryInterface::class);
        $repository->method('hasGroups')->willReturn(false);
        $result = $this->service->leave($this->createMock(GroupRepositoryInterface::class), $repository);

        self::assertFalse($result->getResult());
        self::assertSame('group.not.joined', $result->getErrors()['api']);
    }

    public function testLeaveShouldReturnErrorWhenRemovingGroupRepositoryErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $repository = $this->createMock(RepositoryInterface::class);
        $repository->method('hasGroups')->willReturn(true);
        $repository->method('leave')->willReturn(false);
        $repository->method('getErrors')->willReturn([1]);
        $result = $this->service->leave($this->createMock(GroupRepositoryInterface::class), $repository);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testLeaveShouldReturnSuccessWhenRemovingGroupRepositoryIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $repository = $this->createMock(RepositoryInterface::class);
        $repository->method('hasGroups')->willReturn(true);
        $repository->method('leave')->willReturn(true);
        $result = $this->service->leave($this->createMock(GroupRepositoryInterface::class), $repository);

        self::assertTrue($result->getResult());
    }

    public function testLeaveShouldReturnErrorWhenRemovingGroupRepositoryThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while leaving group' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $repository = $this->createMock(RepositoryInterface::class);
        $repository->method('hasGroups')->willReturn(true);
        $repository->method('leave')->willThrowException(new Exception('exc'));
        $result = $this->service->leave($this->createMock(GroupRepositoryInterface::class), $repository);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
