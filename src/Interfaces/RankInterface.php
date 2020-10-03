<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface RankInterface
{
    /**
     * Returns the rank repository.
     */
    public function getRepository(): RankRepositoryInterface;

    /**
     * Creates a rank.
     */
    public function create(array $data = []): PodiumResponse;

    /**
     * Edits the rank.
     */
    public function edit(RankRepositoryInterface $rank, array $data = []): PodiumResponse;

    /**
     * Removes the rank.
     */
    public function remove(RankRepositoryInterface $rank): PodiumResponse;
}
