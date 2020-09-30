<?php

declare(strict_types=1);

namespace Podium\Api\Services\Logger;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\RemoveEvent;
use Podium\Api\Interfaces\LogRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class LoggerRemover extends Component implements RemoverInterface
{
    public const EVENT_BEFORE_REMOVING = 'podium.log.removing.before';
    public const EVENT_AFTER_REMOVING = 'podium.log.removing.after';

    /**
     * Calls before removing the log.
     */
    private function beforeRemove(): bool
    {
        $event = new RemoveEvent();
        $this->trigger(self::EVENT_BEFORE_REMOVING, $event);

        return $event->canRemove;
    }

    /**
     * Removes the log.
     */
    public function remove(RepositoryInterface $log): PodiumResponse
    {
        if (!$log instanceof LogRepositoryInterface || !$this->beforeRemove()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$log->delete()) {
                throw new ServiceException($log->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while deleting log', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterRemove();

        return PodiumResponse::success();
    }

    /**
     * Calls after removing the log successfully.
     */
    private function afterRemove(): void
    {
        $this->trigger(self::EVENT_AFTER_REMOVING);
    }
}
