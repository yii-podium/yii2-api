<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumDecision;
use Podium\Api\PodiumResponse;

interface PermitInterface
{
    public function createRole(array $data): PodiumResponse;

    public function editRole(RoleRepositoryInterface $role, array $data): PodiumResponse;

    public function removeRole(RoleRepositoryInterface $role): PodiumResponse;

    public function grantRole(MemberRepositoryInterface $member, RoleRepositoryInterface $role): PodiumResponse;

    public function revokeRole(MemberRepositoryInterface $member, RoleRepositoryInterface $role): PodiumResponse;

    public function check(
        DeciderInterface $decider,
        string $type,
        RepositoryInterface $subject = null,
        MemberRepositoryInterface $member = null
    ): PodiumDecision;
}
