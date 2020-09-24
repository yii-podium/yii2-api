<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

interface GroupRepositoryInterface extends RepositoryInterface
{
    public function create(array $data = []): bool;

    public function getGroupMember(): GroupMemberRepositoryInterface;
}
