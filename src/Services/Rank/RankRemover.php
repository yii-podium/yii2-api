<?php

declare(strict_types=1);

namespace Podium\Api\Services\Rank;

use InvalidArgumentException;
use Podium\Api\Events\RemoveEvent;
use Podium\Api\Interfaces\RankRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\PodiumResponse;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class RankRemover extends Component implements RemoverInterface
{
    public const EVENT_BEFORE_REMOVING = 'podium.rank.removing.before';
    public const EVENT_AFTER_REMOVING = 'podium.rank.removing.after';

    /**
     * Calls before removing the rank.
     */
    private function beforeRemove(): bool
    {
        $event = new RemoveEvent();
        $this->trigger(self::EVENT_BEFORE_REMOVING, $event);

        return $event->canRemove;
    }

    /**
     * Removes the rank.
     */
    public function remove(RepositoryInterface $rank): PodiumResponse
    {
        if (!$rank instanceof RankRepositoryInterface) {
            return PodiumResponse::error(
                [
                    'exception' => new InvalidArgumentException(
                        'Rank must be instance of Podium\Api\Interfaces\RankRepositoryInterface!'
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
            if (!$rank->delete()) {
                throw new ServiceException($rank->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while deleting rank', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterRemove();

        return PodiumResponse::success();
    }

    /**
     * Calls after removing the rank successfully.
     */
    private function afterRemove(): void
    {
        $this->trigger(self::EVENT_AFTER_REMOVING);
    }
}
