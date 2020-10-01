<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface RemoverInterface
{
    /**
     * Removes repository storage entry.
     */
    public function remove(RepositoryInterface $repository): PodiumResponse;
}
