<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Forum;

use Exception;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Forum\ForumHider;
use Podium\Tests\AppTestCase;

use function count;

class ForumHiderTest extends AppTestCase
{
    private ForumHider $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ForumHider();
    }

    public function testHideShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->hide($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Forum must be instance of Podium\Api\Interfaces\ForumRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testHideShouldReturnErrorWhenHidingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('isHidden')->willReturn(false);
        $forum->method('hide')->willReturn(false);
        $forum->method('getErrors')->willReturn([1]);
        $result = $this->service->hide($forum);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testHideShouldReturnErrorWhenForumIsAlreadyHidden(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('isHidden')->willReturn(true);
        $result = $this->service->hide($forum);

        self::assertFalse($result->getResult());
        self::assertSame('forum.already.hidden', $result->getErrors()['api']);
    }

    public function testHideShouldReturnSuccessWhenHidingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('isHidden')->willReturn(false);
        $forum->method('hide')->willReturn(true);
        $result = $this->service->hide($forum);

        self::assertTrue($result->getResult());
    }

    public function testHideShouldReturnErrorWhenHidingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while hiding forum' === $data[0]
                        && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('isHidden')->willReturn(false);
        $forum->method('hide')->willThrowException(new Exception('exc'));
        $result = $this->service->hide($forum);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testRevealShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->reveal($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Forum must be instance of Podium\Api\Interfaces\ForumRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testRevealShouldReturnErrorWhenRevealingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('isHidden')->willReturn(true);
        $forum->method('reveal')->willReturn(false);
        $forum->method('getErrors')->willReturn([1]);
        $result = $this->service->reveal($forum);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testRevealShouldReturnErrorWhenForumIsNotHidden(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('isHidden')->willReturn(false);
        $result = $this->service->reveal($forum);

        self::assertFalse($result->getResult());
        self::assertSame('forum.not.hidden', $result->getErrors()['api']);
    }

    public function testRevealShouldReturnSuccessWhenRevealingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('isHidden')->willReturn(true);
        $forum->method('reveal')->willReturn(true);
        $result = $this->service->reveal($forum);

        self::assertTrue($result->getResult());
    }

    public function testRevealShouldReturnErrorWhenRevealingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while revealing forum' === $data[0]
                        && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('isHidden')->willReturn(true);
        $forum->method('reveal')->willThrowException(new Exception('exc'));
        $result = $this->service->reveal($forum);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
