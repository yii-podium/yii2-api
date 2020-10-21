<?php

declare(strict_types=1);

namespace Podium\Api\Enums;

final class VoterDecision
{
    public const ALLOW = 1;
    public const DENY = -1;
    public const ABSTAIN = 0;
}
