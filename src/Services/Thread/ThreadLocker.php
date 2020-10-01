<?php

declare(strict_types=1);

namespace Podium\Api\Services\Thread;

use Podium\Api\Events\LockEvent;
use Podium\Api\Interfaces\LockerInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\PodiumResponse;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class ThreadLocker extends Component implements LockerInterface
{
    public const EVENT_BEFORE_LOCKING = 'podium.thread.locking.before';
    public const EVENT_AFTER_LOCKING = 'podium.thread.locking.after';
    public const EVENT_BEFORE_UNLOCKING = 'podium.thread.unlocking.before';
    public const EVENT_AFTER_UNLOCKING = 'podium.thread.unlocking.after';

    /**
     * Calls before locking the thread.
     */
    private function beforeLock(): bool
    {
        $event = new LockEvent();
        $this->trigger(self::EVENT_BEFORE_LOCKING, $event);

        return $event->canLock;
    }

    /**
     * Locks the thread.
     */
    public function lock(ThreadRepositoryInterface $thread): PodiumResponse
    {
        if (!$this->beforeLock()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$thread->lock()) {
                throw new ServiceException($thread->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while locking thread', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterLock($thread);

        return PodiumResponse::success();
    }

    /**
     * Calls after locking the thread successfully.
     */
    private function afterLock(ThreadRepositoryInterface $thread): void
    {
        $this->trigger(self::EVENT_AFTER_LOCKING, new LockEvent(['repository' => $thread]));
    }

    /**
     * Calls before unlocking the thread.
     */
    private function beforeUnlock(): bool
    {
        $event = new LockEvent();
        $this->trigger(self::EVENT_BEFORE_UNLOCKING, $event);

        return $event->canUnlock;
    }

    /**
     * Unlocks the thread.
     */
    public function unlock(ThreadRepositoryInterface $thread): PodiumResponse
    {
        if (!$this->beforeUnlock()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$thread->unlock()) {
                throw new ServiceException($thread->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while unlocking thread', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterUnlock($thread);

        return PodiumResponse::success();
    }

    /**
     * Calls after unlocking the thread successfully.
     */
    private function afterUnlock(ThreadRepositoryInterface $thread): void
    {
        $this->trigger(self::EVENT_AFTER_UNLOCKING, new LockEvent(['repository' => $thread]));
    }
}
