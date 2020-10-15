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

    public function testRemoveShouldReturnErrorWhenRemovingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $participant = $this->createMock(MemberRepositoryInterface::class);
        $participant->method('isBanned')->willReturn(false);
        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(true);
        $messageSide->method('delete')->willReturn(false);

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $result = $this->service->remove($message, $participant);

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testRemoveShouldReturnErrorWhenParticipantIsBanned(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $participant = $this->createMock(MemberRepositoryInterface::class);
        $participant->method('isBanned')->willReturn(true);

        $result = $this->service->remove($this->createMock(MessageRepositoryInterface::class), $participant);

        self::assertFalse($result->getResult());
        self::assertSame(['api' => 'member.banned'], $result->getErrors());
    }

    public function testRemoveShouldReturnErrorWhenMessageIsNotArchived(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $participant = $this->createMock(MemberRepositoryInterface::class);
        $participant->method('isBanned')->willReturn(false);
        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(false);

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $result = $this->service->remove($message, $participant);

        self::assertFalse($result->getResult());
        self::assertSame('message.must.be.archived', $result->getErrors()['api']);
    }

    public function testRemoveShouldReturnErrorWhenAllSidesAreDeletedAndMessageDeleteErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $participant = $this->createMock(MemberRepositoryInterface::class);
        $participant->method('isBanned')->willReturn(false);
        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(true);
        $messageSide->method('delete')->willReturn(true);

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $message->method('isCompletelyDeleted')->willReturn(true);
        $message->method('delete')->willReturn(false);
        $result = $this->service->remove($message, $participant);

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testRemoveShouldReturnSuccessWhenRemovingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $participant = $this->createMock(MemberRepositoryInterface::class);
        $participant->method('isBanned')->willReturn(false);
        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(true);
        $messageSide->method('delete')->willReturn(true);

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $message->method('isCompletelyDeleted')->willReturn(false);
        $result = $this->service->remove($message, $participant);

        self::assertTrue($result->getResult());
    }

    public function testRemoveShouldReturnErrorWhenRemovingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while deleting message' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $participant = $this->createMock(MemberRepositoryInterface::class);
        $participant->method('isBanned')->willReturn(false);
        $messageSide = $this->createMock(MessageParticipantRepositoryInterface::class);
        $messageSide->method('isArchived')->willReturn(true);
        $messageSide->method('delete')->willThrowException(new Exception('exc'));

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('getParticipant')->willReturn($messageSide);
        $result = $this->service->remove($message, $participant);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
