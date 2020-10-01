<?php

declare(strict_types=1);

namespace Podium\Api\Services\Poll;

use InvalidArgumentException;
use Podium\Api\Events\RemoveEvent;
use Podium\Api\Interfaces\PollPostRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\PodiumResponse;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class PollRemover extends Component implements RemoverInterface
{
    public const EVENT_BEFORE_REMOVING = 'podium.poll.removing.before';
    public const EVENT_AFTER_REMOVING = 'podium.poll.removing.after';

    private function beforeRemove(): bool
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
        if (!$post instanceof PollPostRepositoryInterface) {
            return PodiumResponse::error(
                [
                    'exception' => new InvalidArgumentException(
                        'Post must be instance of Podium\Api\Interfaces\PollPostRepositoryInterface!'
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
            $poll = $post->getPoll();
            if (!$poll->delete()) {
                throw new ServiceException($poll->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while deleting poll', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterRemove();

        return PodiumResponse::success();
    }

    private function afterRemove(): void
    {
        $this->trigger(self::EVENT_AFTER_REMOVING);
    }
}
