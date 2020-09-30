<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Message;

use Exception;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\MessageParticipantRepositoryInterface;
use Podium\Api\Interfaces\MessageRepositoryInterface;
use Podium\Api\Services\Message\MessageArchiver;
use Podium\Tests\AppTestCase;

class MessageArchiverTest extends AppTestCase
{
    private MessageArchiver $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MessageArchiver();
    }

    public function testArchiveShouldReturnErrorWhenArchivingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(false);
        $messageSide->method('archive')->willReturn(false);
        $messageSide->method('getErrors')->willReturn([1]);

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $result = $this->service->archive($message, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testArchiveShouldReturnErrorWhenMessageIsAlreadyArchived(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(true);

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $result = $this->service->archive($message, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('message.already.archived', $result->getErrors()['api']);
    }

    public function testArchiveShouldReturnSuccessWhenArchivingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(false);
        $messageSide->method('archive')->willReturn(true);

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $result = $this->service->archive($message, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($result->getResult());
    }

    public function testArchiveShouldReturnErrorWhenArchivingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(false);
        $messageSide->method('archive')->willThrowException(new Exception('exc'));

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $result = $this->service->archive($message, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testReviveShouldReturnErrorWhenRevivingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(true);
        $messageSide->method('revive')->willReturn(false);
        $messageSide->method('getErrors')->willReturn([1]);

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $result = $this->service->revive($message, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testReviveShouldReturnErrorWhenMessageIsNotArchived(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(false);

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $result = $this->service->revive($message, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('message.not.archived', $result->getErrors()['api']);
    }

    public function testReviveShouldReturnSuccessWhenRevivingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(true);
        $messageSide->method('revive')->willReturn(true);

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $result = $this->service->revive($message, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($result->getResult());
    }

    public function testReviveShouldReturnErrorWhenRevivingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(true);
        $messageSide->method('revive')->willThrowException(new Exception('exc'));

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $result = $this->service->revive($message, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
