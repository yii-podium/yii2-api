<?php

declare(strict_types=1);

namespace Podium\Api\Services\Thread;

use Podium\Api\Events\SubscriptionEvent;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\SubscriberInterface;
use Podium\Api\Interfaces\SubscriptionRepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\PodiumResponse;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class ThreadSubscriber extends Component implements SubscriberInterface
{
    public const EVENT_BEFORE_SUBSCRIBING = 'podium.subscription.subscribing.before';
    public const EVENT_AFTER_SUBSCRIBING = 'podium.subscription.subscribing.after';
    public const EVENT_BEFORE_UNSUBSCRIBING = 'podium.subscription.unsubscribing.before';
    public const EVENT_AFTER_UNSUBSCRIBING = 'podium.subscription.unsubscribing.after';

    /**
     * Calls before subscribing to the thread.
     */
    private function beforeSubscribe(): bool
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

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($subscription->isMemberSubscribed($member, $thread)) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'thread.already.subscribed')]);
            }

            if (!$subscription->subscribe($member, $thread)) {
                throw new ServiceException($subscription->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while subscribing thread', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterSubscribe($subscription);

        return PodiumResponse::success();
    }

    /**
     * Calls after subscribing to the thread.
     */
    private function afterSubscribe(SubscriptionRepositoryInterface $subscription): void
    {
        $this->trigger(self::EVENT_AFTER_SUBSCRIBING, new SubscriptionEvent(['repository' => $subscription]));
    }

    /**
     * Calls before unsubscribing from the thread.
     */
    private function beforeUnsubscribe(): bool
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

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$subscription->fetchOne($member, $thread)) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'thread.not.subscribed')]);
            }

            if (!$subscription->delete()) {
                throw new ServiceException($subscription->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(
                ['Exception while unsubscribing thread', $exc->getMessage(), $exc->getTraceAsString()],
                'podium'
            );

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterUnsubscribe();

        return PodiumResponse::success();
    }

    /**
     * Calls after unsubscribing from the thread successfully.
     */
    private function afterUnsubscribe(): void
    {
        $this->trigger(self::EVENT_AFTER_UNSUBSCRIBING);
    }
}
