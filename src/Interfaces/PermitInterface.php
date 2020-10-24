<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumDecision;
use Podium\Api\PodiumResponse;

interface PermitInterface
{
    /**
     * Returns role repository.
     */
    public function getRepository(): RoleRepositoryInterface;

    /**
     * Creates a role.
     */
    public function createRole(array $data): PodiumResponse;

    /**
     * Edits the role.
     */
    public function editRole(RoleRepositoryInterface $role, array $data = []): PodiumResponse;

    /**
     * Removes the role.
     */
    public function removeRole(RoleRepositoryInterface $role): PodiumResponse;

    /**
     * Grants the role to the member.
     */
    public function grantRole(MemberRepositoryInterface $member, RoleRepositoryInterface $role): PodiumResponse;

    /**
     * Revokes the role from the member.
     */
    public function revokeRole(MemberRepositoryInterface $member, RoleRepositoryInterface $role): PodiumResponse;

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
