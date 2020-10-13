<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface HiderInterface
{
    /**
     * Hides the repository.
     */
    public function hide(RepositoryInterface $repository): PodiumResponse;

    /**
     * Reveals the repository.
     */
    public function reveal(RepositoryInterface $repository): PodiumResponse;
}
