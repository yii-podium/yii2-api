<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Permit;

use Exception;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Interfaces\RoleRepositoryInterface;
use Podium\Api\Services\Permit\RoleRemover;
use Podium\Tests\AppTestCase;

class RoleRemoverTest extends AppTestCase
{
    private RoleRemover $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RoleRemover();
    }

    public function testRemoveShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->remove($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Role must be instance of Podium\Api\Interfaces\RoleRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testRemoveShouldReturnErrorWhenRemovingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $role = $this->createMock(RoleRepositoryInterface::class);
        $role->method('delete')->willReturn(false);
        $result = $this->service->remove($role);

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testRemoveShouldReturnSuccessWhenRemovingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $role = $this->createMock(RoleRepositoryInterface::class);
        $role->method('delete')->willReturn(true);
        $result = $this->service->remove($role);

        self::assertTrue($result->getResult());
    }

    public function testRemoveShouldReturnErrorWhenRemovingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while deleting role' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $role = $this->createMock(RoleRepositoryInterface::class);
        $role->method('delete')->willThrowException(new Exception('exc'));
        $result = $this->service->remove($role);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
