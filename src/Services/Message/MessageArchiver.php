<?php

declare(strict_types=1);

namespace Podium\Api\Services\Message;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\ArchiveEvent;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\MessageArchiverInterface;
use Podium\Api\Interfaces\MessageRepositoryInterface;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class MessageArchiver extends Component implements MessageArchiverInterface
{
    public const EVENT_BEFORE_ARCHIVING = 'podium.message.archiving.before';
    public const EVENT_AFTER_ARCHIVING = 'podium.message.archiving.after';
    public const EVENT_BEFORE_REVIVING = 'podium.message.reviving.before';
    public const EVENT_AFTER_REVIVING = 'podium.message.reviving.after';

    public function beforeArchive(): bool
    {
        $event = new ArchiveEvent();
        $this->trigger(self::EVENT_BEFORE_ARCHIVING, $event);

        return $event->canArchive;
    }

    /**
     * Archives the message.
     */
    public function archive(MessageRepositoryInterface $message, MemberRepositoryInterface $participant): PodiumResponse
    {
        if (!$this->beforeArchive()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $messageSide = $message->getParticipant($participant);

            if ($messageSide->isArchived()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'message.already.archived')]);
            }

            if (!$messageSide->archive()) {
                throw new ServiceException($messageSide->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while archiving message', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error();
        }

        $this->afterArchive($message);

        return PodiumResponse::success();
    }

    public function afterArchive(MessageRepositoryInterface $message): void
    {
        $this->trigger(self::EVENT_AFTER_ARCHIVING, new ArchiveEvent(['repository' => $message]));
    }

    public function beforeRevive(): bool
    {
        $event = new ArchiveEvent();
        $this->trigger(self::EVENT_BEFORE_REVIVING, $event);

        return $event->canRevive;
    }

    /**
     * Revives the message.
     */
    public function revive(MessageRepositoryInterface $message, MemberRepositoryInterface $participant): PodiumResponse
    {
        if (!$this->beforeRevive()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $messageSide = $message->getParticipant($participant);

            if (!$messageSide->isArchived()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'message.not.archived')]);
            }

            if (!$messageSide->revive()) {
                throw new ServiceException($messageSide->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while reviving message', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error();
        }

        $this->afterRevive($message);

        return PodiumResponse::success();
    }

    public function afterRevive(MessageRepositoryInterface $message): void
    {
        $this->trigger(self::EVENT_AFTER_REVIVING, new ArchiveEvent(['repository' => $message]));
    }
}
