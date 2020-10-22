<?php

declare(strict_types=1);

namespace Podium\Api\Services\Permit;

use Podium\Api\Interfaces\DeciderInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\PodiumDecision;

final class GroupDecider implements DeciderInterface
{
    private ?string $type = null;

    private ?RepositoryInterface $subject = null;

    private ?MemberRepositoryInterface $member = null;

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function setSubject(?RepositoryInterface $subject): void
    {
        $this->subject = $subject;
    }

    public function setMember(?MemberRepositoryInterface $member): void
    {
        $this->member = $member;
    }

    public function decide(): PodiumDecision
    {
        if (null === $this->subject) {
            return PodiumDecision::deny();
        }

        $subjectGroups = $this->subject->getAllowedGroups();
        if ([] === $subjectGroups) {
            return PodiumDecision::abstain();
        }

        if (null === $this->member) {
            return PodiumDecision::deny();
        }

        if ($this->member->isGroupMember($subjectGroups)) {
            return PodiumDecision::allow();
        }

        return PodiumDecision::deny();
    }
}
