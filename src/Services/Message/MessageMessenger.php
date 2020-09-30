<?php

declare(strict_types=1);

namespace Podium\Api\Services\Message;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\SendEvent;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\MessageRepositoryInterface;
use Podium\Api\Interfaces\MessengerInterface;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class MessageMessenger extends Component implements MessengerInterface
{
    public const EVENT_BEFORE_SENDING = 'podium.message.sending.before';
    public const EVENT_AFTER_SENDING = 'podium.message.sending.after';

    /**
     * Calls before sending the message.
     */
    private function beforeSend(): bool
    {
        $event = new SendEvent();
        $this->trigger(self::EVENT_BEFORE_SENDING, $event);

        return $event->canSend;
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
            if ($sender->getId() === $receiver->getId()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'message.no.self.sending')]);
            }

            if (null !== $replyTo && !$replyTo->verifyParticipants($sender, $receiver)) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'message.wrong.reply')]);
            }

            if ($receiver->isIgnoring($sender)) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'message.receiver.rejected')]);
            }

            if (!$message->send($sender, $receiver, $replyTo, $data)) {
                throw new ServiceException($message->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while sending message', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterSend($message);

        return PodiumResponse::success();
    }

    /**
     * Calls after sending the message successfully.
     */
    private function afterSend(MessageRepositoryInterface $message): void
    {
        $this->trigger(self::EVENT_AFTER_SENDING, new SendEvent(['repository' => $message]));
    }
}
