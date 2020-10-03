<?php

declare(strict_types=1);

namespace Podium\Api\Components;

use Podium\Api\Interfaces\ArchiverInterface;
use Podium\Api\Interfaces\BookmarkerInterface;
use Podium\Api\Interfaces\BookmarkRepositoryInterface;
use Podium\Api\Interfaces\CategorisedBuilderInterface;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\LockerInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\MoverInterface;
use Podium\Api\Interfaces\PinnerInterface;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\Interfaces\SubscriberInterface;
use Podium\Api\Interfaces\SubscriptionRepositoryInterface;
use Podium\Api\Interfaces\ThreadInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\PodiumResponse;
use Podium\Api\Services\Thread\ThreadArchiver;
use Podium\Api\Services\Thread\ThreadBookmarker;
use Podium\Api\Services\Thread\ThreadBuilder;
use Podium\Api\Services\Thread\ThreadLocker;
use Podium\Api\Services\Thread\ThreadMover;
use Podium\Api\Services\Thread\ThreadPinner;
use Podium\Api\Services\Thread\ThreadRemover;
use Podium\Api\Services\Thread\ThreadSubscriber;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\di\Instance;

final class Thread extends Component implements ThreadInterface
{
    /**
     * @var string|array|CategorisedBuilderInterface
     */
    public $builderConfig = ThreadBuilder::class;

    /**
     * @var string|array|SubscriberInterface
     */
    public $subscriberConfig = ThreadSubscriber::class;

    /**
     * @var string|array|BookmarkerInterface
     */
    public $bookmarkerConfig = ThreadBookmarker::class;

    /**
     * @var string|array|RemoverInterface
     */
    public $removerConfig = ThreadRemover::class;

    /**
     * @var string|array|ArchiverInterface
     */
    public $archiverConfig = ThreadArchiver::class;

    /**
     * @var string|array|MoverInterface
     */
    public $moverConfig = ThreadMover::class;

    /**
     * @var string|array|LockerInterface
     */
    public $lockerConfig = ThreadLocker::class;

    /**
     * @var string|array|PinnerInterface
     */
    public $pinnerConfig = ThreadPinner::class;

    /**
     * @var string|array|ThreadRepositoryInterface
     */
    public $threadRepositoryConfig;

    /**
     * @var string|array|BookmarkRepositoryInterface
     */
    public $bookmarkRepositoryConfig;

    /**
     * @var string|array|SubscriptionRepositoryInterface
     */
    public $subscriptionRepositoryConfig;

    private ?ThreadRepositoryInterface $repository = null;

    /**
     * @throws InvalidConfigException
     */
    public function getThreadRepository(): ThreadRepositoryInterface
    {
        if (null === $this->repository) {
            /** @var ThreadRepositoryInterface $repository */
            $repository = Instance::ensure($this->threadRepositoryConfig, ThreadRepositoryInterface::class);
            $this->repository = $repository;
        }

        return $this->repository;
    }

    private ?CategorisedBuilderInterface $builder = null;

    /**
     * @throws InvalidConfigException
     */
    public function getBuilder(): CategorisedBuilderInterface
    {
        if (null === $this->builder) {
            /** @var CategorisedBuilderInterface $builder */
            $builder = Instance::ensure($this->builderConfig, CategorisedBuilderInterface::class);
            $this->builder = $builder;
        }

        return $this->builder;
    }

    /**
     * @throws InvalidConfigException
     */
    public function create(
        MemberRepositoryInterface $author,
        ForumRepositoryInterface $forum,
        array $data = []
    ): PodiumResponse {
        return $this->getBuilder()->create($this->getThreadRepository(), $author, $forum, $data);
    }

    /**
     * @throws InvalidConfigException
     */
    public function edit(ThreadRepositoryInterface $thread, array $data = []): PodiumResponse
    {
        return $this->getBuilder()->edit($thread, $data);
    }

    private ?RemoverInterface $remover = null;

    /**
     * @throws InvalidConfigException
     */
    public function getRemover(): RemoverInterface
    {
        if (null === $this->remover) {
            /** @var RemoverInterface $remover */
            $remover = Instance::ensure($this->removerConfig, RemoverInterface::class);
            $this->remover = $remover;
        }

        return $this->remover;
    }

    /**
     * @throws InvalidConfigException
     */
    public function remove(ThreadRepositoryInterface $thread): PodiumResponse
    {
        return $this->getRemover()->remove($thread);
    }

    private ?MoverInterface $mover = null;

    /**
     * @throws InvalidConfigException
     */
    public function getMover(): MoverInterface
    {
        if (null === $this->mover) {
            /** @var MoverInterface $mover */
            $mover = Instance::ensure($this->moverConfig, MoverInterface::class);
            $this->mover = $mover;
        }

        return $this->mover;
    }

    /**
     * @throws InvalidConfigException
     */
    public function move(ThreadRepositoryInterface $thread, ForumRepositoryInterface $forum): PodiumResponse
    {
        return $this->getMover()->move($thread, $forum);
    }

    private ?PinnerInterface $pinner = null;

    /**
     * @throws InvalidConfigException
     */
    public function getPinner(): PinnerInterface
    {
        if (null === $this->pinner) {
            /** @var PinnerInterface $pinner */
            $pinner = Instance::ensure($this->pinnerConfig, PinnerInterface::class);
            $this->pinner = $pinner;
        }

        return $this->pinner;
    }

    /**
     * @throws InvalidConfigException
     */
    public function pin(ThreadRepositoryInterface $thread): PodiumResponse
    {
        return $this->getPinner()->pin($thread);
    }

    /**
     * @throws InvalidConfigException
     */
    public function unpin(ThreadRepositoryInterface $thread): PodiumResponse
    {
        return $this->getPinner()->unpin($thread);
    }

    private ?LockerInterface $locker = null;

    /**
     * @throws InvalidConfigException
     */
    public function getLocker(): LockerInterface
    {
        if (null === $this->locker) {
            /** @var LockerInterface $locker */
            $locker = Instance::ensure($this->lockerConfig, LockerInterface::class);
            $this->locker = $locker;
        }

        return $this->locker;
    }

    /**
     * @throws InvalidConfigException
     */
    public function lock(ThreadRepositoryInterface $thread): PodiumResponse
    {
        return $this->getLocker()->lock($thread);
    }

    /**
     * @throws InvalidConfigException
     */
    public function unlock(ThreadRepositoryInterface $thread): PodiumResponse
    {
        return $this->getLocker()->unlock($thread);
    }

    private ?ArchiverInterface $archiver = null;

    /**
     * @throws InvalidConfigException
     */
    public function getArchiver(): ArchiverInterface
    {
        if (null === $this->archiver) {
            /** @var ArchiverInterface $archiver */
            $archiver = Instance::ensure($this->archiverConfig, ArchiverInterface::class);
            $this->archiver = $archiver;
        }

        return $this->archiver;
    }

    /**
     * @throws InvalidConfigException
     */
    public function archive(ThreadRepositoryInterface $thread): PodiumResponse
    {
        return $this->getArchiver()->archive($thread);
    }

    /**
     * @throws InvalidConfigException
     */
    public function revive(ThreadRepositoryInterface $thread): PodiumResponse
    {
        return $this->getArchiver()->revive($thread);
    }

    private ?SubscriberInterface $subscriber = null;

    /**
     * @throws InvalidConfigException
     */
    public function getSubscriber(): SubscriberInterface
    {
        if (null === $this->subscriber) {
            /** @var SubscriberInterface $subscriber */
            $subscriber = Instance::ensure($this->subscriberConfig, SubscriberInterface::class);
            $this->subscriber = $subscriber;
        }

        return $this->subscriber;
    }

    private ?SubscriptionRepositoryInterface $subscriptionRepository = null;

    /**
     * @throws InvalidConfigException
     */
    public function getSubscriptionRepository(): SubscriptionRepositoryInterface
    {
        if (null === $this->subscriptionRepository) {
            /** @var SubscriptionRepositoryInterface $repository */
            $repository = Instance::ensure($this->subscriptionRepositoryConfig, SubscriptionRepositoryInterface::class);
            $this->subscriptionRepository = $repository;
        }

        return $this->subscriptionRepository;
    }

    /**
     * @throws InvalidConfigException
     */
    public function subscribe(ThreadRepositoryInterface $thread, MemberRepositoryInterface $member): PodiumResponse
    {
        return $this->getSubscriber()->subscribe($this->getSubscriptionRepository(), $thread, $member);
    }

    /**
     * @throws InvalidConfigException
     */
    public function unsubscribe(ThreadRepositoryInterface $thread, MemberRepositoryInterface $member): PodiumResponse
    {
        return $this->getSubscriber()->unsubscribe($this->getSubscriptionRepository(), $thread, $member);
    }

    private ?BookmarkerInterface $bookmarker = null;

    /**
     * @throws InvalidConfigException
     */
    public function getBookmarker(): BookmarkerInterface
    {
        if (null === $this->bookmarker) {
            /** @var BookmarkerInterface $bookmarker */
            $bookmarker = Instance::ensure($this->bookmarkerConfig, BookmarkerInterface::class);
            $this->bookmarker = $bookmarker;
        }

        return $this->bookmarker;
    }

    private ?BookmarkRepositoryInterface $bookmarkRepository = null;

    /**
     * @throws InvalidConfigException
     */
    public function getBookmarkRepository(): BookmarkRepositoryInterface
    {
        if (null === $this->bookmarkRepository) {
            /** @var BookmarkRepositoryInterface $repository */
            $repository = Instance::ensure($this->bookmarkRepositoryConfig, BookmarkRepositoryInterface::class);
            $this->bookmarkRepository = $repository;
        }

        return $this->bookmarkRepository;
    }

    /**
     * @throws InvalidConfigException
     */
    public function mark(PostRepositoryInterface $post, MemberRepositoryInterface $member): PodiumResponse
    {
        return $this->getBookmarker()->mark($this->getBookmarkRepository(), $post, $member);
    }
}
