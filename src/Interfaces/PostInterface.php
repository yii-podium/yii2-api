<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\Components\PodiumResponse;

/**
 * Interface PostInterface.
 */
interface PostInterface
{
    public function getRepository(): PostRepositoryInterface;

    /**
     * Creates a post.
     */
    public function create(
        MemberRepositoryInterface $author,
        ThreadRepositoryInterface $thread,
        array $data = []
    ): PodiumResponse;

    /**
     * Updates the post.
     */
    public function edit(PostRepositoryInterface $post, array $data = []): PodiumResponse;

    /**
     * Removes the post.
     */
    public function remove(PostRepositoryInterface $post): PodiumResponse;

    /**
     * Moves the post to a different thread.
     */
    public function move(PostRepositoryInterface $post, ThreadRepositoryInterface $thread): PodiumResponse;

    /**
     * Archives the post.
     */
    public function archive(PostRepositoryInterface $post): PodiumResponse;

    /**
     * Revives the post.
     */
    public function revive(PostRepositoryInterface $post): PodiumResponse;

    /**
     * Gives thumb up to the post.
     */
    public function thumbUp(PostRepositoryInterface $post, MemberRepositoryInterface $member): PodiumResponse;

    /**
     * Gives thumb down to the post.
     */
    public function thumbDown(PostRepositoryInterface $post, MemberRepositoryInterface $member): PodiumResponse;

    /**
     * Resets thumb from the post.
     */
    public function thumbReset(PostRepositoryInterface $post, MemberRepositoryInterface $member): PodiumResponse;
}
