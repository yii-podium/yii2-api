<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

interface RoleRepositoryInterface extends RepositoryInterface
{
    public function create(array $data): bool;
}
