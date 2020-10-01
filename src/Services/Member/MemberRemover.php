<?php

declare(strict_types=1);

namespace Podium\Api\Services\Member;

use InvalidArgumentException;
use Podium\Api\Events\RemoveEvent;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\PodiumResponse;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class MemberRemover extends Component implements RemoverInterface
{
    public const EVENT_BEFORE_REMOVING = 'podium.member.removing.before';
    public const EVENT_AFTER_REMOVING = 'podium.member.removing.after';

    /**
     * Calls before removing the member.
     */
    private function beforeRemove(): bool
    {
        $event = new RemoveEvent();
        $this->trigger(self::EVENT_BEFORE_REMOVING, $event);

        return $event->canRemove;
    }

    /**
     * Removes the member.
     */
    public function remove(RepositoryInterface $member): PodiumResponse
    {
        if (!$member instanceof MemberRepositoryInterface) {
            return PodiumResponse::error(
                [
                    'exception' => new InvalidArgumentException(
                        'Member must be instance of Podium\Api\Interfaces\MemberRepositoryInterface!'
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
            if (!$member->delete()) {
                throw new ServiceException($member->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while deleting member', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterRemove();

        return PodiumResponse::success();
    }

    /**
     * Calls after removing the member successfully.
     */
    private function afterRemove(): void
    {
        $this->trigger(self::EVENT_AFTER_REMOVING);
    }
}
