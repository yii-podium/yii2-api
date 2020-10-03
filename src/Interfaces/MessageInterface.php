<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface MessageInterface
{
    /**
     * Returns the message repository.
     */
    public function getRepository(): MessageRepositoryInterface;

    /**
     * Send a new message (or a reply to the message) from the sender to the receiver.
     */
    public function send(
        MemberRepositoryInterface $sender,
        MemberRepositoryInterface $receiver,
        MessageRepositoryInterface $replyTo = null,
        array $data = []
    ): PodiumResponse;

    /**
     * Removes the member's side of the message.
     */
    public function remove(MessageRepositoryInterface $message, MemberRepositoryInterface $participant): PodiumResponse;

    /**
     * Archives the member's side of the message.
     */
    public function archive(
        MessageRepositoryInterface $message,
        MemberRepositoryInterface $participant
    ): PodiumResponse;

    /**
     * Revives the member's side of the message.
     */
    public function revive(MessageRepositoryInterface $message, MemberRepositoryInterface $participant): PodiumResponse;
}
