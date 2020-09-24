<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\Components\PodiumResponse;

interface CategorisedBuilderInterface
{
    public function create(
        RepositoryInterface $forum,
        MemberRepositoryInterface $author,
        RepositoryInterface $category,
        array $data = []
    ): PodiumResponse;

    public function edit(RepositoryInterface $repository, array $data = []): PodiumResponse;
}
