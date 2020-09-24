<?php

declare(strict_types=1);

namespace Podium\Api\Components;

use Podium\Api\Interfaces\BuilderInterface;
use Podium\Api\Interfaces\RankInterface;
use Podium\Api\Interfaces\RankRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\Services\Rank\RankBuilder;
use Podium\Api\Services\Rank\RankRemover;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\di\Instance;

final class Rank extends Component implements RankInterface
{
    /**
     * @var string|array|BuilderInterface
     */
    public $builderConfig = RankBuilder::class;

    /**
     * @var string|array|RemoverInterface
     */
    public $removerConfig = RankRemover::class;

    /**
     * @var string|array|RankRepositoryInterface
     */
    public $repositoryConfig;

    private ?RankRepositoryInterface $repository = null;

    /**
     * @throws InvalidConfigException
     */
    public function getRepository(): RankRepositoryInterface
    {
        if (null === $this->repository) {
            /** @var RankRepositoryInterface $repository */
            $repository = Instance::ensure($this->repositoryConfig, RankRepositoryInterface::class);
            $this->repository = $repository;
        }

        return $this->repository;
    }

    private ?BuilderInterface $builder = null;

    /**
     * @throws InvalidConfigException
     */
    public function getBuilder(): BuilderInterface
    {
        if (null === $this->builder) {
            /** @var BuilderInterface $builder */
            $builder = Instance::ensure($this->builderConfig, BuilderInterface::class);
            $this->builder = $builder;
        }

        return $this->builder;
    }

    /**
     * Creates rank.
     *
     * @throws InvalidConfigException
     */
    public function create(array $data = []): PodiumResponse
    {
        return $this->getBuilder()->create($this->getRepository(), $data);
    }

    /**
     * Updates rank.
     *
     * @throws InvalidConfigException
     */
    public function edit(RankRepositoryInterface $rank, array $data = []): PodiumResponse
    {
        return $this->getBuilder()->edit($rank, $data);
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
     * Deletes rank.
     *
     * @throws InvalidConfigException
     */
    public function remove(RankRepositoryInterface $rank): PodiumResponse
    {
        return $this->getRemover()->remove($rank);
    }
}
