<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Thread;

use Exception;
use PHPUnit\Framework\TestCase;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\SubscriptionRepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\Services\Thread\ThreadSubscriber;

class ThreadSubscriberTest extends TestCase
{
    private ThreadSubscriber $service;

    protected function setUp(): void
    {
        $this->service = new ThreadSubscriber();
    }

    public function testBeforeSubscribeShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeSubscribe());
    }

    public function testSubscribeShouldReturnErrorWhenSubscribingErrored(): void
    {
        $subscription = $this->createMock(SubscriptionRepositoryInterface::class);
        $subscription->method('getErrors')->willReturn([1]);
        $subscription->method('isMemberSubscribed')->willReturn(false);
        $subscription->method('subscribe')->willReturn(false);
        $result = $this->service->subscribe(
            $subscription,
            $this->createMock(ThreadRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testSubscribeShouldReturnSuccessWhenSubscribingIsDone(): void
    {
        $subscription = $this->createMock(SubscriptionRepositoryInterface::class);
        $subscription->method('isMemberSubscribed')->willReturn(false);
        $subscription->method('subscribe')->willReturn(true);
        $result = $this->service->subscribe(
            $subscription,
            $this->createMock(ThreadRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertTrue($result->getResult());
    }

    public function testSubscribeShouldReturnErrorWhenSubscribingThrowsException(): void
    {
        $subscription = $this->createMock(SubscriptionRepositoryInterface::class);
        $subscription->method('isMemberSubscribed')->willReturn(false);
        $subscription->method('subscribe')->willThrowException(new Exception('exc'));
        $result = $this->service->subscribe(
            $subscription,
            $this->createMock(ThreadRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testSubscribeShouldReturnErrorWhenMemberIsAlreadySubscribed(): void
    {
        $subscription = $this->createMock(SubscriptionRepositoryInterface::class);
        $subscription->method('isMemberSubscribed')->willReturn(true);
        $result = $this->service->subscribe(
            $subscription,
            $this->createMock(ThreadRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame('thread.already.subscribed', $result->getErrors()['api']);
    }

    public function testBeforeUnsubscribeShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeUnsubscribe());
    }

    public function testUnsubscribeShouldReturnErrorWhenUnsubscribingErrored(): void
    {
        $subscription = $this->createMock(SubscriptionRepositoryInterface::class);
        $subscription->method('getErrors')->willReturn([1]);
        $subscription->method('fetchOne')->willReturn(true);
        $subscription->method('delete')->willReturn(false);
        $result = $this->service->unsubscribe(
            $subscription,
            $this->createMock(ThreadRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testUnsubscribeShouldReturnSuccessWhenUnsubscribingIsDone(): void
    {
        $subscription = $this->createMock(SubscriptionRepositoryInterface::class);
        $subscription->method('fetchOne')->willReturn(true);
        $subscription->method('delete')->willReturn(true);
        $result = $this->service->unsubscribe(
            $subscription,
            $this->createMock(ThreadRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertTrue($result->getResult());
    }

    public function testUnsubscribeShouldReturnErrorWhenUnsubscribingThrowsException(): void
    {
        $subscription = $this->createMock(SubscriptionRepositoryInterface::class);
        $subscription->method('fetchOne')->willReturn(true);
        $subscription->method('delete')->willThrowException(new Exception('exc'));
        $result = $this->service->unsubscribe(
            $subscription,
            $this->createMock(ThreadRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testUnsubscribeShouldReturnErrorWhenSubscriptionDoesntExist(): void
    {
        $subscription = $this->createMock(SubscriptionRepositoryInterface::class);
        $subscription->method('fetchOne')->willReturn(false);
        $result = $this->service->unsubscribe(
            $subscription,
            $this->createMock(ThreadRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame('thread.not.subscribed', $result->getErrors()['api']);
    }
}
