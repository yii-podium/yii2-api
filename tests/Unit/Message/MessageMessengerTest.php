<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Message;

use Exception;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\MessageRepositoryInterface;
use Podium\Api\Services\Message\MessageMessenger;
use Podium\Tests\AppTestCase;

class MessageMessengerTest extends AppTestCase
{
    private MessageMessenger $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MessageMessenger();
    }

    public function testSendShouldReturnErrorWhenSendingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('send')->willReturn(false);
        $sender = $this->createMock(MemberRepositoryInterface::class);
        $sender->method('getId')->willReturn(1);
        $receiver = $this->createMock(MemberRepositoryInterface::class);
        $receiver->method('getId')->willReturn(2);
        $receiver->method('isIgnoring')->willReturn(false);
        $result = $this->service->send($message, $sender, $receiver);

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testSendShouldReturnErrorWhenSenderAndReceiverAreTheSame(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $sender = $this->createMock(MemberRepositoryInterface::class);
        $sender->method('getId')->willReturn(1);
        $receiver = $this->createMock(MemberRepositoryInterface::class);
        $receiver->method('getId')->willReturn(2);
        $result = $this->service->send(
            $this->createMock(MessageRepositoryInterface::class),
            $sender,
            $sender
        );

        self::assertFalse($result->getResult());
        self::assertSame('message.no.self.sending', $result->getErrors()['api']);
    }

    public function testSendShouldReturnErrorWhenReplyParticipantsAreUnverified(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $replyTo = $this->createMock(MessageRepositoryInterface::class);
        $replyTo->method('verifyParticipants')->willReturn(false);
        $sender = $this->createMock(MemberRepositoryInterface::class);
        $sender->method('getId')->willReturn(1);
        $receiver = $this->createMock(MemberRepositoryInterface::class);
        $receiver->method('getId')->willReturn(2);
        $result = $this->service->send(
            $this->createMock(MessageRepositoryInterface::class),
            $sender,
            $receiver,
            $replyTo
        );

        self::assertFalse($result->getResult());
        self::assertSame('message.wrong.reply', $result->getErrors()['api']);
    }

    public function testSendShouldReturnErrorWhenReceiverIgnoresSender(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $sender = $this->createMock(MemberRepositoryInterface::class);
        $sender->method('getId')->willReturn(1);
        $receiver = $this->createMock(MemberRepositoryInterface::class);
        $receiver->method('getId')->willReturn(2);
        $receiver->method('isIgnoring')->willReturn(true);
        $result = $this->service->send(
            $this->createMock(MessageRepositoryInterface::class),
            $sender,
            $receiver
        );

        self::assertFalse($result->getResult());
        self::assertSame('message.receiver.rejected', $result->getErrors()['api']);
    }

    public function testSendShouldReturnSuccessWhenSendingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('send')->willReturn(true);
        $sender = $this->createMock(MemberRepositoryInterface::class);
        $sender->method('getId')->willReturn(1);
        $receiver = $this->createMock(MemberRepositoryInterface::class);
        $receiver->method('getId')->willReturn(2);
        $receiver->method('isIgnoring')->willReturn(false);
        $result = $this->service->send($message, $sender, $receiver);

        self::assertTrue($result->getResult());
    }

    public function testSendShouldReturnSuccessWhenSendingIsDoneWithReply(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $replyTo = $this->createMock(MessageRepositoryInterface::class);
        $replyTo->method('verifyParticipants')->willReturn(true);
        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('send')->willReturn(true);
        $sender = $this->createMock(MemberRepositoryInterface::class);
        $sender->method('getId')->willReturn(1);
        $receiver = $this->createMock(MemberRepositoryInterface::class);
        $receiver->method('getId')->willReturn(2);
        $receiver->method('isIgnoring')->willReturn(false);
        $result = $this->service->send($message, $sender, $receiver, $replyTo);

        self::assertTrue($result->getResult());
    }

    public function testSendShouldReturnErrorWhenSendingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('send')->willThrowException(new Exception('exc'));
        $sender = $this->createMock(MemberRepositoryInterface::class);
        $sender->method('getId')->willReturn(1);
        $receiver = $this->createMock(MemberRepositoryInterface::class);
        $receiver->method('getId')->willReturn(2);
        $receiver->method('isIgnoring')->willReturn(false);
        $result = $this->service->send($message, $sender, $receiver);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
