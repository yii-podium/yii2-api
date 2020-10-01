<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface CategoryBuilderInterface
{
    public function create(
        CategoryRepositoryInterface $category,
        MemberRepositoryInterface $author,
        array $data = []
    ): PodiumResponse;

    public function edit(CategoryRepositoryInterface $category, array $data = []): PodiumResponse;
}
