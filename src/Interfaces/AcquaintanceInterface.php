<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface AcquaintanceInterface
{
    /**
     * Handles befriending process.
     */
    public function befriend(
        AcquaintanceRepositoryInterface $acquaintance,
        MemberRepositoryInterface $member,
        MemberRepositoryInterface $target
    ): PodiumResponse;

    /**
     * Handles ignoring process.
     */
    public function ignore(
        AcquaintanceRepositoryInterface $acquaintance,
        MemberRepositoryInterface $member,
        MemberRepositoryInterface $target
    ): PodiumResponse;

    /**
     * Handles disconnecting process.
     */
    public function disconnect(
        AcquaintanceRepositoryInterface $acquaintance,
        MemberRepositoryInterface $member,
        MemberRepositoryInterface $target
    ): PodiumResponse;
}
