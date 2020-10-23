<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Permit;

use Exception;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Interfaces\RoleRepositoryInterface;
use Podium\Api\Services\Permit\RoleBuilder;
use Podium\Tests\AppTestCase;

class RoleBuilderTest extends AppTestCase
{
    private RoleBuilder $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RoleBuilder();
    }

    public function testCreateShouldReturnErrorWhenCreatingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $role = $this->createMock(RoleRepositoryInterface::class);
        $role->method('create')->willReturn(false);
        $role->method('getErrors')->willReturn([1]);
        $result = $this->service->create($role);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testCreateShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->create($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Role must be instance of Podium\Api\Interfaces\RoleRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testCreateShouldReturnSuccessWhenCreatingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $role = $this->createMock(RoleRepositoryInterface::class);
        $role->method('create')->willReturn(true);
        $result = $this->service->create($role);

        self::assertTrue($result->getResult());
    }

    public function testCreateShouldReturnErrorWhenCreatingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while creating role' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $role = $this->createMock(RoleRepositoryInterface::class);
        $role->method('create')->willThrowException(new Exception('exc'));
        $result = $this->service->create($role);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testEditShouldReturnErrorWhenEditingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $role = $this->createMock(RoleRepositoryInterface::class);
        $role->method('edit')->willReturn(false);
        $role->method('getErrors')->willReturn([1]);
        $result = $this->service->edit($role);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testEditShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->edit($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Role must be instance of Podium\Api\Interfaces\RoleRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testEditShouldReturnSuccessWhenEditingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $role = $this->createMock(RoleRepositoryInterface::class);
        $role->method('edit')->willReturn(true);
        $result = $this->service->edit($role);

        self::assertTrue($result->getResult());
    }

    public function testEditShouldReturnErrorWhenEditingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while editing role' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $role = $this->createMock(RoleRepositoryInterface::class);
        $role->method('edit')->willThrowException(new Exception('exc'));
        $result = $this->service->edit($role);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
