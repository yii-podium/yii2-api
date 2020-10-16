<?php

declare(strict_types=1);

namespace Podium\Api\Components;

use Podium\Api\Interfaces\AcquaintanceInterface;
use Podium\Api\Interfaces\AcquaintanceRepositoryInterface;
use Podium\Api\Interfaces\BanisherInterface;
use Podium\Api\Interfaces\MemberBuilderInterface;
use Podium\Api\Interfaces\MemberInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\PodiumResponse;
use Podium\Api\Services\Member\MemberAcquaintance;
use Podium\Api\Services\Member\MemberBanisher;
use Podium\Api\Services\Member\MemberBuilder;
use Podium\Api\Services\Member\MemberRemover;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\di\Instance;

final class Member extends Component implements MemberInterface
{
    /**
     * @var string|array|MemberBuilderInterface
     */
    public $builderConfig = MemberBuilder::class;

    /**
     * @var string|array|AcquaintanceInterface
     */
    public $acquaintanceConfig = MemberAcquaintance::class;

    /**
     * @var string|array|RemoverInterface
     */
    public $removerConfig = MemberRemover::class;

    /**
     * @var string|array|BanisherInterface
     */
    public $banisherConfig = MemberBanisher::class;

    /**
     * @var string|array|MemberRepositoryInterface
     */
    public $memberRepositoryConfig;

    /**
     * @var string|array|AcquaintanceRepositoryInterface
     */
    public $acquaintanceRepositoryConfig;

    private ?MemberRepositoryInterface $repository = null;

    /**
     * @throws InvalidConfigException
     */
    public function getMemberRepository(): MemberRepositoryInterface
    {
        if (null === $this->repository) {
            /** @var MemberRepositoryInterface $repository */
            $repository = Instance::ensure($this->memberRepositoryConfig, MemberRepositoryInterface::class);
            $this->repository = $repository;
        }

        return $this->repository;
    }

    private ?MemberBuilderInterface $builder = null;

    /**
     * @throws InvalidConfigException
     */
    public function getBuilder(): MemberBuilderInterface
    {
        if (null === $this->builder) {
            /** @var MemberBuilderInterface $builder */
            $builder = Instance::ensure($this->builderConfig, MemberBuilderInterface::class);
            $this->builder = $builder;
        }

        return $this->builder;
    }

    /**
     * @throws InvalidConfigException
     */
    public function register($id, array $data = []): PodiumResponse
    {
        return $this->getBuilder()->register($this->getMemberRepository(), $id, $data);
    }

    /**
     * @throws InvalidConfigException
     */
    public function edit(MemberRepositoryInterface $member, array $data = []): PodiumResponse
    {
        return $this->getBuilder()->edit($member, $data);
    }

    private ?AcquaintanceInterface $acquaintance = null;

    /**
     * @throws InvalidConfigException
     */
    public function getAcquaintance(): AcquaintanceInterface
    {
        if (null === $this->acquaintance) {
            /** @var AcquaintanceInterface $acquaintance */
            $acquaintance = Instance::ensure($this->acquaintanceConfig, AcquaintanceInterface::class);
            $this->acquaintance = $acquaintance;
        }

        return $this->acquaintance;
    }

    private ?AcquaintanceRepositoryInterface $acquaintanceRepository = null;

    /**
     * @throws InvalidConfigException
     */
    public function getAcquaintanceRepository(): AcquaintanceRepositoryInterface
    {
        if (null === $this->acquaintanceRepository) {
            /** @var AcquaintanceRepositoryInterface $repository */
            $repository = Instance::ensure(
                $this->acquaintanceRepositoryConfig,
                AcquaintanceRepositoryInterface::class
            );
            $this->acquaintanceRepository = $repository;
        }

        return $this->acquaintanceRepository;
    }

    /**
     * @throws InvalidConfigException
     */
    public function befriend(MemberRepositoryInterface $member, MemberRepositoryInterface $target): PodiumResponse
    {
        return $this->getAcquaintance()->befriend($this->getAcquaintanceRepository(), $member, $target);
    }

    /**
     * @throws InvalidConfigException
     */
    public function ignore(MemberRepositoryInterface $member, MemberRepositoryInterface $target): PodiumResponse
    {
        return $this->getAcquaintance()->ignore($this->getAcquaintanceRepository(), $member, $target);
    }

    /**
     * @throws InvalidConfigException
     */
    public function disconnect(MemberRepositoryInterface $member, MemberRepositoryInterface $target): PodiumResponse
    {
        return $this->getAcquaintance()->disconnect($this->getAcquaintanceRepository(), $member, $target);
    }

    private ?BanisherInterface $banisher = null;

    /**
     * @throws InvalidConfigException
     */
    public function getBanisher(): BanisherInterface
    {
        if (null === $this->banisher) {
            /** @var BanisherInterface $banisher */
            $banisher = Instance::ensure($this->banisherConfig, BanisherInterface::class);
            $this->banisher = $banisher;
        }

        return $this->banisher;
    }

    /**
     * @throws InvalidConfigException
     */
    public function ban(MemberRepositoryInterface $member): PodiumResponse
    {
        return $this->getBanisher()->ban($member);
    }

    /**
     * @throws InvalidConfigException
     */
    public function unban(MemberRepositoryInterface $member): PodiumResponse
    {
        return $this->getBanisher()->unban($member);
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
    public function remove(MemberRepositoryInterface $member): PodiumResponse
    {
        return $this->getRemover()->remove($member);
    }
}
