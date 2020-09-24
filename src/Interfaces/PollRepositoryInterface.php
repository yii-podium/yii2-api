<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

interface PollRepositoryInterface extends RepositoryInterface
{
    public function create(
        array $data,
        array $answers = []
    ): bool;

    public function edit(array $answers = [], array $data = []): bool;

    public function getAnswerRepository(): PollAnswerRepositoryInterface;

    public function getVoteRepository(): PollVoteRepositoryInterface;

    public function hasMemberVoted(MemberRepositoryInterface $member): bool;

    public function isSingleChoice(): bool;

    public function vote(MemberRepositoryInterface $member, array $answers): bool;
}
