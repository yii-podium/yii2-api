<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Message;

use Exception;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\MessageParticipantRepositoryInterface;
use Podium\Api\Interfaces\MessageRepositoryInterface;
use Podium\Api\Services\Message\MessageRemover;
use Podium\Tests\AppTestCase;

class MessageRemoverTest extends AppTestCase
{
    private MessageRemover $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MessageRemover();
    }

    public function testBeforeRemoveShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeRemove());
    }

    public function testRemoveShouldReturnErrorWhenRemovingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(true);
        $messageSide->method('delete')->willReturn(false);

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $result = $this->service->remove($message, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testRemoveShouldReturnErrorWhenMessageIsNotArchived(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(false);

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $result = $this->service->remove($message, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('message.must.be.archived', $result->getErrors()['api']);
    }

    public function testRemoveShouldReturnErrorWhenAllSidesAreDeletedAndMessageDeleteErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(true);
        $messageSide->method('delete')->willReturn(true);

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $message->method('isCompletelyDeleted')->willReturn(true);
        $message->method('delete')->willReturn(false);
        $result = $this->service->remove($message, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testRemoveShouldReturnSuccessWhenRemovingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(true);
        $messageSide->method('delete')->willReturn(true);

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $message->method('isCompletelyDeleted')->willReturn(false);
        $result = $this->service->remove($message, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($result->getResult());
    }

    public function testRemoveShouldReturnErrorWhenRemovingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(true);
        $messageSide->method('delete')->willThrowException(new Exception('exc'));

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $result = $this->service->remove($message, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
