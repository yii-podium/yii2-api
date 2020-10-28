<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface KeeperInterface
{
    /**
     * Joins group.
     */
    public function join(GroupRepositoryInterface $group, RepositoryInterface $repository): PodiumResponse;

    /**
     * Leaves group.
     */
    public function leave(GroupRepositoryInterface $group, RepositoryInterface $repository): PodiumResponse;
}
