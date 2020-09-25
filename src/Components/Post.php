<?php

declare(strict_types=1);

namespace Podium\Api\Components;

use Podium\Api\Interfaces\ArchiverInterface;
use Podium\Api\Interfaces\CategorisedBuilderInterface;
use Podium\Api\Interfaces\LikerInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\MoverInterface;
use Podium\Api\Interfaces\PinnerInterface;
use Podium\Api\Interfaces\PollBuilderInterface;
use Podium\Api\Interfaces\PollPostInterface;
use Podium\Api\Interfaces\PollPostRepositoryInterface;
use Podium\Api\Interfaces\PostInterface;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\Interfaces\ThumbRepositoryInterface;
use Podium\Api\Interfaces\VoterInterface;
use Podium\Api\Services\Poll\PollBuilder;
use Podium\Api\Services\Poll\PollRemover;
use Podium\Api\Services\Poll\PollVoter;
use Podium\Api\Services\Post\PostArchiver;
use Podium\Api\Services\Post\PostBuilder;
use Podium\Api\Services\Post\PostLiker;
use Podium\Api\Services\Post\PostMover;
use Podium\Api\Services\Post\PostPinner;
use Podium\Api\Services\Post\PostRemover;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\di\Instance;

final class Post extends Component implements PostInterface, PollPostInterface
{
    /**
     * @var string|array|CategorisedBuilderInterface
     */
    public $builderConfig = PostBuilder::class;

    /**
     * @var string|array|LikerInterface
     */
    public $likerConfig = PostLiker::class;

    /**
     * @var string|array|RemoverInterface
     */
    public $removerConfig = PostRemover::class;

    /**
     * @var string|array|ArchiverInterface
     */
    public $archiverConfig = PostArchiver::class;

    /**
     * @var string|array|MoverInterface
     */
    public $moverConfig = PostMover::class;

    /**
     * @var string|array|PinnerInterface
     */
    public $pinnerConfig = PostPinner::class;

    /**
     * @var string|array|PollBuilderInterface
     */
    public $pollBuilderConfig = PollBuilder::class;

    /**
     * @var string|array|VoterInterface
     */
    public $pollVoterConfig = PollVoter::class;

    /**
     * @var string|array|RemoverInterface
     */
    public $pollRemoverConfig = PollRemover::class;

    /**
     * @var string|array|PostRepositoryInterface
     */
    public $postRepositoryConfig;

    /**
     * @var string|array|ThumbRepositoryInterface
     */
    public $thumbRepositoryConfig;

    private ?PostRepositoryInterface $repository = null;

    /**
     * @throws InvalidConfigException
     */
    public function getRepository(): PostRepositoryInterface
    {
        if (null === $this->repository) {
            /** @var PostRepositoryInterface $repository */
            $repository = Instance::ensure($this->postRepositoryConfig, PostRepositoryInterface::class);
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
     * Creates post.
     *
     * @throws InvalidConfigException
     */
    public function create(
        MemberRepositoryInterface $author,
        ThreadRepositoryInterface $thread,
        array $data = []
    ): PodiumResponse {
        return $this->getBuilder()->create($this->getRepository(), $author, $thread, $data);
    }

    /**
     * Updates post.
     *
     * @throws InvalidConfigException
     */
    public function edit(PostRepositoryInterface $post, array $data = []): PodiumResponse
    {
        return $this->getBuilder()->edit($post, $data);
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
     * Deletes post.
     *
     * @throws InvalidConfigException
     */
    public function remove(PostRepositoryInterface $post): PodiumResponse
    {
        return $this->getRemover()->remove($post);
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
     * Moves post.
     *
     * @throws InvalidConfigException
     */
    public function move(PostRepositoryInterface $post, ThreadRepositoryInterface $thread): PodiumResponse
    {
        return $this->getMover()->move($post, $thread);
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
     * Archives post.
     *
     * @throws InvalidConfigException
     */
    public function archive(PostRepositoryInterface $post): PodiumResponse
    {
        return $this->getArchiver()->archive($post);
    }

    /**
     * Revives post.
     *
     * @throws InvalidConfigException
     */
    public function revive(PostRepositoryInterface $post): PodiumResponse
    {
        return $this->getArchiver()->revive($post);
    }

    private ?LikerInterface $liker = null;

    /**
     * @throws InvalidConfigException
     */
    public function getLiker(): LikerInterface
    {
        if (null === $this->liker) {
            /** @var LikerInterface $liker */
            $liker = Instance::ensure($this->likerConfig, LikerInterface::class);
            $this->liker = $liker;
        }

        return $this->liker;
    }

    private ?ThumbRepositoryInterface $thumbRepository = null;

    /**
     * @throws InvalidConfigException
     */
    public function getThumbRepository(): ThumbRepositoryInterface
    {
        if (null === $this->thumbRepository) {
            /** @var ThumbRepositoryInterface $repository */
            $repository = Instance::ensure($this->thumbRepositoryConfig, ThumbRepositoryInterface::class);
            $this->thumbRepository = $repository;
        }

        return $this->thumbRepository;
    }

    /**
     * Gives post a thumb up.
     *
     * @throws InvalidConfigException
     */
    public function thumbUp(PostRepositoryInterface $post, MemberRepositoryInterface $member): PodiumResponse
    {
        return $this->getLiker()->thumbUp($this->getThumbRepository(), $post, $member);
    }

    /**
     * Gives post a thumb down.
     *
     * @throws InvalidConfigException
     */
    public function thumbDown(PostRepositoryInterface $post, MemberRepositoryInterface $member): PodiumResponse
    {
        return $this->getLiker()->thumbDown($this->getThumbRepository(), $post, $member);
    }

    /**
     * Resets post given thumb.
     *
     * @throws InvalidConfigException
     */
    public function thumbReset(PostRepositoryInterface $post, MemberRepositoryInterface $member): PodiumResponse
    {
        return $this->getLiker()->thumbReset($this->getThumbRepository(), $post, $member);
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
     * Pins post.
     *
     * @throws InvalidConfigException
     */
    public function pin(PostRepositoryInterface $post): PodiumResponse
    {
        return $this->getPinner()->pin($post);
    }

    /**
     * Unpins post.
     *
     * @throws InvalidConfigException
     */
    public function unpin(PostRepositoryInterface $post): PodiumResponse
    {
        return $this->getPinner()->unpin($post);
    }

    private ?PollBuilderInterface $pollBuilder = null;

    /**
     * @throws InvalidConfigException
     */
    public function getPollBuilder(): PollBuilderInterface
    {
        if (null === $this->pollBuilder) {
            /** @var PollBuilderInterface $builder */
            $builder = Instance::ensure($this->pollBuilderConfig, PollBuilderInterface::class);
            $this->pollBuilder = $builder;
        }

        return $this->pollBuilder;
    }

    /**
     * @throws InvalidConfigException
     */
    public function addPoll(PollPostRepositoryInterface $post, array $answers, array $data = []): PodiumResponse
    {
        return $this->getPollBuilder()->create($post, $answers, $data);
    }

    /**
     * @throws InvalidConfigException
     */
    public function editPoll(PollPostRepositoryInterface $post, array $answers = [], array $data = []): PodiumResponse
    {
        return $this->getPollBuilder()->edit($post, $answers, $data);
    }

    private ?RemoverInterface $pollRemover = null;

    /**
     * @throws InvalidConfigException
     */
    public function getPollRemover(): RemoverInterface
    {
        if (null === $this->pollRemover) {
            /** @var RemoverInterface $remover */
            $remover = Instance::ensure($this->pollRemoverConfig, RemoverInterface::class);
            $this->pollRemover = $remover;
        }

        return $this->pollRemover;
    }

    /**
     * @throws InvalidConfigException
     */
    public function removePoll(PollPostRepositoryInterface $post): PodiumResponse
    {
        return $this->getPollRemover()->remove($post);
    }

    private ?VoterInterface $pollVoter = null;

    /**
     * @throws InvalidConfigException
     */
    public function getPollVoter(): VoterInterface
    {
        if (null === $this->pollVoter) {
            /** @var VoterInterface $voter */
            $voter = Instance::ensure($this->pollVoterConfig, VoterInterface::class);
            $this->pollVoter = $voter;
        }

        return $this->pollVoter;
    }

    /**
     * @throws InvalidConfigException
     */
    public function votePoll(
        PollPostRepositoryInterface $post,
        MemberRepositoryInterface $member,
        array $answers
    ): PodiumResponse {
        return $this->getPollVoter()->vote($post, $member, $answers);
    }
}
