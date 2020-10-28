<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

interface GroupRepositoryInterface extends RepositoryInterface
{
    public function create(array $data = []): bool;

    public function addMember(MemberRepositoryInterface $member): bool;

    public function removeMember(MemberRepositoryInterface $member): bool;
}
