<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface VoterInterface
{
    public function vote(
        PollPostRepositoryInterface $post,
        MemberRepositoryInterface $member,
        array $answers
    ): PodiumResponse;
}
