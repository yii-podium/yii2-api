<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumDecision;

interface CheckerInterface
{
    /**
     * Checks the member's permit by the type for accessing the subject.
     */
    public function check(
        DeciderInterface $decider,
        string $type,
        RepositoryInterface $subject = null,
        MemberRepositoryInterface $member = null
    ): PodiumDecision;
}
