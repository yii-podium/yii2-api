<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Forum;

use Exception;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Forum\ForumArchiver;
use Podium\Tests\AppTestCase;

class ForumArchiverTest extends AppTestCase
{
    private ForumArchiver $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ForumArchiver();
    }

    public function testArchiveShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->archive($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Forum must be instance of Podium\Api\Interfaces\ForumRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testArchiveShouldReturnErrorWhenArchivingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('isArchived')->willReturn(false);
        $forum->method('archive')->willReturn(false);
        $forum->method('getErrors')->willReturn([1]);
        $result = $this->service->archive($forum);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testArchiveShouldReturnErrorWhenForumIsAlreadyArchived(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('isArchived')->willReturn(true);
        $result = $this->service->archive($forum);

        self::assertFalse($result->getResult());
        self::assertSame('forum.already.archived', $result->getErrors()['api']);
    }

    public function testArchiveShouldReturnSuccessWhenArchivingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('archive')->willReturn(true);
        $result = $this->service->archive($forum);

        self::assertTrue($result->getResult());
    }

    public function testArchiveShouldReturnErrorWhenArchivingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while archiving forum' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('isArchived')->willReturn(false);
        $forum->method('archive')->willThrowException(new Exception('exc'));
        $result = $this->service->archive($forum);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testReviveShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->revive($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Forum must be instance of Podium\Api\Interfaces\ForumRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testReviveShouldReturnErrorWhenRevivingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('isArchived')->willReturn(true);
        $forum->method('revive')->willReturn(false);
        $forum->method('getErrors')->willReturn([1]);
        $result = $this->service->revive($forum);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testReviveShouldReturnErrorWhenForumIsNotArchived(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('isArchived')->willReturn(false);
        $result = $this->service->revive($forum);

        self::assertFalse($result->getResult());
        self::assertSame('forum.not.archived', $result->getErrors()['api']);
    }

    public function testReviveShouldReturnSuccessWhenRevivingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('isArchived')->willReturn(true);
        $forum->method('revive')->willReturn(true);
        $result = $this->service->revive($forum);

        self::assertTrue($result->getResult());
    }

    public function testReviveShouldReturnErrorWhenRevivingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while reviving forum' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('isArchived')->willReturn(true);
        $forum->method('revive')->willThrowException(new Exception('exc'));
        $result = $this->service->revive($forum);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
