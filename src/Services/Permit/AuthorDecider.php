<?php

declare(strict_types=1);

namespace Podium\Api\Services\Permit;

use Podium\Api\Enums\PermitType;
use Podium\Api\Interfaces\DeciderInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\PodiumDecision;

use function in_array;

final class AuthorDecider implements DeciderInterface
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
        if (!in_array($this->type, [PermitType::UPDATE, PermitType::DELETE], true)) {
            return PodiumDecision::abstain();
        }

        if (
            null !== $this->subject
            && null !== $this->member
            && $this->member->getId() === $this->subject->getAuthor()->getId()
        ) {
            return PodiumDecision::allow();
        }

        return PodiumDecision::deny();
    }
}
