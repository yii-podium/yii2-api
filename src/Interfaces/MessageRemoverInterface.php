<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface MessageRemoverInterface
{
    public function remove(MessageRepositoryInterface $message, MemberRepositoryInterface $participant): PodiumResponse;
}
