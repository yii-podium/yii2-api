<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\Components\PodiumResponse;

interface LockerInterface
{
    /**
     * Locks the thread.
     */
    public function lock(ThreadRepositoryInterface $thread): PodiumResponse;

    /**
     * Unlock the thread.
     */
    public function unlock(ThreadRepositoryInterface $thread): PodiumResponse;
}
