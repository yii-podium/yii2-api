<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

interface LogRepositoryInterface extends RepositoryInterface
{
    public function create(MemberRepositoryInterface $author, string $action, array $data = []): bool;
}
