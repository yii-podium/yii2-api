<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Poll;

use Exception;
use Podium\Api\Interfaces\PollPostRepositoryInterface;
use Podium\Api\Interfaces\PollRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Poll\PollRemover;
use Podium\Tests\AppTestCase;
use Yii;
use yii\db\Connection;
use yii\db\Transaction;

class PollRemoverTest extends AppTestCase
{
    private PollRemover $service;

    protected function setUp(): void
    {
        $this->service = new PollRemover();
        $connection = $this->createMock(Connection::class);
        $connection->method('beginTransaction')->willReturn($this->createMock(Transaction::class));
        Yii::$app->set('db', $connection);
    }

    public function testBeforeRemoveShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeRemove());
    }

    public function testRemoveShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->remove($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testRemoveShouldReturnErrorWhenRemovingErrored(): void
    {
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
        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('delete')->willReturn(true);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->remove($post);

        self::assertTrue($result->getResult());
    }

    public function testRemoveShouldReturnErrorWhenRemovingThrowsException(): void
    {
        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('delete')->willThrowException(new Exception('exc'));
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->remove($post);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
