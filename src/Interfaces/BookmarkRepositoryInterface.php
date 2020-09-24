<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

interface BookmarkRepositoryInterface
{
    public function fetchOne(MemberRepositoryInterface $member, ThreadRepositoryInterface $thread): bool;

    public function prepare(MemberRepositoryInterface $member, ThreadRepositoryInterface $thread): void;

    public function getErrors(): array;

    public function getLastSeen(): ?int;

    public function mark(int $timeMark): bool;
}
