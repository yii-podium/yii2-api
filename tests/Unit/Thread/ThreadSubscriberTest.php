<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Thread;

use Exception;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\SubscriptionRepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\Services\Thread\ThreadSubscriber;
use Podium\Tests\AppTestCase;

class ThreadSubscriberTest extends AppTestCase
{
    private ThreadSubscriber $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ThreadSubscriber();
    }

    public function testSubscribeShouldReturnErrorWhenSubscribingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $subscription = $this->createMock(SubscriptionRepositoryInterface::class);
        $subscription->method('getErrors')->willReturn([1]);
        $subscription->method('isMemberSubscribed')->willReturn(false);
        $subscription->method('subscribe')->willReturn(false);
        $result = $this->service->subscribe(
            $subscription,
            $this->createMock(ThreadRepositoryInterface::class),
            $member
        );

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testSubscribeShouldReturnErrorWhenMemberIsBanned(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(true);
        $result = $this->service->subscribe(
            $this->createMock(SubscriptionRepositoryInterface::class),
            $this->createMock(ThreadRepositoryInterface::class),
            $member
        );

        self::assertFalse($result->getResult());
        self::assertSame(['api' => 'member.banned'], $result->getErrors());
    }

    public function testSubscribeShouldReturnSuccessWhenSubscribingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $subscription = $this->createMock(SubscriptionRepositoryInterface::class);
        $subscription->method('isMemberSubscribed')->willReturn(false);
        $subscription->method('subscribe')->willReturn(true);
        $result = $this->service->subscribe(
            $subscription,
            $this->createMock(ThreadRepositoryInterface::class),
            $member
        );

        self::assertTrue($result->getResult());
    }

    public function testSubscribeShouldReturnErrorWhenSubscribingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while subscribing thread' === $data[0]
                        && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $subscription = $this->createMock(SubscriptionRepositoryInterface::class);
        $subscription->method('isMemberSubscribed')->willReturn(false);
        $subscription->method('subscribe')->willThrowException(new Exception('exc'));
        $result = $this->service->subscribe(
            $subscription,
            $this->createMock(ThreadRepositoryInterface::class),
            $member
        );

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testSubscribeShouldReturnErrorWhenMemberIsAlreadySubscribed(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $subscription = $this->createMock(SubscriptionRepositoryInterface::class);
        $subscription->method('isMemberSubscribed')->willReturn(true);
        $result = $this->service->subscribe(
            $subscription,
            $this->createMock(ThreadRepositoryInterface::class),
            $member
        );

        self::assertFalse($result->getResult());
        self::assertSame('thread.already.subscribed', $result->getErrors()['api']);
    }

    public function testUnsubscribeShouldReturnErrorWhenUnsubscribingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $subscription = $this->createMock(SubscriptionRepositoryInterface::class);
        $subscription->method('getErrors')->willReturn([1]);
        $subscription->method('fetchOne')->willReturn(true);
        $subscription->method('delete')->willReturn(false);
        $result = $this->service->unsubscribe(
            $subscription,
            $this->createMock(ThreadRepositoryInterface::class),
            $member
        );

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testUnsubscribeShouldReturnErrorWhenMemberIsBanned(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(true);
        $result = $this->service->unsubscribe(
            $this->createMock(SubscriptionRepositoryInterface::class),
            $this->createMock(ThreadRepositoryInterface::class),
            $member
        );

        self::assertFalse($result->getResult());
        self::assertSame(['api' => 'member.banned'], $result->getErrors());
    }

    public function testUnsubscribeShouldReturnSuccessWhenUnsubscribingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $subscription = $this->createMock(SubscriptionRepositoryInterface::class);
        $subscription->method('fetchOne')->willReturn(true);
        $subscription->method('delete')->willReturn(true);
        $result = $this->service->unsubscribe(
            $subscription,
            $this->createMock(ThreadRepositoryInterface::class),
            $member
        );

        self::assertTrue($result->getResult());
    }

    public function testUnsubscribeShouldReturnErrorWhenUnsubscribingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while unsubscribing thread' === $data[0]
                        && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $subscription = $this->createMock(SubscriptionRepositoryInterface::class);
        $subscription->method('fetchOne')->willReturn(true);
        $subscription->method('delete')->willThrowException(new Exception('exc'));
        $result = $this->service->unsubscribe(
            $subscription,
            $this->createMock(ThreadRepositoryInterface::class),
            $member
        );

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testUnsubscribeShouldReturnErrorWhenSubscriptionDoesntExist(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $subscription = $this->createMock(SubscriptionRepositoryInterface::class);
        $subscription->method('fetchOne')->willReturn(false);
        $result = $this->service->unsubscribe(
            $subscription,
            $this->createMock(ThreadRepositoryInterface::class),
            $member
        );

        self::assertFalse($result->getResult());
        self::assertSame('thread.not.subscribed', $result->getErrors()['api']);
    }
}
