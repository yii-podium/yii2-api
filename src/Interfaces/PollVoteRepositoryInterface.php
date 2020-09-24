<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

interface PollVoteRepositoryInterface
{
    public function hasMemberVoted(MemberRepositoryInterface $member): bool;

    public function getErrors(): array;

    public function register(MemberRepositoryInterface $member, PollAnswerRepositoryInterface $answer): bool;
}
