<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\Podium;

interface PodiumBridgeInterface
{
    /**
     * Sets Podium module's link.
     */
    public function setPodium(Podium $podium): void;

    /**
     * Returns Podium module.
     */
    public function getPodium(): Podium;
}
