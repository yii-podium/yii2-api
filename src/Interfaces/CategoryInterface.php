<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\Components\PodiumResponse;

interface CategoryInterface
{
    /**
     * Returns category repository.
     */
    public function getRepository(): CategoryRepositoryInterface;

    /**
     * Creates category.
     */
    public function create(MemberRepositoryInterface $author, array $data = []): PodiumResponse;

    /**
     * Updates category.
     */
    public function edit(CategoryRepositoryInterface $category, array $data = []): PodiumResponse;

    public function remove(CategoryRepositoryInterface $category): PodiumResponse;

    /**
     * Replaces the order of the categories.
     */
    public function replace(
        CategoryRepositoryInterface $firstCategory,
        CategoryRepositoryInterface $secondCategory
    ): PodiumResponse;

    public function sort(): PodiumResponse;

    public function archive(CategoryRepositoryInterface $category): PodiumResponse;

    public function revive(CategoryRepositoryInterface $category): PodiumResponse;
}
