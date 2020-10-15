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

        $participant = $this->createMock(MemberRepositoryInterface::class);
        $participant->method('isBanned')->willReturn(false);
        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(false);
        $messageSide->method('archive')->willReturn(false);
        $messageSide->method('getErrors')->willReturn([1]);

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $result = $this->service->archive($message, $participant);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testArchiveShouldReturnErrorWhenParticipantIsBanned(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $participant = $this->createMock(MemberRepositoryInterface::class);
        $participant->method('isBanned')->willReturn(true);

        $result = $this->service->archive($this->createMock(MessageRepositoryInterface::class), $participant);

        self::assertFalse($result->getResult());
        self::assertSame(['api' => 'member.banned'], $result->getErrors());
    }

    public function testArchiveShouldReturnErrorWhenMessageIsAlreadyArchived(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $participant = $this->createMock(MemberRepositoryInterface::class);
        $participant->method('isBanned')->willReturn(false);
        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(true);

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $result = $this->service->archive($message, $participant);

        self::assertFalse($result->getResult());
        self::assertSame('message.already.archived', $result->getErrors()['api']);
    }

    public function testArchiveShouldReturnSuccessWhenArchivingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $participant = $this->createMock(MemberRepositoryInterface::class);
        $participant->method('isBanned')->willReturn(false);
        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(false);
        $messageSide->method('archive')->willReturn(true);

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $result = $this->service->archive($message, $participant);

        self::assertTrue($result->getResult());
    }

    public function testArchiveShouldReturnErrorWhenArchivingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while archiving message' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $participant = $this->createMock(MemberRepositoryInterface::class);
        $participant->method('isBanned')->willReturn(false);
        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(false);
        $messageSide->method('archive')->willThrowException(new Exception('exc'));

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $result = $this->service->archive($message, $participant);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testReviveShouldReturnErrorWhenRevivingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $participant = $this->createMock(MemberRepositoryInterface::class);
        $participant->method('isBanned')->willReturn(false);
        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(true);
        $messageSide->method('revive')->willReturn(false);
        $messageSide->method('getErrors')->willReturn([1]);

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $result = $this->service->revive($message, $participant);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testReviveShouldReturnErrorWhenParticipantIsBanned(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $participant = $this->createMock(MemberRepositoryInterface::class);
        $participant->method('isBanned')->willReturn(true);

        $result = $this->service->revive($this->createMock(MessageRepositoryInterface::class), $participant);

        self::assertFalse($result->getResult());
        self::assertSame(['api' => 'member.banned'], $result->getErrors());
    }

    public function testReviveShouldReturnErrorWhenMessageIsNotArchived(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $participant = $this->createMock(MemberRepositoryInterface::class);
        $participant->method('isBanned')->willReturn(false);
        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(false);

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $result = $this->service->revive($message, $participant);

        self::assertFalse($result->getResult());
        self::assertSame('message.not.archived', $result->getErrors()['api']);
    }

    public function testReviveShouldReturnSuccessWhenRevivingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $participant = $this->createMock(MemberRepositoryInterface::class);
        $participant->method('isBanned')->willReturn(false);
        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(true);
        $messageSide->method('revive')->willReturn(true);

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $result = $this->service->revive($message, $participant);

        self::assertTrue($result->getResult());
    }

    public function testReviveShouldReturnErrorWhenRevivingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while reviving message' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $participant = $this->createMock(MemberRepositoryInterface::class);
        $participant->method('isBanned')->willReturn(false);
        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(true);
        $messageSide->method('revive')->willThrowException(new Exception('exc'));

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $result = $this->service->revive($message, $participant);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
