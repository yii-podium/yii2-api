<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

interface CheckerInterface
{
    public function check(AllowerInterface $allower): bool;
}
