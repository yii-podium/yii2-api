<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Group;

use Exception;
use Podium\Api\Interfaces\GroupRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Group\GroupBuilder;
use Podium\Tests\AppTestCase;

class GroupBuilderTest extends AppTestCase
{
    private GroupBuilder $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GroupBuilder();
    }

    public function testCreateShouldReturnErrorWhenCreatingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('create')->willReturn(false);
        $group->method('getErrors')->willReturn([1]);
        $result = $this->service->create($group);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testCreateShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->create($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Group must be instance of Podium\Api\Interfaces\GroupRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testCreateShouldReturnSuccessWhenCreatingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('create')->willReturn(true);
        $result = $this->service->create($group);

        self::assertTrue($result->getResult());
    }

    public function testCreateShouldReturnErrorWhenCreatingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while creating group' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('create')->willThrowException(new Exception('exc'));
        $result = $this->service->create($group);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testEditShouldReturnErrorWhenEditingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('edit')->willReturn(false);
        $group->method('getErrors')->willReturn([1]);
        $result = $this->service->edit($group);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testEditShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->edit($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Group must be instance of Podium\Api\Interfaces\GroupRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testEditShouldReturnSuccessWhenEditingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('edit')->willReturn(true);
        $result = $this->service->edit($group);

        self::assertTrue($result->getResult());
    }

    public function testEditShouldReturnErrorWhenEditingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while editing group' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $rank = $this->createMock(GroupRepositoryInterface::class);
        $rank->method('edit')->willThrowException(new Exception('exc'));
        $result = $this->service->edit($rank);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
