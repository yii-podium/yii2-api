<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\Components\PodiumResponse;

interface PollPostInterface
{
    /**
     * Adds a poll to the post.
     */
    public function addPoll(PollPostRepositoryInterface $post, array $answers, array $data = []): PodiumResponse;

    /**
     * Updates the post's poll.
     */
    public function editPoll(PollPostRepositoryInterface $post, array $answers = [], array $data = []): PodiumResponse;

    /**
     * Removes the post's poll.
     */
    public function removePoll(PollPostRepositoryInterface $post): PodiumResponse;

    /**
     * Votes in the post's poll.
     */
    public function votePoll(
        PollPostRepositoryInterface $post,
        MemberRepositoryInterface $member,
        array $answers
    ): PodiumResponse;
}
