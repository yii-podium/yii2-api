<?php

declare(strict_types=1);

namespace Podium\Api\Services\Group;

use Podium\Api\Events\GroupEvent;
use Podium\Api\Interfaces\GroupRepositoryInterface;
use Podium\Api\Interfaces\KeeperInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\PodiumResponse;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class GroupKeeper extends Component implements KeeperInterface
{
    public const EVENT_BEFORE_JOINING = 'podium.group.joining.before';
    public const EVENT_AFTER_JOINING = 'podium.group.joining.after';
    public const EVENT_BEFORE_LEAVING = 'podium.group.leaving.before';
    public const EVENT_AFTER_LEAVING = 'podium.group.leaving.after';

    /**
     * Calls before joining the group.
     */
    private function beforeJoin(): bool
    {
        $event = new GroupEvent();
        $this->trigger(self::EVENT_BEFORE_JOINING, $event);

        return $event->canJoin;
    }

    /**
     * Adds the repository to the group.
     */
    public function join(GroupRepositoryInterface $group, RepositoryInterface $repository): PodiumResponse
    {
        if (!$this->beforeJoin()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($repository->hasGroups([$group])) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'group.already.joined')]);
            }

            if (!$repository->join($group)) {
                throw new ServiceException($repository->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while joining group', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterJoin($group);

        return PodiumResponse::success();
    }

    /**
     * Calls after joining the group successfully.
     */
    private function afterJoin(RepositoryInterface $repository): void
    {
        $this->trigger(self::EVENT_AFTER_JOINING, new GroupEvent(['repository' => $repository]));
    }

    /**
     * Calls before leaving the group.
     */
    private function beforeLeave(): bool
    {
        $event = new GroupEvent();
        $this->trigger(self::EVENT_BEFORE_LEAVING, $event);

        return $event->canLeave;
    }

    /**
     * Removes the repository from the group.
     */
    public function leave(GroupRepositoryInterface $group, RepositoryInterface $repository): PodiumResponse
    {
        if (!$this->beforeLeave()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$repository->hasGroups([$group])) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'group.not.joined')]);
            }

            if (!$repository->leave($group)) {
                throw new ServiceException($repository->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while leaving group', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterLeave($group);

        return PodiumResponse::success();
    }

    /**
     * Calls after leaving the group successfully.
     */
    private function afterLeave(RepositoryInterface $repository): void
    {
        $this->trigger(self::EVENT_AFTER_LEAVING, new GroupEvent(['repository' => $repository]));
    }
}
