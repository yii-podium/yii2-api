<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

interface AllowerInterface
{
    public function isAllowed(): bool;
}
