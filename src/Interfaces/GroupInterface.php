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
     * Adds the repository to the group.
     */
    public function join(GroupRepositoryInterface $group, RepositoryInterface $repository): PodiumResponse;

    /**
     * Removes the repository from the group.
     */
    public function leave(GroupRepositoryInterface $group, RepositoryInterface $repository): PodiumResponse;
}
