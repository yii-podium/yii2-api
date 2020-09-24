<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\Components\PodiumResponse;

interface SubscriberInterface
{
    /**
     * Subscribes the member to the thread.
     */
    public function subscribe(
        SubscriptionRepositoryInterface $subscription,
        ThreadRepositoryInterface $thread,
        MemberRepositoryInterface $member
    ): PodiumResponse;

    /**
     * Unsubscribes the member from the thread.
     */
    public function unsubscribe(
        SubscriptionRepositoryInterface $subscription,
        ThreadRepositoryInterface $thread,
        MemberRepositoryInterface $member
    ): PodiumResponse;
}
