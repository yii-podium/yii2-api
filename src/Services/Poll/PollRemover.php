<?php

declare(strict_types=1);

namespace Podium\Api\Services\Poll;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\RemoveEvent;
use Podium\Api\Interfaces\PollPostRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class PollRemover extends Component implements RemoverInterface
{
    public const EVENT_BEFORE_REMOVING = 'podium.poll.removing.before';
    public const EVENT_AFTER_REMOVING = 'podium.poll.removing.after';

    public function beforeRemove(): bool
    {
        $event = new RemoveEvent();
        $this->trigger(self::EVENT_BEFORE_REMOVING, $event);

        return $event->canRemove;
    }

    /**
     * Removes the poll.
     */
    public function remove(RepositoryInterface $post): PodiumResponse
    {
        if (!$post instanceof PollPostRepositoryInterface || !$this->beforeRemove()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$post->getPoll()->delete()) {
                return PodiumResponse::error();
            }

            $this->afterRemove();
            $transaction->commit();

            return PodiumResponse::success();
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while deleting poll', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }
    }

    public function afterRemove(): void
    {
        $this->trigger(self::EVENT_AFTER_REMOVING);
    }
}
