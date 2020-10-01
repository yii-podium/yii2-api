<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface RankInterface
{
    public function getRepository(): RankRepositoryInterface;

    /**
     * Creates rank.
     */
    public function create(array $data = []): PodiumResponse;

    /**
     * Updates rank.
     */
    public function edit(RankRepositoryInterface $rank, array $data = []): PodiumResponse;

    public function remove(RankRepositoryInterface $rank): PodiumResponse;
}
