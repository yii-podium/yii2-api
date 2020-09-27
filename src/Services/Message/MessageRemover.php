<?php

declare(strict_types=1);

namespace Podium\Api\Services\Message;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\RemoveEvent;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\MessageRemoverInterface;
use Podium\Api\Interfaces\MessageRepositoryInterface;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class MessageRemover extends Component implements MessageRemoverInterface
{
    public const EVENT_BEFORE_REMOVING = 'podium.message.removing.before';
    public const EVENT_AFTER_REMOVING = 'podium.message.removing.after';

    public function beforeRemove(): bool
    {
        $event = new RemoveEvent();
        $this->trigger(self::EVENT_BEFORE_REMOVING, $event);

        return $event->canRemove;
    }

    public function remove(MessageRepositoryInterface $message, MemberRepositoryInterface $participant): PodiumResponse
    {
        if (!$this->beforeRemove()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $messageSide = $message->getParticipant($participant);

            if (!$messageSide->isArchived()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'message.must.be.archived')]);
            }

            if (!$messageSide->delete()) {
                throw new ServiceException($messageSide->getErrors());
            }

            if ($message->isCompletelyDeleted() && !$message->delete()) {
                throw new ServiceException($message->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while removing message', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error();
        }

        $this->afterRemove();

        return PodiumResponse::success();
    }

    public function afterRemove(): void
    {
        $this->trigger(self::EVENT_AFTER_REMOVING);
    }
}
