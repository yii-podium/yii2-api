<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface GranterInterface
{
    /**
     * Grants the role to the member.
     */
    public function grant(MemberRepositoryInterface $member, RoleRepositoryInterface $role): PodiumResponse;

    /**
     * Revokes the role from the member.
     */
    public function revoke(MemberRepositoryInterface $member, RoleRepositoryInterface $role): PodiumResponse;
}
