<?php

declare(strict_types=1);

namespace Podium\Api\Services\Forum;

use InvalidArgumentException;
use Podium\Api\Events\HideEvent;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\HiderInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\PodiumResponse;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class ForumHider extends Component implements HiderInterface
{
    public const EVENT_BEFORE_HIDING = 'podium.forum.hiding.before';
    public const EVENT_AFTER_HIDING = 'podium.forum.hiding.after';
    public const EVENT_BEFORE_REVEALING = 'podium.forum.revealing.before';
    public const EVENT_AFTER_REVEALING = 'podium.forum.revealing.after';

    /**
     * Calls before hiding the forum.
     */
    private function beforeHide(): bool
    {
        $event = new HideEvent();
        $this->trigger(self::EVENT_BEFORE_HIDING, $event);

        return $event->canHide;
    }

    /**
     * Hides the forum.
     */
    public function hide(RepositoryInterface $forum): PodiumResponse
    {
        if (!$forum instanceof ForumRepositoryInterface) {
            return PodiumResponse::error(
                [
                    'exception' => new InvalidArgumentException(
                        'Forum must be instance of Podium\Api\Interfaces\ForumRepositoryInterface!'
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
            if ($forum->isHidden()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'forum.already.hidden')]);
            }

            if (!$forum->hide()) {
                throw new ServiceException($forum->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while hiding forum', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterHide($forum);

        return PodiumResponse::success();
    }

    /**
     * Calls after successful hiding the forum.
     */
    private function afterHide(ForumRepositoryInterface $category): void
    {
        $this->trigger(self::EVENT_AFTER_HIDING, new HideEvent(['repository' => $category]));
    }

    /**
     * Calls before revealing the forum.
     */
    private function beforeReveal(): bool
    {
        $event = new HideEvent();
        $this->trigger(self::EVENT_BEFORE_REVEALING, $event);

        return $event->canReveal;
    }

    /**
     * Reveal the forum.
     */
    public function reveal(RepositoryInterface $forum): PodiumResponse
    {
        if (!$forum instanceof ForumRepositoryInterface) {
            return PodiumResponse::error(
                [
                    'exception' => new InvalidArgumentException(
                        'Forum must be instance of Podium\Api\Interfaces\ForumRepositoryInterface!'
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
            if (!$forum->isHidden()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'forum.not.hidden')]);
            }

            if (!$forum->reveal()) {
                throw new ServiceException($forum->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while revealing forum', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterReveal($forum);

        return PodiumResponse::success();
    }

    /**
     * Calls after successful revealing the forum.
     */
    private function afterReveal(ForumRepositoryInterface $category): void
    {
        $this->trigger(self::EVENT_AFTER_REVEALING, new HideEvent(['repository' => $category]));
    }
}
