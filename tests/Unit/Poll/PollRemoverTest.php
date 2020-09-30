<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Poll;

use Exception;
use Podium\Api\Interfaces\PollPostRepositoryInterface;
use Podium\Api\Interfaces\PollRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Poll\PollRemover;
use Podium\Tests\AppTestCase;

class PollRemoverTest extends AppTestCase
{
    private PollRemover $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PollRemover();
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

        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('delete')->willReturn(false);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->remove($post);

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testRemoveShouldReturnSuccessWhenRemovingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('delete')->willReturn(true);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->remove($post);

        self::assertTrue($result->getResult());
    }

    public function testRemoveShouldReturnErrorWhenRemovingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('delete')->willThrowException(new Exception('exc'));
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->remove($post);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
