<?php

declare(strict_types=1);

namespace Podium\Api\Services\Logger;

use Podium\Api\Events\BuildEvent;
use Podium\Api\Interfaces\LogBuilderInterface;
use Podium\Api\Interfaces\LogRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\PodiumResponse;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class LoggerBuilder extends Component implements LogBuilderInterface
{
    public const EVENT_BEFORE_CREATING = 'podium.log.creating.before';
    public const EVENT_AFTER_CREATING = 'podium.log.creating.after';

    /**
     * Calls before creating the thread.
     */
    private function beforeCreate(): bool
    {
        $event = new BuildEvent();
        $this->trigger(self::EVENT_BEFORE_CREATING, $event);

        return $event->canCreate;
    }

    /**
     * Creates new log.
     */
    public function create(
        LogRepositoryInterface $log,
        MemberRepositoryInterface $author,
        string $action,
        array $data = []
    ): PodiumResponse {
        if (!$this->beforeCreate()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$log->create($author, $action, $data)) {
                throw new ServiceException($log->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while creating log', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterCreate($log);

        return PodiumResponse::success();
    }

    /**
     * Calls after creating the log successfully.
     */
    private function afterCreate(LogRepositoryInterface $thread): void
    {
        $this->trigger(self::EVENT_AFTER_CREATING, new BuildEvent(['repository' => $thread]));
    }
}
