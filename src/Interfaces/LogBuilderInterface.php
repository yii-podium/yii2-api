<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\Components\PodiumResponse;

interface LogBuilderInterface
{
    public function create(
        LogRepositoryInterface $repository,
        MemberRepositoryInterface $author,
        string $action,
        array $data = []
    ): PodiumResponse;
}
