<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\Components\PodiumResponse;

interface LikerInterface
{
    /**
     * Gives thumb up.
     */
    public function thumbUp(
        ThumbRepositoryInterface $thumb,
        PostRepositoryInterface $post,
        MemberRepositoryInterface $member
    ): PodiumResponse;

    /**
     * Gives thumb down.
     */
    public function thumbDown(
        ThumbRepositoryInterface $thumb,
        PostRepositoryInterface $post,
        MemberRepositoryInterface $member
    ): PodiumResponse;

    /**
     * Resets thumb.
     */
    public function thumbReset(
        ThumbRepositoryInterface $thumb,
        PostRepositoryInterface $post,
        MemberRepositoryInterface $member
    ): PodiumResponse;
}
