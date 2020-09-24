<?php

declare(strict_types=1);

namespace Podium\Api\Components;

use Podium\Api\Interfaces\ArchiverInterface;
use Podium\Api\Interfaces\CategoryBuilderInterface;
use Podium\Api\Interfaces\CategoryInterface;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\Interfaces\SorterInterface;
use Podium\Api\Services\Category\CategoryArchiver;
use Podium\Api\Services\Category\CategoryBuilder;
use Podium\Api\Services\Category\CategoryRemover;
use Podium\Api\Services\Category\CategorySorter;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\di\Instance;

final class Category extends Component implements CategoryInterface
{
    /**
     * @var string|array|CategoryBuilderInterface
     */
    public $builderConfig = CategoryBuilder::class;

    /**
     * @var string|array|SorterInterface
     */
    public $sorterConfig = CategorySorter::class;

    /**
     * @var string|array|RemoverInterface
     */
    public $removerConfig = CategoryRemover::class;

    /**
     * @var string|array|ArchiverInterface
     */
    public $archiverConfig = CategoryArchiver::class;

    /**
     * @var string|array|CategoryRepositoryInterface
     */
    public $repositoryConfig;

    private ?CategoryRepositoryInterface $repository = null;

    /**
     * @throws InvalidConfigException
     */
    public function getRepository(): CategoryRepositoryInterface
    {
        if (null === $this->repository) {
            /** @var CategoryRepositoryInterface $repository */
            $repository = Instance::ensure($this->repositoryConfig, CategoryRepositoryInterface::class);
            $this->repository = $repository;
        }

        return $this->repository;
    }

    private ?CategoryBuilderInterface $builder = null;

    /**
     * @throws InvalidConfigException
     */
    public function getBuilder(): CategoryBuilderInterface
    {
        if (null === $this->builder) {
            /** @var CategoryBuilderInterface $builder */
            $builder = Instance::ensure($this->builderConfig, CategoryBuilderInterface::class);
            $this->builder = $builder;
        }

        return $this->builder;
    }

    /**
     * Creates category.
     *
     * @throws InvalidConfigException
     */
    public function create(MemberRepositoryInterface $author, array $data = []): PodiumResponse
    {
        return $this->getBuilder()->create($this->getRepository(), $author, $data);
    }

    /**
     * Updates the category.
     *
     * @throws InvalidConfigException
     */
    public function edit(CategoryRepositoryInterface $category, array $data = []): PodiumResponse
    {
        return $this->getBuilder()->edit($category, $data);
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
     * Removes the category.
     *
     * @throws InvalidConfigException
     */
    public function remove(CategoryRepositoryInterface $category): PodiumResponse
    {
        return $this->getRemover()->remove($category);
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
     * Replaces the order of the categories.
     *
     * @throws InvalidConfigException
     */
    public function replace(
        CategoryRepositoryInterface $firstCategory,
        CategoryRepositoryInterface $secondCategory
    ): PodiumResponse {
        return $this->getSorter()->replace($firstCategory, $secondCategory);
    }

    /**
     * Normalizes the sort order of all categories.
     *
     * @throws InvalidConfigException
     */
    public function sort(): PodiumResponse
    {
        return $this->getSorter()->sort($this->getRepository());
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
     * Archives the category.
     *
     * @throws InvalidConfigException
     */
    public function archive(CategoryRepositoryInterface $category): PodiumResponse
    {
        return $this->getArchiver()->archive($category);
    }

    /**
     * Revives the category.
     *
     * @throws InvalidConfigException
     */
    public function revive(CategoryRepositoryInterface $category): PodiumResponse
    {
        return $this->getArchiver()->revive($category);
    }
}
