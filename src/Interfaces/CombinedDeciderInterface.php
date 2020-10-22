<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

interface CombinedDeciderInterface extends DeciderInterface
{
    public function setDeciders(array $deciders): void;
}
