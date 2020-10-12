<?php

declare(strict_types=1);

namespace Podium\Api\Services\Thread;

use InvalidArgumentException;
use Podium\Api\Events\HideEvent;
use Podium\Api\Interfaces\HiderInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\PodiumResponse;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class ThreadHider extends Component implements HiderInterface
{
    public const EVENT_BEFORE_HIDING = 'podium.thread.hiding.before';
    public const EVENT_AFTER_HIDING = 'podium.thread.hiding.after';
    public const EVENT_BEFORE_REVEALING = 'podium.thread.revealing.before';
    public const EVENT_AFTER_REVEALING = 'podium.thread.revealing.after';

    /**
     * Calls before hiding the thread.
     */
    private function beforeHide(): bool
    {
        $event = new HideEvent();
        $this->trigger(self::EVENT_BEFORE_HIDING, $event);

        return $event->canHide;
    }

    /**
     * Hides the thread.
     */
    public function hide(RepositoryInterface $thread): PodiumResponse
    {
        if (!$thread instanceof ThreadRepositoryInterface) {
            return PodiumResponse::error(
                [
                    'exception' => new InvalidArgumentException(
                        'Thread must be instance of Podium\Api\Interfaces\ThreadRepositoryInterface!'
                    ),
                ]
            );
        }

        if (!$this->beforeHide()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$thread->hide()) {
                throw new ServiceException($thread->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while hiding thread', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterHide($thread);

        return PodiumResponse::success();
    }

    /**
     * Calls after successful hiding the thread.
     */
    private function afterHide(ThreadRepositoryInterface $category): void
    {
        $this->trigger(self::EVENT_AFTER_HIDING, new HideEvent(['repository' => $category]));
    }

    /**
     * Calls before revealing the thread.
     */
    private function beforeReveal(): bool
    {
        $event = new HideEvent();
        $this->trigger(self::EVENT_BEFORE_REVEALING, $event);

        return $event->canReveal;
    }

    /**
     * Reveal the thread.
     */
    public function reveal(RepositoryInterface $thread): PodiumResponse
    {
        if (!$thread instanceof ThreadRepositoryInterface) {
            return PodiumResponse::error(
                [
                    'exception' => new InvalidArgumentException(
                        'Thread must be instance of Podium\Api\Interfaces\ThreadRepositoryInterface!'
                    ),
                ]
            );
        }

        if (!$this->beforeReveal()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$thread->reveal()) {
                throw new ServiceException($thread->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while revealing thread', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterReveal($thread);

        return PodiumResponse::success();
    }

    /**
     * Calls after successful revealing the thread.
     */
    private function afterReveal(ThreadRepositoryInterface $category): void
    {
        $this->trigger(self::EVENT_AFTER_REVEALING, new HideEvent(['repository' => $category]));
    }
}
