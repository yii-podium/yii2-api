<?php

declare(strict_types=1);

namespace Podium\Api\Services\Message;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\ArchiveEvent;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\MessageRepositoryInterface;
use Podium\Api\Interfaces\MessengerInterface;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class MessageMessenger extends Component implements MessengerInterface
{
    public const EVENT_BEFORE_SENDING = 'podium.message.sending.before';
    public const EVENT_AFTER_SENDING = 'podium.message.sending.after';

    public function beforeSend(): bool
    {
        $event = new ArchiveEvent();
        $this->trigger(self::EVENT_BEFORE_SENDING, $event);

        return $event->canArchive;
    }

    /**
     * Sends the message.
     */
    public function send(
        MessageRepositoryInterface $message,
        MemberRepositoryInterface $sender,
        MemberRepositoryInterface $receiver,
        MessageRepositoryInterface $replyTo = null,
        array $data = []
    ): PodiumResponse {
        if (!$this->beforeSend()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$message->send($sender, $receiver, $replyTo, $data)) {
                return PodiumResponse::error($message->getErrors());
            }

            $this->afterSend($message);
            $transaction->commit();

            return PodiumResponse::success();
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while sending message', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error();
        }
    }

    public function afterSend(MessageRepositoryInterface $message): void
    {
        $this->trigger(self::EVENT_AFTER_SENDING, new ArchiveEvent(['repository' => $message]));
    }
}
