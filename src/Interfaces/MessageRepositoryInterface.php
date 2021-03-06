<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

interface MessageRepositoryInterface extends RepositoryInterface
{
    public function getParticipant(MemberRepositoryInterface $member): MessageParticipantRepositoryInterface;

    public function isCompletelyDeleted(): bool;

    public function send(
        MemberRepositoryInterface $sender,
        MemberRepositoryInterface $receiver,
        MessageRepositoryInterface $replyTo = null,
        array $data = []
    ): bool;

    public function verifyParticipants(MemberRepositoryInterface $sender, MemberRepositoryInterface $receiver): bool;
}
