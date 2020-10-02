<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\Module;

interface PodiumBridgeInterface
{
    /**
     * Sets Podium module's link.
     */
    public function setPodium(Module $podium): void;

    /**
     * Returns Podium module.
     */
    public function getPodium(): Module;
}
