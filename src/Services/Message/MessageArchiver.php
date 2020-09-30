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

    /**
     * Calls before archiving the message side.
     */
    private function beforeArchive(): bool
    {
        $event = new ArchiveEvent();
        $this->trigger(self::EVENT_BEFORE_ARCHIVING, $event);

        return $event->canArchive;
    }

    /**
     * Archives the message side.
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

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterArchive($message);

        return PodiumResponse::success();
    }

    /**
     * Calls after archiving the message successfully.
     */
    private function afterArchive(MessageRepositoryInterface $message): void
    {
        $this->trigger(self::EVENT_AFTER_ARCHIVING, new ArchiveEvent(['repository' => $message]));
    }

    /**
     * Calls before reviving the message side.
     */
    private function beforeRevive(): bool
    {
        $event = new ArchiveEvent();
        $this->trigger(self::EVENT_BEFORE_REVIVING, $event);

        return $event->canRevive;
    }

    /**
     * Revives the message side.
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

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterRevive($message);

        return PodiumResponse::success();
    }

    /**
     * Calls after reviving the message successfully.
     */
    private function afterRevive(MessageRepositoryInterface $message): void
    {
        $this->trigger(self::EVENT_AFTER_REVIVING, new ArchiveEvent(['repository' => $message]));
    }
}
