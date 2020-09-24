<?php

declare(strict_types=1);

namespace Podium\Api\Components;

use Podium\Api\Interfaces\BuilderInterface;
use Podium\Api\Interfaces\GroupInterface;
use Podium\Api\Interfaces\GroupMemberRepositoryInterface;
use Podium\Api\Interfaces\GroupRepositoryInterface;
use Podium\Api\Interfaces\KeeperInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\Services\Group\GroupBuilder;
use Podium\Api\Services\Group\GroupKeeper;
use Podium\Api\Services\Group\GroupRemover;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\di\Instance;

final class Group extends Component implements GroupInterface
{
    /**
     * @var string|array|BuilderInterface
     */
    public $builderConfig = GroupBuilder::class;

    /**
     * @var string|array|RemoverInterface
     */
    public $removerConfig = GroupRemover::class;

    /**
     * @var string|array|KeeperInterface
     */
    public $keeperConfig = GroupKeeper::class;

    /**
     * @var string|array|GroupRepositoryInterface
     */
    public $groupRepositoryConfig;

    /**
     * @var string|array|GroupMemberRepositoryInterface
     */
    public $groupMemberRepositoryConfig;

    private ?GroupRepositoryInterface $repository = null;

    /**
     * @throws InvalidConfigException
     */
    public function getRepository(): GroupRepositoryInterface
    {
        if (null === $this->repository) {
            /** @var GroupRepositoryInterface $repository */
            $repository = Instance::ensure($this->groupRepositoryConfig, GroupRepositoryInterface::class);
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
     * Creates group.
     *
     * @throws InvalidConfigException
     */
    public function create(array $data = []): PodiumResponse
    {
        return $this->getBuilder()->create($this->getRepository(), $data);
    }

    /**
     * Updates group.
     *
     * @throws InvalidConfigException
     */
    public function edit(GroupRepositoryInterface $group, array $data = []): PodiumResponse
    {
        return $this->getBuilder()->edit($group, $data);
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
     * Deletes group.
     *
     * @throws InvalidConfigException
     */
    public function remove(GroupRepositoryInterface $group): PodiumResponse
    {
        return $this->getRemover()->remove($group);
    }

    private ?KeeperInterface $keeper = null;

    /**
     * @throws InvalidConfigException
     */
    public function getKeeper(): KeeperInterface
    {
        if (null === $this->keeper) {
            /** @var KeeperInterface $keeper */
            $keeper = Instance::ensure($this->keeperConfig, KeeperInterface::class);
            $this->keeper = $keeper;
        }

        return $this->keeper;
    }

    /**
     * Adds member to the group.
     *
     * @throws InvalidConfigException
     */
    public function join(GroupRepositoryInterface $group, MemberRepositoryInterface $member): PodiumResponse
    {
        return $this->getKeeper()->join($group, $member);
    }

    /**
     * Removes member from a group.
     *
     * @throws InvalidConfigException
     */
    public function leave(GroupRepositoryInterface $group, MemberRepositoryInterface $member): PodiumResponse
    {
        return $this->getKeeper()->leave($group, $member);
    }
}
