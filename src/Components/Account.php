<?php

declare(strict_types=1);

namespace Podium\Api\Components;

use DomainException;
use Podium\Api\Interfaces\AccountInterface;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\GroupRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\MessageRepositoryInterface;
use Podium\Api\Interfaces\PollPostRepositoryInterface;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\Module;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\helpers\Json;
use yii\web\User;

use function is_string;

final class Account extends Component implements AccountInterface
{
    /**
     * @var string|array|MemberRepositoryInterface
     */
    public $repositoryConfig;

    /**
     * @var string|array|User
     */
    public $userConfig = 'user';

    private ?Module $podium = null;

    public function setPodium(Module $podium): void
    {
        $this->podium = $podium;
    }

    /**
     * @throws InvalidConfigException
     */
    public function getPodium(): Module
    {
        if (null === $this->podium) {
            throw new InvalidConfigException('Podium module is not set!');
        }

        return $this->podium;
    }

    private ?MemberRepositoryInterface $member = null;

    /**
     * @throws InvalidConfigException
     * @throws NoMembershipException
     */
    public function getMembership(bool $renew = false): MemberRepositoryInterface
    {
        if (null === $this->member || $renew) {
            /** @var User $user */
            $user = Instance::ensure($this->userConfig, User::class);
            $userId = Json::encode($user->getId());
            if (!is_string($userId)) {
                throw new DomainException('Invalid user ID!');
            }

            /** @var MemberRepositoryInterface $member */
            $member = Instance::ensure($this->repositoryConfig, MemberRepositoryInterface::class);
            if (!$member->fetchOne(['user_id' => $userId])) {
                throw new NoMembershipException('No Podium Membership found related to given identity!');
            }
            $this->member = $member;
        }

        return $this->member;
    }

    /**
     * @throws InvalidConfigException
     * @throws NoMembershipException
     */
    public function joinGroup(GroupRepositoryInterface $group): PodiumResponse
    {
        return $this->getPodium()->group->join($group, $this->getMembership());
    }

    /**
     * @throws InvalidConfigException
     * @throws NoMembershipException
     */
    public function leaveGroup(GroupRepositoryInterface $group): PodiumResponse
    {
        return $this->getPodium()->group->leave($group, $this->getMembership());
    }

    /**
     * @throws InvalidConfigException
     * @throws NoMembershipException
     */
    public function createCategory(array $data = []): PodiumResponse
    {
        return $this->getPodium()->category->create($this->getMembership(), $data);
    }

    /**
     * @throws InvalidConfigException
     * @throws NoMembershipException
     */
    public function createForum(CategoryRepositoryInterface $category, array $data = []): PodiumResponse
    {
        return $this->getPodium()->forum->create($this->getMembership(), $category, $data);
    }

    /**
     * @throws InvalidConfigException
     * @throws NoMembershipException
     */
    public function createThread(ForumRepositoryInterface $forum, array $data = []): PodiumResponse
    {
        return $this->getPodium()->thread->create($this->getMembership(), $forum, $data);
    }

    /**
     * @throws InvalidConfigException
     * @throws NoMembershipException
     */
    public function createPost(ThreadRepositoryInterface $thread, array $data = []): PodiumResponse
    {
        return $this->getPodium()->post->create($this->getMembership(), $thread, $data);
    }

    /**
     * @throws InvalidConfigException
     * @throws NoMembershipException
     */
    public function markPost(PostRepositoryInterface $post): PodiumResponse
    {
        return $this->getPodium()->thread->mark($post, $this->getMembership());
    }

    /**
     * @throws InvalidConfigException
     * @throws NoMembershipException
     */
    public function subscribeThread(ThreadRepositoryInterface $thread): PodiumResponse
    {
        return $this->getPodium()->thread->subscribe($thread, $this->getMembership());
    }

    /**
     * @throws InvalidConfigException
     * @throws NoMembershipException
     */
    public function unsubscribeThread(ThreadRepositoryInterface $thread): PodiumResponse
    {
        return $this->getPodium()->thread->unsubscribe($thread, $this->getMembership());
    }

    /**
     * @throws InvalidConfigException
     * @throws NoMembershipException
     */
    public function thumbUpPost(PostRepositoryInterface $post): PodiumResponse
    {
        return $this->getPodium()->post->thumbUp($post, $this->getMembership());
    }

    /**
     * @throws InvalidConfigException
     * @throws NoMembershipException
     */
    public function thumbDownPost(PostRepositoryInterface $post): PodiumResponse
    {
        return $this->getPodium()->post->thumbDown($post, $this->getMembership());
    }

    /**
     * @throws InvalidConfigException
     * @throws NoMembershipException
     */
    public function thumbResetPost(PostRepositoryInterface $post): PodiumResponse
    {
        return $this->getPodium()->post->thumbReset($post, $this->getMembership());
    }

    /**
     * @throws InvalidConfigException
     * @throws NoMembershipException
     */
    public function votePoll(PollPostRepositoryInterface $post, array $answer): PodiumResponse
    {
        return $this->getPodium()->post->votePoll($post, $this->getMembership(), $answer);
    }

    /**
     * @throws InvalidConfigException
     * @throws NoMembershipException
     */
    public function edit(array $data = []): PodiumResponse
    {
        return $this->getPodium()->member->edit($this->getMembership(), $data);
    }

    /**
     * @throws InvalidConfigException
     * @throws NoMembershipException
     */
    public function befriendMember(MemberRepositoryInterface $target): PodiumResponse
    {
        return $this->getPodium()->member->befriend($this->getMembership(), $target);
    }

    /**
     * @throws InvalidConfigException
     * @throws NoMembershipException
     */
    public function unfriendMember(MemberRepositoryInterface $target): PodiumResponse
    {
        return $this->getPodium()->member->unfriend($this->getMembership(), $target);
    }

    /**
     * @throws InvalidConfigException
     * @throws NoMembershipException
     */
    public function ignoreMember(MemberRepositoryInterface $target): PodiumResponse
    {
        return $this->getPodium()->member->ignore($this->getMembership(), $target);
    }

    /**
     * @throws InvalidConfigException
     * @throws NoMembershipException
     */
    public function sendMessage(
        MemberRepositoryInterface $receiver,
        MessageRepositoryInterface $replyTo = null,
        array $data = []
    ): PodiumResponse {
        return $this->getPodium()->message->send($this->getMembership(), $receiver, $replyTo, $data);
    }

    /**
     * @throws InvalidConfigException
     * @throws NoMembershipException
     */
    public function removeMessage(MessageRepositoryInterface $message): PodiumResponse
    {
        return $this->getPodium()->message->remove($message, $this->getMembership());
    }

    /**
     * @throws InvalidConfigException
     * @throws NoMembershipException
     */
    public function archiveMessage(MessageRepositoryInterface $message): PodiumResponse
    {
        return $this->getPodium()->message->archive($message, $this->getMembership());
    }

    /**
     * @throws InvalidConfigException
     * @throws NoMembershipException
     */
    public function reviveMessage(MessageRepositoryInterface $message): PodiumResponse
    {
        return $this->getPodium()->message->revive($message, $this->getMembership());
    }
}
