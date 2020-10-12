<?php

declare(strict_types=1);

namespace Podium\Api\Components;

use Podium\Api\Interfaces\ArchiverInterface;
use Podium\Api\Interfaces\CategorisedBuilderInterface;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\ForumInterface;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\HiderInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\MoverInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\Interfaces\SorterInterface;
use Podium\Api\PodiumResponse;
use Podium\Api\Services\Forum\ForumArchiver;
use Podium\Api\Services\Forum\ForumBuilder;
use Podium\Api\Services\Forum\ForumMover;
use Podium\Api\Services\Forum\ForumRemover;
use Podium\Api\Services\Forum\ForumSorter;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\di\Instance;

final class Forum extends Component implements ForumInterface
{
    /**
     * @var string|array|CategorisedBuilderInterface
     */
    public $builderConfig = ForumBuilder::class;

    /**
     * @var string|array|SorterInterface
     */
    public $sorterConfig = ForumSorter::class;

    /**
     * @var string|array|RemoverInterface
     */
    public $removerConfig = ForumRemover::class;

    /**
     * @var string|array|ArchiverInterface
     */
    public $archiverConfig = ForumArchiver::class;

    /**
     * @var string|array|MoverInterface
     */
    public $moverConfig = ForumMover::class;

    /**
     * @var string|array|ForumRepositoryInterface
     */
    public $repositoryConfig;

    private ?ForumRepositoryInterface $repository = null;

    /**
     * @throws InvalidConfigException
     */
    public function getRepository(): ForumRepositoryInterface
    {
        if (null === $this->repository) {
            /** @var ForumRepositoryInterface $repository */
            $repository = Instance::ensure($this->repositoryConfig, ForumRepositoryInterface::class);
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
     * Creates a forum.
     *
     * @throws InvalidConfigException
     */
    public function create(
        MemberRepositoryInterface $author,
        CategoryRepositoryInterface $category,
        array $data = []
    ): PodiumResponse {
        return $this->getBuilder()->create($this->getRepository(), $author, $category, $data);
    }

    /**
     * Updates the forum.
     *
     * @throws InvalidConfigException
     */
    public function edit(ForumRepositoryInterface $forum, array $data = []): PodiumResponse
    {
        return $this->getBuilder()->edit($forum, $data);
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
     * Deletes the forum.
     *
     * @throws InvalidConfigException
     */
    public function remove(ForumRepositoryInterface $forum): PodiumResponse
    {
        return $this->getRemover()->remove($forum);
    }

    private ?SorterInterface $sorter = null;

    /**
     * @throws InvalidConfigException
     */
    public function getSorter(): SorterInterface
    {
        if (null === $this->sorter) {
            /** @var SorterInterface $sorter */
            $sorter = Instance::ensure($this->sorterConfig, SorterInterface::class);
            $this->sorter = $sorter;
        }

        return $this->sorter;
    }

    /**
     * Replaces the order of the forums.
     *
     * @throws InvalidConfigException
     */
    public function replace(
        ForumRepositoryInterface $firstForum,
        ForumRepositoryInterface $secondForum
    ): PodiumResponse {
        return $this->getSorter()->replace($firstForum, $secondForum);
    }

    /**
     * Sorts the forums.
     *
     * @throws InvalidConfigException
     */
    public function sort(): PodiumResponse
    {
        return $this->getSorter()->sort($this->getRepository());
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
     * Moves the forum.
     *
     * @throws InvalidConfigException
     */
    public function move(ForumRepositoryInterface $forum, CategoryRepositoryInterface $category): PodiumResponse
    {
        return $this->getMover()->move($forum, $category);
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
     * Archives the forum.
     *
     * @throws InvalidConfigException
     */
    public function archive(ForumRepositoryInterface $forum): PodiumResponse
    {
        return $this->getArchiver()->archive($forum);
    }

    /**
     * Revives the forum.
     *
     * @throws InvalidConfigException
     */
    public function revive(ForumRepositoryInterface $forum): PodiumResponse
    {
        return $this->getArchiver()->revive($forum);
    }

    private ?HiderInterface $hider = null;

    /**
     * @throws InvalidConfigException
     */
    public function getHider(): HiderInterface
    {
        if (null === $this->hider) {
            /** @var HiderInterface $hider */
            $hider = Instance::ensure($this->hiderConfig, HiderInterface::class);
            $this->hider = $hider;
        }

        return $this->hider;
    }

    /**
     * @throws InvalidConfigException
     */
    public function hide(ForumRepositoryInterface $forum): PodiumResponse
    {
        return $this->getHider()->hide($forum);
    }

    /**
     * @throws InvalidConfigException
     */
    public function reveal(ForumRepositoryInterface $forum): PodiumResponse
    {
        return $this->getHider()->reveal($forum);
    }
}
