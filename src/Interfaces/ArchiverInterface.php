<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface ArchiverInterface
{
    /**
     * Archives the repository.
     */
    public function archive(RepositoryInterface $repository): PodiumResponse;

    /**
     * Revives the repository.
     */
    public function revive(RepositoryInterface $repository): PodiumResponse;
}
