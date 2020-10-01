<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface MessageInterface
{
    public function getRepository(): MessageRepositoryInterface;

    public function send(
        MemberRepositoryInterface $sender,
        MemberRepositoryInterface $receiver,
        MessageRepositoryInterface $replyTo = null,
        array $data = []
    ): PodiumResponse;

    public function remove(MessageRepositoryInterface $message, MemberRepositoryInterface $participant): PodiumResponse;

    public function archive(
        MessageRepositoryInterface $message,
        MemberRepositoryInterface $participant
    ): PodiumResponse;

    public function revive(MessageRepositoryInterface $message, MemberRepositoryInterface $participant): PodiumResponse;
}
