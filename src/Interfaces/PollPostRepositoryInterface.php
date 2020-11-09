<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

interface PollPostRepositoryInterface extends PostRepositoryInterface
{
    public function removePoll(): bool;

    public function addPoll(array $answers, array $data = []): bool;

    public function editPoll(array $answers, array $data = []): bool;

    public function hasMemberPollVoted(MemberRepositoryInterface $member): bool;

    public function isPollSingleChoice(): bool;

    public function arePollAnswersAcceptable(array $answers): bool;

    public function votePoll(MemberRepositoryInterface $member, array $answers): bool;
}
