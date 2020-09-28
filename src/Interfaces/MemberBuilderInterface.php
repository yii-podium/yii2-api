<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\Components\PodiumResponse;

interface MemberBuilderInterface
{
    /**
     * Registers new Podium account.
     *
     * @param int|string|array $id
     */
    public function register(MemberRepositoryInterface $member, $id, array $data = []): PodiumResponse;

    public function edit(MemberRepositoryInterface $member, array $data = []): PodiumResponse;
}
