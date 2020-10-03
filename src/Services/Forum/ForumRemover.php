<?php

declare(strict_types=1);

namespace Podium\Api\Services\Forum;

use InvalidArgumentException;
use Podium\Api\Events\RemoveEvent;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\PodiumResponse;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class ForumRemover extends Component implements RemoverInterface
{
    public const EVENT_BEFORE_REMOVING = 'podium.forum.removing.before';
    public const EVENT_AFTER_REMOVING = 'podium.forum.removing.after';

    /**
     * Calls before removing the forum.
     */
    private function beforeRemove(): bool
    {
        $event = new RemoveEvent();
        $this->trigger(self::EVENT_BEFORE_REMOVING, $event);

        return $event->canRemove;
    }

    /**
     * Removes the forum.
     */
    public function remove(RepositoryInterface $forum): PodiumResponse
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

        if (!$this->beforeRemove()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$forum->isArchived()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'forum.must.be.archived')]);
            }

            if (!$forum->delete()) {
                throw new ServiceException($forum->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while deleting forum', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterRemove();

        return PodiumResponse::success();
    }

    /**
     * Calls after removing the forum successfully.
     */
    private function afterRemove(): void
    {
        $this->trigger(self::EVENT_AFTER_REMOVING);
    }
}
