<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

interface MemberRepositoryInterface extends RepositoryInterface
{
    public function ban(): bool;

    public function unban(): bool;

    /**
     * @param int|string|array $id
     */
    public function register($id, array $data = []): bool;

    public function isBanned(): bool;
}
