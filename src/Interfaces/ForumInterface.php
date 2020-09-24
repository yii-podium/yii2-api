<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\Components\PodiumResponse;

interface ForumInterface
{
    /**
     * Returns the repository.
     */
    public function getRepository(): ForumRepositoryInterface;

    /**
     * Creates forum.
     */
    public function create(
        MemberRepositoryInterface $author,
        CategoryRepositoryInterface $category,
        array $data = []
    ): PodiumResponse;

    /**
     * Updates forum.
     */
    public function edit(ForumRepositoryInterface $forum, array $data = []): PodiumResponse;

    public function remove(ForumRepositoryInterface $forum): PodiumResponse;

    /**
     * Replaces the order of the forums.
     */
    public function replace(
        ForumRepositoryInterface $firstForum,
        ForumRepositoryInterface $secondForum
    ): PodiumResponse;

    public function sort(): PodiumResponse;

    /**
     * Moves forum to different category.
     */
    public function move(ForumRepositoryInterface $forum, CategoryRepositoryInterface $category): PodiumResponse;

    public function archive(ForumRepositoryInterface $forum): PodiumResponse;

    public function revive(ForumRepositoryInterface $forum): PodiumResponse;
}
