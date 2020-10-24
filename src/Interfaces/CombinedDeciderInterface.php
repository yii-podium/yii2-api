<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

interface CombinedDeciderInterface extends DeciderInterface
{
    /**
     * Sets deciders to combine their decisions.
     */
    public function setDeciders(array $deciders): void;
}
