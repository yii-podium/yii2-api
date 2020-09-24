<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\Components\PodiumResponse;

interface MessageArchiverInterface
{
    /**
     * Archives the message repository.
     */
    public function archive(
        MessageRepositoryInterface $message,
        MemberRepositoryInterface $participant
    ): PodiumResponse;

    /**
     * Revives the message repository.
     */
    public function revive(MessageRepositoryInterface $message, MemberRepositoryInterface $participant): PodiumResponse;
}
