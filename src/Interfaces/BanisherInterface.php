<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface BanisherInterface
{
    public function ban(MemberRepositoryInterface $member): PodiumResponse;

    public function unban(MemberRepositoryInterface $member): PodiumResponse;
}
