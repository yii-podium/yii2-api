<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\Components\PodiumResponse;

interface MoverInterface
{
    /**
     * Moves repository.
     */
    public function move(RepositoryInterface $repository, RepositoryInterface $parentRepository): PodiumResponse;
}
