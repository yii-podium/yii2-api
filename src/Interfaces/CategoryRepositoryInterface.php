<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

interface CategoryRepositoryInterface extends RepositoryInterface
{
    public function create(MemberRepositoryInterface $author, array $data = []): bool;

    public function isArchived(): bool;

    public function archive(): bool;

    public function revive(): bool;

    public function setOrder(int $order): bool;

    public function getOrder(): int;

    public function sort(): bool;

    public function isHidden(): bool;

    public function hide(): bool;

    public function reveal(): bool;
}
