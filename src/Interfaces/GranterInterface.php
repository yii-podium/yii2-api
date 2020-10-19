<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface GranterInterface
{
    public function grant(MemberRepositoryInterface $member, RoleRepositoryInterface $role): PodiumResponse;

    public function revoke(MemberRepositoryInterface $member, RoleRepositoryInterface $role): PodiumResponse;
}
