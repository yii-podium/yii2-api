<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Permit;

use Exception;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RoleRepositoryInterface;
use Podium\Api\Services\Permit\RoleGranter;
use Podium\Tests\AppTestCase;

use function count;

class RoleGranterTest extends AppTestCase
{
    private RoleGranter $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RoleGranter();
    }

    public function testGrantShouldReturnErrorWhenGrantingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('hasRole')->willReturn(false);
        $member->method('addRole')->willReturn(false);
        $member->method('getErrors')->willReturn([1]);
        $result = $this->service->grant($member, $this->createMock(RoleRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testGrantShouldReturnErrorWhenRoleIsAlreadyGranted(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('hasRole')->willReturn(true);
        $result = $this->service->grant($member, $this->createMock(RoleRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('role.already.granted', $result->getErrors()['api']);
    }

    public function testGrantShouldReturnSuccessWhenGrantingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('hasRole')->willReturn(false);
        $member->method('addRole')->willReturn(true);
        $result = $this->service->grant($member, $this->createMock(RoleRepositoryInterface::class));

        self::assertTrue($result->getResult());
    }

    public function testGrantShouldReturnErrorWhenGrantingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while granting role' === $data[0]
                        && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('hasRole')->willReturn(false);
        $member->method('addRole')->willThrowException(new Exception('exc'));
        $result = $this->service->grant($member, $this->createMock(RoleRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testRevokeShouldReturnErrorWhenRevokingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('hasRole')->willReturn(true);
        $member->method('removeRole')->willReturn(false);
        $member->method('getErrors')->willReturn([1]);
        $result = $this->service->revoke($member, $this->createMock(RoleRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testRevokeShouldReturnErrorWhenRoleIsNotGranted(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('hasRole')->willReturn(false);
        $result = $this->service->revoke($member, $this->createMock(RoleRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('role.not.granted', $result->getErrors()['api']);
    }

    public function testRevokeShouldReturnSuccessWhenRevokingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('hasRole')->willReturn(true);
        $member->method('removeRole')->willReturn(true);
        $result = $this->service->revoke($member, $this->createMock(RoleRepositoryInterface::class));

        self::assertTrue($result->getResult());
    }

    public function testRevokeShouldReturnErrorWhenRevokingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while revoking role' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('hasRole')->willReturn(true);
        $member->method('removeRole')->willThrowException(new Exception('exc'));
        $result = $this->service->revoke($member, $this->createMock(RoleRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
