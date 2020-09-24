<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\Components\PodiumResponse;

interface BuilderInterface
{
    public function create(RepositoryInterface $repository, array $data = []): PodiumResponse;

    public function edit(RepositoryInterface $repository, array $data = []): PodiumResponse;
}
