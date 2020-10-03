<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface ThreadInterface
{
    /**
     * Returns the thread repository.
     */
    public function getThreadRepository(): ThreadRepositoryInterface;

    /**
     * Returns the bookmark repository.
     */
    public function getBookmarkRepository(): BookmarkRepositoryInterface;

    /**
     * Returns the subscription repository.
     */
    public function getSubscriptionRepository(): SubscriptionRepositoryInterface;

    /**
     * Creates a thread as the author under the forum.
     */
    public function create(
        MemberRepositoryInterface $author,
        ForumRepositoryInterface $forum,
        array $data = []
    ): PodiumResponse;

    /**
     * Edits the thread.
     */
    public function edit(ThreadRepositoryInterface $thread, array $data = []): PodiumResponse;

    /**
     * Removes the thread.
     */
    public function remove(ThreadRepositoryInterface $thread): PodiumResponse;

    /**
     * Moves the thread to the forum.
     */
    public function move(ThreadRepositoryInterface $thread, ForumRepositoryInterface $forum): PodiumResponse;

    /**
     * Pins the thread.
     */
    public function pin(ThreadRepositoryInterface $thread): PodiumResponse;

    /**
     * Unpins the thread.
     */
    public function unpin(ThreadRepositoryInterface $thread): PodiumResponse;

    /**
     * Locks the thread.
     */
    public function lock(ThreadRepositoryInterface $thread): PodiumResponse;

    /**
     * Unlocks the thread.
     */
    public function unlock(ThreadRepositoryInterface $thread): PodiumResponse;

    /**
     * Archives the thread.
     */
    public function archive(ThreadRepositoryInterface $thread): PodiumResponse;

    /**
     * Revives the thread.
     */
    public function revive(ThreadRepositoryInterface $thread): PodiumResponse;

    /**
     * Subscribes to the thread as the member.
     */
    public function subscribe(ThreadRepositoryInterface $thread, MemberRepositoryInterface $member): PodiumResponse;

    /**
     * Unsubscribes from the thread as the member.
     */
    public function unsubscribe(ThreadRepositoryInterface $thread, MemberRepositoryInterface $member): PodiumResponse;

    /**
     * Marks the thread at the post's timestamp for the member.
     */
    public function mark(PostRepositoryInterface $post, MemberRepositoryInterface $member): PodiumResponse;
}
