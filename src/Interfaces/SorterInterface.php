<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface SorterInterface
{
    /**
     * Replaces the order of two repositories.
     */
    public function replace(
        RepositoryInterface $firstRepository,
        RepositoryInterface $secondRepository
    ): PodiumResponse;

    /**
     * Sorts repositories.
     */
    public function sort(RepositoryInterface $repository): PodiumResponse;
}
