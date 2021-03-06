<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Forum;

use Exception;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Forum\ForumBuilder;
use Podium\Tests\AppTestCase;

class ForumBuilderTest extends AppTestCase
{
    private ForumBuilder $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ForumBuilder();
    }

    public function testCreateShouldReturnErrorWhenForumRepositoryIsWrong(): void
    {
        $result = $this->service->create(
            $this->createMock(RepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class),
            $this->createMock(CategoryRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame(
            'Forum must be instance of Podium\Api\Interfaces\ForumRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testCreateShouldReturnErrorWhenCategoryRepositoryIsWrong(): void
    {
        $result = $this->service->create(
            $this->createMock(ForumRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class),
            $this->createMock(RepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame(
            'Category must be instance of Podium\Api\Interfaces\CategoryRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testCreateShouldReturnErrorWhenCreatingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(false);
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('create')->willReturn(false);
        $forum->method('getErrors')->willReturn([1]);
        $result = $this->service->create($forum, $author, $this->createMock(CategoryRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testCreateShouldReturnErrorWhenAuthorIsBanned(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(true);
        $result = $this->service->create(
            $this->createMock(ForumRepositoryInterface::class),
            $author,
            $this->createMock(CategoryRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame(['api' => 'member.banned'], $result->getErrors());
    }

    public function testCreateShouldReturnSuccessWhenCreatingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(false);
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('create')->willReturn(true);
        $result = $this->service->create($forum, $author, $this->createMock(CategoryRepositoryInterface::class));

        self::assertTrue($result->getResult());
    }

    public function testCreateShouldReturnErrorWhenCreatingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while creating forum' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(false);
        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('create')->willThrowException(new Exception('exc'));
        $result = $this->service->create($forum, $author, $this->createMock(CategoryRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testEditShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->edit($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Forum must be instance of Podium\Api\Interfaces\ForumRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testEditShouldReturnErrorWhenEditingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('edit')->willReturn(false);
        $forum->method('getErrors')->willReturn([1]);
        $result = $this->service->edit($forum);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testEditShouldReturnSuccessWhenEditingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('edit')->willReturn(true);
        $result = $this->service->edit($forum);

        self::assertTrue($result->getResult());
    }

    public function testEditShouldReturnErrorWhenEditingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while editing forum' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $forum = $this->createMock(ForumRepositoryInterface::class);
        $forum->method('edit')->willThrowException(new Exception('exc'));
        $result = $this->service->edit($forum);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
