<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Group;

use Exception;
use Podium\Api\Interfaces\GroupRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Group\GroupRemover;
use Podium\Tests\AppTestCase;
use Yii;
use yii\db\Connection;
use yii\db\Transaction;

class GroupRemoverTest extends AppTestCase
{
    private GroupRemover $service;

    protected function setUp(): void
    {
        $this->service = new GroupRemover();
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
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('delete')->willReturn(false);
        $result = $this->service->remove($group);

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testRemoveShouldReturnSuccessWhenRemovingIsDone(): void
    {
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('delete')->willReturn(true);
        $result = $this->service->remove($group);

        self::assertTrue($result->getResult());
    }

    public function testRemoveShouldReturnErrorWhenRemovingThrowsException(): void
    {
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('delete')->willThrowException(new Exception('exc'));
        $result = $this->service->remove($group);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
