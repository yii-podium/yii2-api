<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumDecision;

interface DeciderInterface
{
    public function setType(string $type): void;

    public function setSubject(?RepositoryInterface $subject): void;

    public function setMember(?MemberRepositoryInterface $member): void;

    public function decide(): PodiumDecision;
}
