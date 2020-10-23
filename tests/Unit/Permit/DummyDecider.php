<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Permit;

use Podium\Api\Interfaces\DeciderInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\PodiumDecision;

final class DummyDecider implements DeciderInterface
{
    public function setType(?string $type): void
    {
    }

    public function setSubject(?RepositoryInterface $subject): void
    {
    }

    public function setMember(?MemberRepositoryInterface $member): void
    {
    }

    public function decide(): PodiumDecision
    {
        return PodiumDecision::deny();
    }
}
