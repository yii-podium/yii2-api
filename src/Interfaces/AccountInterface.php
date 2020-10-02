<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface AccountInterface
{
    /**
     * Returns the member repository.
     */
    public function getMembership(bool $renew = false): MemberRepositoryInterface;

    /**
     * Adds the current user to the group.
     */
    public function joinGroup(GroupRepositoryInterface $group): PodiumResponse;

    /**
     * Removes the current user from the group.
     */
    public function leaveGroup(GroupRepositoryInterface $group): PodiumResponse;

    /**
     * Creates a category as the current user.
     */
    public function createCategory(array $data = []): PodiumResponse;

    /**
     * Creates a forum under the parent category as the current user.
     */
    public function createForum(CategoryRepositoryInterface $parentCategory, array $data = []): PodiumResponse;

    /**
     * Creates a thread under the parent forum as the current user.
     */
    public function createThread(ForumRepositoryInterface $parentForum, array $data = []): PodiumResponse;

    /**
     * Creates a post under the parent thread as the current user.
     */
    public function createPost(ThreadRepositoryInterface $parentThread, array $data = []): PodiumResponse;

    /**
     * Marks the thread for the current user at the post's timestamp.
     */
    public function markThread(PostRepositoryInterface $post): PodiumResponse;

    /**
     * Subscribes the current user to the thread.
     */
    public function subscribeThread(ThreadRepositoryInterface $thread): PodiumResponse;

    /**
     * Unsubscribes the current user from the thread.
     */
    public function unsubscribeThread(ThreadRepositoryInterface $thread): PodiumResponse;

    /**
     * Gives the post a thumb up from the current user.
     */
    public function thumbUpPost(PostRepositoryInterface $post): PodiumResponse;

    /**
     * Gives the post a thumb down from the current user.
     */
    public function thumbDownPost(PostRepositoryInterface $post): PodiumResponse;

    /**
     * Resets the thumb setting of the current user in the post.
     */
    public function thumbResetPost(PostRepositoryInterface $post): PodiumResponse;

    /**
     * Votes in the post's poll as the current user.
     */
    public function votePoll(PollPostRepositoryInterface $post, array $answer): PodiumResponse;

    /**
     * Edits the current user data.
     */
    public function edit(array $data = []): PodiumResponse;

    /**
     * Befriends the target member as the current user.
     */
    public function befriendMember(MemberRepositoryInterface $target): PodiumResponse;

    /**
     * Unfriends the target member as the current user.
     */
    public function unfriendMember(MemberRepositoryInterface $target): PodiumResponse;

    /**
     * Ignores the target member as the current user.
     */
    public function ignoreMember(MemberRepositoryInterface $target): PodiumResponse;

    /**
     * Unignores the target member as the current user.
     */
    public function unignoreMember(MemberRepositoryInterface $target): PodiumResponse;

    /**
     * Sends a message to the receiver as the current user.
     */
    public function sendMessage(
        MemberRepositoryInterface $receiver,
        MessageRepositoryInterface $replyTo = null,
        array $data = []
    ): PodiumResponse;

    /**
     * Removes the current user's side of the message.
     */
    public function removeMessage(MessageRepositoryInterface $message): PodiumResponse;

    /**
     * Archives the current user's side of the message.
     */
    public function archiveMessage(MessageRepositoryInterface $message): PodiumResponse;

    /**
     * Revives the current user's side of the message.
     */
    public function reviveMessage(MessageRepositoryInterface $message): PodiumResponse;

    /**
     * Logs the action as the current user.
     */
    public function log(string $action, array $data = []): PodiumResponse;
}
