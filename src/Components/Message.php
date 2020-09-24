<?php

declare(strict_types=1);

namespace Podium\Api\Components;

use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\MessageArchiverInterface;
use Podium\Api\Interfaces\MessageInterface;
use Podium\Api\Interfaces\MessageRemoverInterface;
use Podium\Api\Interfaces\MessageRepositoryInterface;
use Podium\Api\Interfaces\MessengerInterface;
use Podium\Api\Services\Message\MessageArchiver;
use Podium\Api\Services\Message\MessageMessenger;
use Podium\Api\Services\Message\MessageRemover;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\di\Instance;

final class Message extends Component implements MessageInterface
{
    /**
     * @var string|array|MessengerInterface
     */
    public $messengerConfig = MessageMessenger::class;

    /**
     * @var string|array|MessageRemoverInterface
     */
    public $removerConfig = MessageRemover::class;

    /**
     * @var string|array|MessageArchiverInterface
     */
    public $archiverConfig = MessageArchiver::class;

    /**
     * @var string|array|MessageRepositoryInterface
     */
    public $repositoryConfig;

    private ?MessageRepositoryInterface $repository = null;

    /**
     * @throws InvalidConfigException
     */
    public function getRepository(): MessageRepositoryInterface
    {
        if (null === $this->repository) {
            /** @var MessageRepositoryInterface $repository */
            $repository = Instance::ensure($this->repositoryConfig, MessageRepositoryInterface::class);
            $this->repository = $repository;
        }

        return $this->repository;
    }

    private ?MessengerInterface $messenger = null;

    /**
     * @throws InvalidConfigException
     */
    public function getMessenger(): MessengerInterface
    {
        if (null === $this->messenger) {
            /** @var MessengerInterface $messenger */
            $messenger = Instance::ensure($this->messengerConfig, MessengerInterface::class);
            $this->messenger = $messenger;
        }

        return $this->messenger;
    }

    /**
     * Sends message.
     *
     * @throws InvalidConfigException
     */
    public function send(
        MemberRepositoryInterface $sender,
        MemberRepositoryInterface $receiver,
        MessageRepositoryInterface $replyTo = null,
        array $data = []
    ): PodiumResponse {
        return $this->getMessenger()->send($this->getRepository(), $sender, $receiver, $replyTo, $data);
    }

    private ?MessageRemoverInterface $remover = null;

    /**
     * @throws InvalidConfigException
     */
    public function getRemover(): MessageRemoverInterface
    {
        if (null === $this->remover) {
            /** @var MessageRemoverInterface $remover */
            $remover = Instance::ensure($this->removerConfig, MessageRemoverInterface::class);
            $this->remover = $remover;
        }

        return $this->remover;
    }

    /**
     * Deletes message copy.
     *
     * @throws InvalidConfigException
     */
    public function remove(MessageRepositoryInterface $message, MemberRepositoryInterface $participant): PodiumResponse
    {
        return $this->getRemover()->remove($message, $participant);
    }

    private ?MessageArchiverInterface $archiver = null;

    /**
     * @throws InvalidConfigException
     */
    public function getArchiver(): MessageArchiverInterface
    {
        if (null === $this->archiver) {
            /** @var MessageArchiverInterface $archiver */
            $archiver = Instance::ensure($this->archiverConfig, MessageArchiverInterface::class);
            $this->archiver = $archiver;
        }

        return $this->archiver;
    }

    /**
     * Archives message copy.
     *
     * @throws InvalidConfigException
     */
    public function archive(MessageRepositoryInterface $message, MemberRepositoryInterface $participant): PodiumResponse
    {
        return $this->getArchiver()->archive($message, $participant);
    }

    /**
     * Revives message copy.
     *
     * @throws InvalidConfigException
     */
    public function revive(MessageRepositoryInterface $message, MemberRepositoryInterface $participant): PodiumResponse
    {
        return $this->getArchiver()->revive($message, $participant);
    }
}
