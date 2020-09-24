<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\Components\PodiumResponse;

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
     * Handles unfriending process.
     */
    public function unfriend(
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
     * Handles unignoring process.
     */
    public function unignore(
        AcquaintanceRepositoryInterface $acquaintance,
        MemberRepositoryInterface $member,
        MemberRepositoryInterface $target
    ): PodiumResponse;
}
