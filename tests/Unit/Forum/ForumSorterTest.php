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

    public function testReplaceShouldReturnErrorWhenFirstRepositoryIsWrong(): void
    {
        $result = $this->service->replace(
            $this->createMock(RepositoryInterface::class),
            $this->createMock(ForumRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame(
            'First forum must be instance of Podium\Api\Interfaces\ForumRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testReplaceShouldReturnErrorWhenSecondRepositoryIsWrong(): void
    {
        $result = $this->service->replace(
            $this->createMock(ForumRepositoryInterface::class),
            $this->createMock(RepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame(
            'Second forum must be instance of Podium\Api\Interfaces\ForumRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testReplaceShouldReturnErrorWhenSettingFirstOrderErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while replacing forums order' === $data[0]
                        && 'Error while setting new forum order!' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $forum1 = $this->createMock(ForumRepositoryInterface::class);
        $forum1->method('getOrder')->willReturn(1);
        $forum1->method('setOrder')->willReturn(false);
        $forum2 = $this->createMock(ForumRepositoryInterface::class);
        $forum2->method('getOrder')->willReturn(2);
        $forum2->method('setOrder')->willReturn(true);
        $result = $this->service->replace($forum1, $forum2);

        self::assertFalse($result->getResult());
        self::assertSame('Error while setting new forum order!', $result->getErrors()['exception']->getMessage());
    }

    public function testReplaceShouldReturnErrorWhenSettingSecondOrderErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while replacing forums order' === $data[0]
                        && 'Error while setting new forum order!' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $forum1 = $this->createMock(ForumRepositoryInterface::class);
        $forum1->method('getOrder')->willReturn(1);
        $forum1->method('setOrder')->willReturn(true);
        $forum2 = $this->createMock(ForumRepositoryInterface::class);
        $forum2->method('getOrder')->willReturn(2);
        $forum2->method('setOrder')->willReturn(false);
        $result = $this->service->replace($forum1, $forum2);

        self::assertFalse($result->getResult());
        self::assertSame('Error while setting new forum order!', $result->getErrors()['exception']->getMessage());
    }

    public function testReplaceShouldReturnSuccessWhenReplacingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('getOrder')->willReturn(1);
        $forum->method('setOrder')->willReturn(true);
        $result = $this->service->replace($forum, $forum);

        self::assertTrue($result->getResult());
    }

    public function testReplaceShouldReturnErrorWhenReplacingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while replacing forums order' === $data[0]
                        && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('getOrder')->willReturn(1);
        $forum->method('setOrder')->willThrowException(new Exception('exc'));
        $result = $this->service->replace($forum, $forum);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testSortShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->sort($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Forum must be instance of Podium\Api\Interfaces\ForumRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testSortShouldReturnErrorWhenSortingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('sort')->willReturn(false);
        $result = $this->service->sort($forum);

        self::assertFalse($result->getResult());
    }

    public function testSortShouldReturnSuccessWhenSortingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('sort')->willReturn(true);
        $result = $this->service->sort($forum);

        self::assertTrue($result->getResult());
    }

    public function testSortShouldReturnErrorWhenSortingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while sorting forums' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('sort')->willThrowException(new Exception('exc'));
        $result = $this->service->sort($forum);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
