<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumDecision;

interface DeciderInterface
{
    /**
     * Sets the permit type.
     */
    public function setType(?string $type): void;

    /**
     * Sets the subject to be checked against.
     */
    public function setSubject(?RepositoryInterface $subject): void;

    /**
     * Sets the member to be checked.
     */
    public function setMember(?MemberRepositoryInterface $member): void;

    /**
     * Returns the decision.
     */
    public function decide(): PodiumDecision;
}
