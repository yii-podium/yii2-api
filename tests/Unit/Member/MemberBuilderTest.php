<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Member;

use Exception;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Services\Member\MemberBuilder;
use Podium\Tests\AppTestCase;

class MemberBuilderTest extends AppTestCase
{
    private MemberBuilder $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MemberBuilder();
    }

    public function testBeforeRegisterShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeRegister());
    }

    public function testRegisterShouldReturnErrorWhenRegisteringErrored(): void
    {
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('register')->willReturn(false);
        $member->method('getErrors')->willReturn([1]);
        $result = $this->service->register($member, 1);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testRegisterShouldReturnSuccessWhenRegisteringIsDone(): void
    {
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('register')->willReturn(true);
        $result = $this->service->register($member, 1);

        self::assertTrue($result->getResult());
    }

    public function testRegisterShouldReturnErrorWhenRegisteringThrowsException(): void
    {
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('register')->willThrowException(new Exception('exc'));
        $result = $this->service->register($member, 1);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testBeforeEditShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeEdit());
    }

    public function testEditShouldReturnErrorWhenEditingErrored(): void
    {
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('edit')->willReturn(false);
        $member->method('getErrors')->willReturn([1]);
        $result = $this->service->edit($member);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testEditShouldReturnSuccessWhenEditingIsDone(): void
    {
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('edit')->willReturn(true);
        $result = $this->service->edit($member);

        self::assertTrue($result->getResult());
    }

    public function testEditShouldReturnErrorWhenEditingThrowsException(): void
    {
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('edit')->willThrowException(new Exception('exc'));
        $result = $this->service->edit($member);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
