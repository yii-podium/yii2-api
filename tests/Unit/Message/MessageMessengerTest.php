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

    public function testBeforeSendShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeSend());
    }

    public function testSendShouldReturnErrorWhenSendingErrored(): void
    {
        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('send')->willReturn(false);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->send($message, $member, $member);

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testSendShouldReturnSuccessWhenSendingIsDone(): void
    {
        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('send')->willReturn(true);
        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->send($message, $member, $member);

        self::assertTrue($result->getResult());
    }

    public function testSendShouldReturnErrorWhenSendingThrowsException(): void
    {
        $message = $this->createMock(MessageRepositoryInterface::class);
        $message->method('send')->willThrowException(new Exception('exc'));
        $member = $this->createMock(MemberRepositoryInterface::class);
        $result = $this->service->send($message, $member, $member);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
