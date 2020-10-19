<?php

declare(strict_types=1);

namespace Podium\Api\Components;

use Podium\Api\Interfaces\AllowerInterface;
use Podium\Api\Interfaces\BuilderInterface;
use Podium\Api\Interfaces\CheckerInterface;
use Podium\Api\Interfaces\GranterInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\PermitInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\Interfaces\RoleRepositoryInterface;
use Podium\Api\PodiumResponse;
use Podium\Api\Services\Permit\RoleBuilder;
use Podium\Api\Services\Permit\RoleRemover;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\di\Instance;

final class Permit extends Component implements PermitInterface
{
    /**
     * @var string|array|BuilderInterface
     */
    public $builderConfig = RoleBuilder::class;

    /**
     * @var string|array|RemoverInterface
     */
    public $removerConfig = RoleRemover::class;

    /**
     * @var string|array|GranterInterface
     */
    public $granterConfig = RoleGranter::class;

    /**
     * @var string|array|CheckerInterface
     */
    public $checkerConfig = PermitChecker::class;

    /**
     * @var string|array|RoleRepositoryInterface
     */
    public $repositoryConfig;

    private ?RoleRepositoryInterface $repository = null;

    /**
     * @throws InvalidConfigException
     */
    public function getRepository(): RoleRepositoryInterface
    {
        if (null === $this->repository) {
            /** @var RoleRepositoryInterface $repository */
            $repository = Instance::ensure($this->repositoryConfig, RoleRepositoryInterface::class);
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
     * @throws InvalidConfigException
     */
    public function createRole(array $data): PodiumResponse
    {
        return $this->getBuilder()->create($this->getRepository(), $data);
    }

    public function editRole(RoleRepositoryInterface $role, array $data): PodiumResponse
    {
        return $this->getBuilder()->edit($role, $data);
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
    public function removeRole(RoleRepositoryInterface $role): PodiumResponse
    {
        return $this->getRemover()->remove($role);
    }

    private ?GranterInterface $granter = null;

    /**
     * @throws InvalidConfigException
     */
    public function getGranter(): GranterInterface
    {
        if (null === $this->granter) {
            /** @var GranterInterface $granter */
            $granter = Instance::ensure($this->granterConfig, GranterInterface::class);
            $this->granter = $granter;
        }

        return $this->granter;
    }

    public function grantRole(MemberRepositoryInterface $member, RoleRepositoryInterface $role): PodiumResponse
    {
        return $this->getGranter()->grant($member, $role);
    }

    public function revokeRole(MemberRepositoryInterface $member, RoleRepositoryInterface $role): PodiumResponse
    {
        return $this->getGranter()->revoke($member, $role);
    }

    private ?CheckerInterface $checker = null;

    /**
     * @throws InvalidConfigException
     */
    public function getChecker(): CheckerInterface
    {
        if (null === $this->checker) {
            /** @var CheckerInterface $checker */
            $checker = Instance::ensure($this->checkerConfig, CheckerInterface::class);
            $this->checker = $checker;
        }

        return $this->checker;
    }

    public function check(AllowerInterface $allower): bool
    {
        return $this->getChecker()->check($allower);
    }
}
