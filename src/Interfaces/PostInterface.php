<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

/**
 * Interface PostInterface.
 */
interface PostInterface
{
    /**
     * Returns the post repository.
     */
    public function getPostRepository(): PostRepositoryInterface;

    /**
     * Returns the thumb repository.
     */
    public function getThumbRepository(): ThumbRepositoryInterface;

    /**
     * Creates a post as the author under the thread.
     */
    public function create(
        MemberRepositoryInterface $author,
        ThreadRepositoryInterface $thread,
        array $data = []
    ): PodiumResponse;

    /**
     * Edits the post.
     */
    public function edit(PostRepositoryInterface $post, array $data = []): PodiumResponse;

    /**
     * Removes the post.
     */
    public function remove(PostRepositoryInterface $post): PodiumResponse;

    /**
     * Moves the post to the thread.
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
     * Gives a thumb up to the post as the member.
     */
    public function thumbUp(PostRepositoryInterface $post, MemberRepositoryInterface $member): PodiumResponse;

    /**
     * Gives a thumb down to the post as the member.
     */
    public function thumbDown(PostRepositoryInterface $post, MemberRepositoryInterface $member): PodiumResponse;

    /**
     * Resets the thumb for the post as the member.
     */
    public function thumbReset(PostRepositoryInterface $post, MemberRepositoryInterface $member): PodiumResponse;

    /**
     * Pins the post.
     */
    public function pin(PostRepositoryInterface $post): PodiumResponse;

    /**
     * Unpins the post.
     */
    public function unpin(PostRepositoryInterface $post): PodiumResponse;
}
