<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Poll;

use Exception;
use Podium\Api\Interfaces\PollPostRepositoryInterface;
use Podium\Api\Interfaces\PollRepositoryInterface;
use Podium\Api\Services\Poll\PollBuilder;
use Podium\Tests\AppTestCase;
use Yii;
use yii\db\Connection;
use yii\db\Transaction;

class PollBuilderTest extends AppTestCase
{
    private PollBuilder $service;

    protected function setUp(): void
    {
        $this->service = new PollBuilder();
        $connection = $this->createMock(Connection::class);
        $connection->method('beginTransaction')->willReturn($this->createMock(Transaction::class));
        Yii::$app->set('db', $connection);
    }

    public function testBeforeCreateShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeCreate());
    }

    public function testCreateShouldReturnErrorWhenCreatingErrored(): void
    {
        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('create')->willReturn(false);
        $poll->method('getErrors')->willReturn([1]);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->create($post, []);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testCreateShouldReturnSuccessWhenCreatingIsDone(): void
    {
        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('create')->willReturn(true);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->create($post, []);

        self::assertTrue($result->getResult());
    }

    public function testCreateShouldReturnErrorWhenCreatingThrowsException(): void
    {
        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('create')->willThrowException(new Exception('exc'));
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->create($post, []);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testBeforeEditShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeEdit());
    }

    public function testEditShouldReturnErrorWhenEditingErrored(): void
    {
        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('edit')->willReturn(false);
        $poll->method('getErrors')->willReturn([1]);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->edit($post);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testEditShouldReturnSuccessWhenEditingIsDone(): void
    {
        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('edit')->willReturn(true);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->edit($post);

        self::assertTrue($result->getResult());
    }

    public function testEditShouldReturnErrorWhenEditingThrowsException(): void
    {
        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('edit')->willThrowException(new Exception('exc'));
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->edit($post);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
