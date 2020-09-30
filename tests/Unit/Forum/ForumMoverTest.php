<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Forum;

use Exception;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Forum\ForumMover;
use Podium\Tests\AppTestCase;

class ForumMoverTest extends AppTestCase
{
    private ForumMover $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ForumMover();
    }

    public function testBeforeMoveShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeMove());
    }

    public function testMoveShouldReturnErrorWhenForumRepositoryIsWrong(): void
    {
        $result = $this->service->move(
            $this->createMock(RepositoryInterface::class),
            $this->createMock(CategoryRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testMoveShouldReturnErrorWhenCategoryRepositoryIsWrong(): void
    {
        $result = $this->service->move(
            $this->createMock(ForumRepositoryInterface::class),
            $this->createMock(RepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testMoveShouldReturnErrorWhenMovingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('move')->willReturn(false);
        $result = $this->service->move($forum, $this->createMock(CategoryRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testMoveShouldReturnSuccessWhenMovingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('move')->willReturn(true);
        $result = $this->service->move($forum, $this->createMock(CategoryRepositoryInterface::class));

        self::assertTrue($result->getResult());
    }

    public function testMoveShouldReturnErrorWhenMovingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('move')->willThrowException(new Exception('exc'));
        $result = $this->service->move($forum, $this->createMock(CategoryRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
