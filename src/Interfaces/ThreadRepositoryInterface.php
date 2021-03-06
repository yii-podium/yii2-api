<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

interface ThreadRepositoryInterface extends RepositoryInterface
{
    public function create(MemberRepositoryInterface $author, ForumRepositoryInterface $forum, array $data = []): bool;

    public function isArchived(): bool;

    public function pin(): bool;

    public function unpin(): bool;

    public function move(ForumRepositoryInterface $forum): bool;

    public function lock(): bool;

    public function isLocked(): bool;

    public function unlock(): bool;

    public function archive(): bool;

    public function revive(): bool;

    public function getPostsCount(): int;

    public function updateCounters(int $posts): bool;

    public function isHidden(): bool;

    public function hide(): bool;

    public function reveal(): bool;
}
