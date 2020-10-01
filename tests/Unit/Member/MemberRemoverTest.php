<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Member;

use Exception;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Member\MemberRemover;
use Podium\Tests\AppTestCase;

class MemberRemoverTest extends AppTestCase
{
    private MemberRemover $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MemberRemover();
    }

    public function testRemoveShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->remove($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testRemoveShouldReturnErrorWhenRemovingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('delete')->willReturn(false);
        $result = $this->service->remove($member);

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testRemoveShouldReturnSuccessWhenRemovingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('delete')->willReturn(true);
        $result = $this->service->remove($member);

        self::assertTrue($result->getResult());
    }

    public function testRemoveShouldReturnErrorWhenRemovingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while deleting member' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('delete')->willThrowException(new Exception('exc'));
        $result = $this->service->remove($member);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
