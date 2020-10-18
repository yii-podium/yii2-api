<?php

declare(strict_types=1);

namespace Podium\Api\Components;

use Podium\Api\Interfaces\LogBuilderInterface;
use Podium\Api\Interfaces\LogInterface;
use Podium\Api\Interfaces\LogRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\PodiumResponse;
use Podium\Api\Services\Log\LogBuilder;
use Podium\Api\Services\Log\LogRemover;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\di\Instance;

final class Log extends Component implements LogInterface
{
    /**
     * @var string|array|LogBuilderInterface
     */
    public $builderConfig = LogBuilder::class;

    /**
     * @var string|array|RemoverInterface
     */
    public $removerConfig = LogRemover::class;

    /**
     * @var string|array|LogRepositoryInterface
     */
    public $repositoryConfig;

    private ?LogRepositoryInterface $repository = null;

    /**
     * @throws InvalidConfigException
     */
    public function getRepository(): LogRepositoryInterface
    {
        if (null === $this->repository) {
            /** @var LogRepositoryInterface $repository */
            $repository = Instance::ensure($this->repositoryConfig, LogRepositoryInterface::class);
            $this->repository = $repository;
        }

        return $this->repository;
    }

    private ?LogBuilderInterface $builder = null;

    /**
     * @throws InvalidConfigException
     */
    public function getBuilder(): LogBuilderInterface
    {
        if (null === $this->builder) {
            /** @var LogBuilderInterface $builder */
            $builder = Instance::ensure($this->builderConfig, LogBuilderInterface::class);
            $this->builder = $builder;
        }

        return $this->builder;
    }

    /**
     * @throws InvalidConfigException
     */
    public function create(MemberRepositoryInterface $author, string $action, array $data = []): PodiumResponse
    {
        return $this->getBuilder()->create($this->getRepository(), $author, $action, $data);
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
    public function remove(LogRepositoryInterface $log): PodiumResponse
    {
        return $this->getRemover()->remove($log);
    }
}
