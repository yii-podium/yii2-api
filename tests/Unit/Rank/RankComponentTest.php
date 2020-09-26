<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Rank;

use PHPUnit\Framework\TestCase;
use Podium\Api\Components\PodiumResponse;
use Podium\Api\Components\Rank;
use Podium\Api\Interfaces\BuilderInterface;
use Podium\Api\Interfaces\RankRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use yii\base\InvalidConfigException;

class RankComponentTest extends TestCase
{
    private Rank $component;

    protected function setUp(): void
    {
        $this->component = new Rank();
    }

    public function testGetBuilderShouldThrowExceptionWhenBuilderIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->builderConfig = '';

        $this->component->getBuilder();
    }

    public function testCreateShouldRunBuildersCreate(): void
    {
        $builder = $this->createMock(BuilderInterface::class);
        $builder->expects(self::once())->method('create')->willReturn(PodiumResponse::success());
        $this->component->builderConfig = $builder;
        $this->component->repositoryConfig = $this->createMock(RankRepositoryInterface::class);

        $this->component->create();
    }

    public function testEditShouldRunBuildersEdit(): void
    {
        $builder = $this->createMock(BuilderInterface::class);
        $builder->expects(self::once())->method('edit')->willReturn(PodiumResponse::success());
        $this->component->builderConfig = $builder;

        $this->component->edit($this->createMock(RankRepositoryInterface::class));
    }

    public function testGetRemoverShouldThrowExceptionWhenRemoverIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->removerConfig = '';

        $this->component->getRemover();
    }

    public function testRemoveShouldRunRemoversRemove(): void
    {
        $remover = $this->createMock(RemoverInterface::class);
        $remover->expects(self::once())->method('remove')->willReturn(PodiumResponse::success());
        $this->component->removerConfig = $remover;

        $this->component->remove($this->createMock(RankRepositoryInterface::class));
    }
}
