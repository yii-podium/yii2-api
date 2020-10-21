<?php

declare(strict_types=1);

namespace Podium\Api;

use Podium\Api\Enums\VoterDecision;

final class PodiumDecision
{
    private int $decision;

    private function __construct(int $decision)
    {
        $this->decision = $decision;
    }

    /**
     * Returns decision to allow.
     */
    public static function allow(): PodiumDecision
    {
        return new self(VoterDecision::ALLOW);
    }

    /**
     * Returns decision to allow.
     */
    public static function deny(): PodiumDecision
    {
        return new self(VoterDecision::DENY);
    }

    /**
     * Returns decision to abstain.
     */
    public static function abstain(): PodiumDecision
    {
        return new self(VoterDecision::ABSTAIN);
    }

    public function getDecision(): int
    {
        return $this->decision;
    }
}
