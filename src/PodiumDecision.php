<?php

declare(strict_types=1);

namespace Podium\Api;

use Podium\Api\Enums\Decision;

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
        return new self(Decision::ALLOW);
    }

    /**
     * Returns decision to allow.
     */
    public static function deny(): PodiumDecision
    {
        return new self(Decision::DENY);
    }

    /**
     * Returns decision to abstain.
     */
    public static function abstain(): PodiumDecision
    {
        return new self(Decision::ABSTAIN);
    }

    public function getDecision(): int
    {
        return $this->decision;
    }
}
