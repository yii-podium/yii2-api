<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

interface GroupMemberRepositoryInterface
{
    public function create(GroupRepositoryInterface $group, MemberRepositoryInterface $member, array $data = []): bool;

    public function fetchOne(GroupRepositoryInterface $group, MemberRepositoryInterface $member): bool;

    public function getErrors(): array;

    public function delete(): bool;

    public function getParent(): RepositoryInterface;
}
