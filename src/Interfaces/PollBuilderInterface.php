<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface PollBuilderInterface
{
    public function create(PollPostRepositoryInterface $post, array $answers, array $data = []): PodiumResponse;

    public function edit(PollPostRepositoryInterface $post, array $answers = [], array $data = []): PodiumResponse;
}
