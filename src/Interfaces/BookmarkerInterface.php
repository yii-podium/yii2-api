<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface BookmarkerInterface
{
    /**
     * Marks thread.
     */
    public function mark(
        BookmarkRepositoryInterface $bookmark,
        PostRepositoryInterface $post,
        MemberRepositoryInterface $member
    ): PodiumResponse;
}
