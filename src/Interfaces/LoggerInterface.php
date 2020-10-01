<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface LoggerInterface
{
    public function create(MemberRepositoryInterface $author, string $action, array $data = []): PodiumResponse;

    public function remove(LogRepositoryInterface $log): PodiumResponse;
}
