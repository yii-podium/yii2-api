<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface LoggerInterface
{
    /**
     * Returns the log repository.
     */
    public function getRepository(): LogRepositoryInterface;

    /**
     * Creates a log for action as the member.
     */
    public function create(MemberRepositoryInterface $author, string $action, array $data = []): PodiumResponse;

    /**
     * Removes the log.
     */
    public function remove(LogRepositoryInterface $log): PodiumResponse;
}
