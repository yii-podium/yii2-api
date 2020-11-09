<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Poll;

use Exception;
use Podium\Api\Interfaces\PollPostRepositoryInterface;
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
        self::assertSame(
            'Post must be instance of Podium\Api\Interfaces\PollPostRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testRemoveShouldReturnErrorWhenRemovingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('removePoll')->willReturn(false);
        $result = $this->service->remove($post);

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testRemoveShouldReturnSuccessWhenRemovingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('removePoll')->willReturn(true);
        $result = $this->service->remove($post);

        self::assertTrue($result->getResult());
    }

    public function testRemoveShouldReturnErrorWhenRemovingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while deleting poll' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('removePoll')->willThrowException(new Exception('exc'));
        $result = $this->service->remove($post);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
