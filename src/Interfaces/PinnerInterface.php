<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\Components\PodiumResponse;

interface PinnerInterface
{
    /**
     * Pins model.
     */
    public function pin(RepositoryInterface $thread): PodiumResponse;

    /**
     * Unpins model.
     */
    public function unpin(RepositoryInterface $thread): PodiumResponse;
}
