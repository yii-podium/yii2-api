<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

interface PollPostRepositoryInterface extends PostRepositoryInterface
{
    public function getPoll(): PollRepositoryInterface;
}
