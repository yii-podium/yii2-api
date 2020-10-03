<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface PollPostInterface
{
    /**
     * Adds a poll with the poll answers to the post.
     */
    public function addPoll(PollPostRepositoryInterface $post, array $answers, array $data = []): PodiumResponse;

    /**
     * Edits the post's poll with answers.
     */
    public function editPoll(PollPostRepositoryInterface $post, array $answers = [], array $data = []): PodiumResponse;

    /**
     * Removes the post's poll.
     */
    public function removePoll(PollPostRepositoryInterface $post): PodiumResponse;

    /**
     * Votes in the post's poll with answers as the member.
     */
    public function votePoll(
        PollPostRepositoryInterface $post,
        MemberRepositoryInterface $member,
        array $answers
    ): PodiumResponse;
}
