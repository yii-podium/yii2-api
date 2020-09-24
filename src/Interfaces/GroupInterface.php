<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\Components\PodiumResponse;

interface GroupInterface
{
    /**
     * Returns the repository.
     */
    public function getRepository(): GroupRepositoryInterface;

    /**
     * Creates group.
     */
    public function create(array $data = []): PodiumResponse;

    /**
     * Updates group.
     */
    public function edit(GroupRepositoryInterface $group, array $data = []): PodiumResponse;

    public function remove(GroupRepositoryInterface $group): PodiumResponse;

    public function join(GroupRepositoryInterface $group, MemberRepositoryInterface $member): PodiumResponse;

    public function leave(GroupRepositoryInterface $group, MemberRepositoryInterface $member): PodiumResponse;
}
