<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Forum;

use Exception;
use PHPUnit\Framework\TestCase;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Forum\ForumBuilder;

class ForumBuilderTest extends TestCase
{
    private ForumBuilder $service;

    protected function setUp(): void
    {
        $this->service = new ForumBuilder();
    }

    public function testBeforeCreateShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeCreate());
    }

    public function testCreateShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->create(
            $this->createMock(RepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class),
            $this->createMock(RepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
    }

    public function testCreateShouldReturnErrorWhenCreatingErrored(): void
    {
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('create')->willReturn(false);
        $forum->method('getErrors')->willReturn([1]);
        $result = $this->service->create(
            $forum,
            $this->createMock(MemberRepositoryInterface::class),
            $this->createMock(CategoryRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testCreateShouldReturnSuccessWhenCreatingIsDone(): void
    {
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('create')->willReturn(true);
        $result = $this->service->create(
            $forum,
            $this->createMock(MemberRepositoryInterface::class),
            $this->createMock(CategoryRepositoryInterface::class)
        );

        self::assertTrue($result->getResult());
    }

    public function testCreateShouldReturnErrorWhenCreatingThrowsException(): void
    {
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('create')->willThrowException(new Exception('exc'));
        $result = $this->service->create(
            $forum,
            $this->createMock(MemberRepositoryInterface::class),
            $this->createMock(CategoryRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testBeforeEditShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeEdit());
    }

    public function testEditShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->edit($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
    }

    public function testEditShouldReturnErrorWhenEditingErrored(): void
    {
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('edit')->willReturn(false);
        $forum->method('getErrors')->willReturn([1]);
        $result = $this->service->edit($forum);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testEditShouldReturnSuccessWhenEditingIsDone(): void
    {
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('edit')->willReturn(true);
        $result = $this->service->edit($forum);

        self::assertTrue($result->getResult());
    }

    public function testEditShouldReturnErrorWhenEditingThrowsException(): void
    {
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('edit')->willThrowException(new Exception('exc'));
        $result = $this->service->edit($forum);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
