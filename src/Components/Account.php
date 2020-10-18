<?php

declare(strict_types=1);

namespace Podium\Api\Components;

use DomainException;
use Podium\Api\Interfaces\AccountInterface;
use Podium\Api\Interfaces\CategoryInterface;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\ForumInterface;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\GroupInterface;
use Podium\Api\Interfaces\GroupRepositoryInterface;
use Podium\Api\Interfaces\LogInterface;
use Podium\Api\Interfaces\MemberInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\MessageInterface;
use Podium\Api\Interfaces\MessageRepositoryInterface;
use Podium\Api\Interfaces\PodiumBridgeInterface;
use Podium\Api\Interfaces\PollPostInterface;
use Podium\Api\Interfaces\PollPostRepositoryInterface;
use Podium\Api\Interfaces\PostInterface;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\ThreadInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\Podium;
use Podium\Api\PodiumResponse;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\helpers\Json;
use yii\web\User;

final class Account extends Component implements AccountInterface, PodiumBridgeInterface
{
    /**
     * @var string|array|MemberRepositoryInterface
     */
    public $repositoryConfig;

    /**
     * @var string|array|User
     */
    public $userConfig = 'user';

    private ?Podium $podium = null;

    public function setPodium(Podium $podium): void
    {
        $this->podium = $podium;
    }

    /**
     * @throws InvalidConfigException
     */
    public function getPodium(): Podium
    {
        if (null === $this->podium) {
            throw new InvalidConfigException('Podium module is not set!');
        }

        return $this->podium;
    }

    private ?MemberRepositoryInterface $member = null;

    /**
     * Returns member's repository loaded with current user's data.
     *
     * @throws InvalidConfigException|NoMembershipException
     */
    public function getMembership(bool $renew = false): MemberRepositoryInterface
    {
        if (null === $this->member || $renew) {
            /** @var User $user */
            $user = Instance::ensure($this->userConfig, User::class);
            /** @var int|string|null $userId */
            $userId = $user->getId();
            if (null === $userId) {
                throw new DomainException('Invalid user ID!');
            }

            /** @var MemberRepositoryInterface $member */
            $member = Instance::ensure($this->repositoryConfig, MemberRepositoryInterface::class);
            if (!$member->fetchOne(['user_id' => Json::encode($userId)])) {
                throw new NoMembershipException('No Podium Membership found related to given identity!');
            }
            $this->member = $member;
        }

        return $this->member;
    }

    /**
     * @throws InvalidConfigException|NoMembershipException
     */
    public function joinGroup(GroupRepositoryInterface $group): PodiumResponse
    {
        /** @var GroupInterface $groupComponent */
        $groupComponent = $this->getPodium()->getGroup();

        return $groupComponent->join($group, $this->getMembership());
    }

    /**
     * @throws InvalidConfigException|NoMembershipException
     */
    public function leaveGroup(GroupRepositoryInterface $group): PodiumResponse
    {
        /** @var GroupInterface $groupComponent */
        $groupComponent = $this->getPodium()->getGroup();

        return $groupComponent->leave($group, $this->getMembership());
    }

    /**
     * @throws InvalidConfigException|NoMembershipException
     */
    public function createCategory(array $data = []): PodiumResponse
    {
        /** @var CategoryInterface $categoryComponent */
        $categoryComponent = $this->getPodium()->getCategory();

        return $categoryComponent->create($this->getMembership(), $data);
    }

    /**
     * @throws InvalidConfigException|NoMembershipException
     */
    public function createForum(CategoryRepositoryInterface $parentCategory, array $data = []): PodiumResponse
    {
        /** @var ForumInterface $forumComponent */
        $forumComponent = $this->getPodium()->getForum();

        return $forumComponent->create($this->getMembership(), $parentCategory, $data);
    }

    /**
     * @throws InvalidConfigException|NoMembershipException
     */
    public function createThread(ForumRepositoryInterface $parentForum, array $data = []): PodiumResponse
    {
        /** @var ThreadInterface $threadComponent */
        $threadComponent = $this->getPodium()->getThread();

        return $threadComponent->create($this->getMembership(), $parentForum, $data);
    }

    /**
     * @throws InvalidConfigException|NoMembershipException
     */
    public function createPost(ThreadRepositoryInterface $parentThread, array $data = []): PodiumResponse
    {
        /** @var PostInterface $postComponent */
        $postComponent = $this->getPodium()->getPost();

        return $postComponent->create($this->getMembership(), $parentThread, $data);
    }

    /**
     * @throws InvalidConfigException|NoMembershipException
     */
    public function markThread(PostRepositoryInterface $post): PodiumResponse
    {
        /** @var ThreadInterface $threadComponent */
        $threadComponent = $this->getPodium()->getThread();

        return $threadComponent->mark($post, $this->getMembership());
    }

    /**
     * @throws InvalidConfigException|NoMembershipException
     */
    public function subscribeThread(ThreadRepositoryInterface $thread): PodiumResponse
    {
        /** @var ThreadInterface $threadComponent */
        $threadComponent = $this->getPodium()->getThread();

        return $threadComponent->subscribe($thread, $this->getMembership());
    }

    /**
     * @throws InvalidConfigException|NoMembershipException
     */
    public function unsubscribeThread(ThreadRepositoryInterface $thread): PodiumResponse
    {
        /** @var ThreadInterface $threadComponent */
        $threadComponent = $this->getPodium()->getThread();

        return $threadComponent->unsubscribe($thread, $this->getMembership());
    }

    /**
     * @throws InvalidConfigException|NoMembershipException
     */
    public function thumbUpPost(PostRepositoryInterface $post): PodiumResponse
    {
        /** @var PostInterface $postComponent */
        $postComponent = $this->getPodium()->getPost();

        return $postComponent->thumbUp($post, $this->getMembership());
    }

    /**
     * @throws InvalidConfigException|NoMembershipException
     */
    public function thumbDownPost(PostRepositoryInterface $post): PodiumResponse
    {
        /** @var PostInterface $postComponent */
        $postComponent = $this->getPodium()->getPost();

        return $postComponent->thumbDown($post, $this->getMembership());
    }

    /**
     * @throws InvalidConfigException|NoMembershipException
     */
    public function thumbResetPost(PostRepositoryInterface $post): PodiumResponse
    {
        /** @var PostInterface $postComponent */
        $postComponent = $this->getPodium()->getPost();

        return $postComponent->thumbReset($post, $this->getMembership());
    }

    /**
     * @throws InvalidConfigException|NoMembershipException
     */
    public function votePoll(PollPostRepositoryInterface $post, array $answer): PodiumResponse
    {
        /** @var PollPostInterface $postComponent */
        $postComponent = $this->getPodium()->getPost();

        return $postComponent->votePoll($post, $this->getMembership(), $answer);
    }

    /**
     * @throws InvalidConfigException|NoMembershipException
     */
    public function edit(array $data = []): PodiumResponse
    {
        /** @var MemberInterface $memberComponent */
        $memberComponent = $this->getPodium()->getMember();

        return $memberComponent->edit($this->getMembership(), $data);
    }

    /**
     * @throws InvalidConfigException|NoMembershipException
     */
    public function befriendMember(MemberRepositoryInterface $target): PodiumResponse
    {
        /** @var MemberInterface $memberComponent */
        $memberComponent = $this->getPodium()->getMember();

        return $memberComponent->befriend($this->getMembership(), $target);
    }

    /**
     * @throws InvalidConfigException|NoMembershipException
     */
    public function ignoreMember(MemberRepositoryInterface $target): PodiumResponse
    {
        /** @var MemberInterface $memberComponent */
        $memberComponent = $this->getPodium()->getMember();

        return $memberComponent->ignore($this->getMembership(), $target);
    }

    /**
     * @throws InvalidConfigException|NoMembershipException
     */
    public function disconnectMember(MemberRepositoryInterface $target): PodiumResponse
    {
        /** @var MemberInterface $memberComponent */
        $memberComponent = $this->getPodium()->getMember();

        return $memberComponent->disconnect($this->getMembership(), $target);
    }

    /**
     * @throws InvalidConfigException|NoMembershipException
     */
    public function sendMessage(
        MemberRepositoryInterface $receiver,
        MessageRepositoryInterface $replyTo = null,
        array $data = []
    ): PodiumResponse {
        /** @var MessageInterface $messageComponent */
        $messageComponent = $this->getPodium()->getMessage();

        return $messageComponent->send($this->getMembership(), $receiver, $replyTo, $data);
    }

    /**
     * @throws InvalidConfigException|NoMembershipException
     */
    public function removeMessage(MessageRepositoryInterface $message): PodiumResponse
    {
        /** @var MessageInterface $messageComponent */
        $messageComponent = $this->getPodium()->getMessage();

        return $messageComponent->remove($message, $this->getMembership());
    }

    /**
     * @throws InvalidConfigException|NoMembershipException
     */
    public function archiveMessage(MessageRepositoryInterface $message): PodiumResponse
    {
        /** @var MessageInterface $messageComponent */
        $messageComponent = $this->getPodium()->getMessage();

        return $messageComponent->archive($message, $this->getMembership());
    }

    /**
     * @throws InvalidConfigException|NoMembershipException
     */
    public function reviveMessage(MessageRepositoryInterface $message): PodiumResponse
    {
        /** @var MessageInterface $messageComponent */
        $messageComponent = $this->getPodium()->getMessage();

        return $messageComponent->revive($message, $this->getMembership());
    }

    /**
     * @throws InvalidConfigException|NoMembershipException
     */
    public function log(string $action, array $data = []): PodiumResponse
    {
        /** @var LogInterface $logComponent */
        $logComponent = $this->getPodium()->getLog();

        return $logComponent->create($this->getMembership(), $action, $data);
    }
}
