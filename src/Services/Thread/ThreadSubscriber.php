<?php

declare(strict_types=1);

namespace Podium\Api\Services\Thread;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\SubscriptionEvent;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\SubscriberInterface;
use Podium\Api\Interfaces\SubscriptionRepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Throwable;
use Yii;
use yii\base\Component;

final class ThreadSubscriber extends Component implements SubscriberInterface
{
    public const EVENT_BEFORE_SUBSCRIBING = 'podium.subscription.subscribing.before';
    public const EVENT_AFTER_SUBSCRIBING = 'podium.subscription.subscribing.after';
    public const EVENT_BEFORE_UNSUBSCRIBING = 'podium.subscription.unsubscribing.before';
    public const EVENT_AFTER_UNSUBSCRIBING = 'podium.subscription.unsubscribing.after';

    /**
     * Calls before subscribing to the thread.
     */
    public function beforeSubscribe(): bool
    {
        $event = new SubscriptionEvent();
        $this->trigger(self::EVENT_BEFORE_SUBSCRIBING, $event);

        return $event->canSubscribe;
    }

    /**
     * Subscribes to the thread.
     */
    public function subscribe(
        SubscriptionRepositoryInterface $subscription,
        ThreadRepositoryInterface $thread,
        MemberRepositoryInterface $member
    ): PodiumResponse {
        if (!$this->beforeSubscribe()) {
            return PodiumResponse::error();
        }

        try {
            if ($subscription->isMemberSubscribed($member, $thread)) {
                return PodiumResponse::error(['api' => Yii::t('podium.error', 'thread.already.subscribed')]);
            }

            if (!$subscription->subscribe($member, $thread)) {
                return PodiumResponse::error($subscription->getErrors());
            }

            $this->afterSubscribe($subscription);

            return PodiumResponse::success();
        } catch (Throwable $exc) {
            Yii::error(['Exception while subscribing thread', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }
    }

    /**
     * Calls after subscribing to the thread.
     */
    public function afterSubscribe(SubscriptionRepositoryInterface $subscription): void
    {
        $this->trigger(self::EVENT_AFTER_SUBSCRIBING, new SubscriptionEvent(['repository' => $subscription]));
    }

    /**
     * Calls before unsubscribing from the thread.
     */
    public function beforeUnsubscribe(): bool
    {
        $event = new SubscriptionEvent();
        $this->trigger(self::EVENT_BEFORE_UNSUBSCRIBING, $event);

        return $event->canUnsubscribe;
    }

    /**
     * Unsubscribes from the thread.
     */
    public function unsubscribe(
        SubscriptionRepositoryInterface $subscription,
        ThreadRepositoryInterface $thread,
        MemberRepositoryInterface $member
    ): PodiumResponse {
        if (!$this->beforeUnsubscribe()) {
            return PodiumResponse::error();
        }

        try {
            if (!$subscription->fetchOne($member, $thread)) {
                return PodiumResponse::error(['api' => Yii::t('podium.error', 'thread.not.subscribed')]);
            }

            if (!$subscription->delete()) {
                return PodiumResponse::error($subscription->getErrors());
            }

            $this->afterUnsubscribe();

            return PodiumResponse::success();
        } catch (Throwable $exc) {
            Yii::error(
                ['Exception while unsubscribing thread', $exc->getMessage(), $exc->getTraceAsString()],
                'podium'
            );

            return PodiumResponse::error(['exception' => $exc]);
        }
    }

    /**
     * Calls after unsubscribing from the thread successfully.
     */
    public function afterUnsubscribe(): void
    {
        $this->trigger(self::EVENT_AFTER_UNSUBSCRIBING);
    }
}
