<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface KeeperInterface
{
    /**
     * Joins group.
     */
    public function join(GroupRepositoryInterface $group, MemberRepositoryInterface $member): PodiumResponse;

    /**
     * Leaves group.
     */
    public function leave(GroupRepositoryInterface $group, MemberRepositoryInterface $member): PodiumResponse;

    /**
     * Adds to the group.
     */
    public function addTo(GroupRepositoryInterface $group, RepositoryInterface $repository): PodiumResponse;

    /**
     * Removes from the group.
     */
    public function removeFrom(GroupRepositoryInterface $group, RepositoryInterface $repository): PodiumResponse;
}
