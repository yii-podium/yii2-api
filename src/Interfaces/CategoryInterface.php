<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface CategoryInterface
{
    /**
     * Returns category repository.
     */
    public function getRepository(): CategoryRepositoryInterface;

    /**
     * Creates a category as the author.
     */
    public function create(MemberRepositoryInterface $author, array $data = []): PodiumResponse;

    /**
     * Edits the category.
     */
    public function edit(CategoryRepositoryInterface $category, array $data = []): PodiumResponse;

    /**
     * Removes the category.
     */
    public function remove(CategoryRepositoryInterface $category): PodiumResponse;

    /**
     * Replaces the categories order.
     */
    public function replace(
        CategoryRepositoryInterface $firstCategory,
        CategoryRepositoryInterface $secondCategory
    ): PodiumResponse;

    /**
     * Sorts the categories order.
     */
    public function sort(): PodiumResponse;

    /**
     * Archives the category.
     */
    public function archive(CategoryRepositoryInterface $category): PodiumResponse;

    /**
     * Revives the category.
     */
    public function revive(CategoryRepositoryInterface $category): PodiumResponse;
}
