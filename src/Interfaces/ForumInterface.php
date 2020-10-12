<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface ForumInterface
{
    /**
     * Returns the forum repository.
     */
    public function getRepository(): ForumRepositoryInterface;

    /**
     * Creates a forum as the author under the category.
     */
    public function create(
        MemberRepositoryInterface $author,
        CategoryRepositoryInterface $category,
        array $data = []
    ): PodiumResponse;

    /**
     * Edits the forum.
     */
    public function edit(ForumRepositoryInterface $forum, array $data = []): PodiumResponse;

    /**
     * Removes the forum.
     */
    public function remove(ForumRepositoryInterface $forum): PodiumResponse;

    /**
     * Replaces the forums order.
     */
    public function replace(
        ForumRepositoryInterface $firstForum,
        ForumRepositoryInterface $secondForum
    ): PodiumResponse;

    /**
     * Sorts the forums order.
     */
    public function sort(): PodiumResponse;

    /**
     * Moves the forum to the category.
     */
    public function move(ForumRepositoryInterface $forum, CategoryRepositoryInterface $category): PodiumResponse;

    /**
     * Archives the forum.
     */
    public function archive(ForumRepositoryInterface $forum): PodiumResponse;

    /**
     * Revives the forum.
     */
    public function revive(ForumRepositoryInterface $forum): PodiumResponse;

    /**
     * Hides the forum.
     */
    public function hide(ForumRepositoryInterface $forum): PodiumResponse;

    /**
     * Reveals the forum.
     */
    public function reveal(ForumRepositoryInterface $forum): PodiumResponse;
}
