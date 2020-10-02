<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface GroupInterface
{
    /**
     * Returns the group repository.
     */
    public function getRepository(): GroupRepositoryInterface;

    /**
     * Creates a group.
     */
    public function create(array $data = []): PodiumResponse;

    /**
     * Edits the group.
     */
    public function edit(GroupRepositoryInterface $group, array $data = []): PodiumResponse;

    /**
     * Removes the group.
     */
    public function remove(GroupRepositoryInterface $group): PodiumResponse;

    /**
     * Joins the group as the member.
     */
    public function join(GroupRepositoryInterface $group, MemberRepositoryInterface $member): PodiumResponse;

    /**
     * Leaves the group as the member.
     */
    public function leave(GroupRepositoryInterface $group, MemberRepositoryInterface $member): PodiumResponse;
}
