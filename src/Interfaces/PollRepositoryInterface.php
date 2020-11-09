<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

interface PollRepositoryInterface extends RepositoryInterface
{
    public function create(
        array $data,
        array $answers = []
    ): bool;

    public function edit(array $answers = [], array $data = []): bool;
}
