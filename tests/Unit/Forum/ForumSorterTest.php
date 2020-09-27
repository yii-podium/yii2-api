<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Forum;

use Exception;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Forum\ForumSorter;
use Podium\Tests\AppTestCase;

class ForumSorterTest extends AppTestCase
{
    private ForumSorter $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ForumSorter();
    }

    public function testBeforeReplaceShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeReplace());
    }

    public function testReplaceShouldReturnErrorWhenFirstRepositoryIsWrong(): void
    {
        $result = $this->service->replace(
            $this->createMock(RepositoryInterface::class),
            $this->createMock(ForumRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testReplaceShouldReturnErrorWhenSecondRepositoryIsWrong(): void
    {
        $result = $this->service->replace(
            $this->createMock(ForumRepositoryInterface::class),
            $this->createMock(RepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testReplaceShouldReturnErrorWhenSettingFirstOrderErrored(): void
    {
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('getOrder')->willReturn(1);
        $forum->method('setOrder')->willReturn(false);
        $result = $this->service->replace($forum, $forum);

        self::assertFalse($result->getResult());
    }

    public function testReplaceShouldReturnErrorWhenSettingSecondOrderErrored(): void
    {
        $forum1 = $this->createMock(ForumRepositoryInterface::class);
        $forum1->method('getOrder')->willReturn(1);
        $forum1->method('setOrder')->willReturn(true);
        $forum2 = $this->createMock(ForumRepositoryInterface::class);
        $forum2->method('getOrder')->willReturn(2);
        $forum2->method('setOrder')->willReturn(false);
        $result = $this->service->replace($forum1, $forum2);

        self::assertFalse($result->getResult());
    }

    public function testReplaceShouldReturnSuccessWhenReplacingIsDone(): void
    {
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('getOrder')->willReturn(1);
        $forum->method('setOrder')->willReturn(true);
        $result = $this->service->replace($forum, $forum);

        self::assertTrue($result->getResult());
    }

    public function testReplaceShouldReturnErrorWhenReplacingThrowsException(): void
    {
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('getOrder')->willReturn(1);
        $forum->method('setOrder')->willThrowException(new Exception('exc'));
        $result = $this->service->replace($forum, $forum);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testBeforeSortShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeSort());
    }

    public function testSortShouldReturnErrorWhenSortingErrored(): void
    {
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('sort')->willReturn(false);
        $result = $this->service->sort($forum);

        self::assertFalse($result->getResult());
    }

    public function testSortShouldReturnSuccessWhenSortingIsDone(): void
    {
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('sort')->willReturn(true);
        $result = $this->service->sort($forum);

        self::assertTrue($result->getResult());
    }

    public function testSortShouldReturnErrorWhenSortingThrowsException(): void
    {
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('sort')->willThrowException(new Exception('exc'));
        $result = $this->service->sort($forum);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
