<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Group;

use PHPUnit\Framework\TestCase;
use Podium\Api\Components\Group;
use Podium\Api\Components\PodiumResponse;
use Podium\Api\Interfaces\BuilderInterface;
use Podium\Api\Interfaces\GroupRepositoryInterface;
use Podium\Api\Interfaces\KeeperInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use yii\base\InvalidConfigException;

class GroupComponentTest extends TestCase
{
    private Group $component;

    protected function setUp(): void
    {
        $this->component = new Group();
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
        $this->component->repositoryConfig = $this->createMock(GroupRepositoryInterface::class);

        $this->component->create();
    }

    public function testEditShouldRunBuildersEdit(): void
    {
        $builder = $this->createMock(BuilderInterface::class);
        $builder->expects(self::once())->method('edit')->willReturn(PodiumResponse::success());
        $this->component->builderConfig = $builder;

        $this->component->edit($this->createMock(GroupRepositoryInterface::class));
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

        $this->component->remove($this->createMock(GroupRepositoryInterface::class));
    }

    public function testGetKeeperShouldThrowExceptionWhenKeeperIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->keeperConfig = '';

        $this->component->getKeeper();
    }

    public function testJoinShouldRunKeepersJoin(): void
    {
        $keeper = $this->createMock(KeeperInterface::class);
        $keeper->expects(self::once())->method('join')->willReturn(PodiumResponse::success());
        $this->component->keeperConfig = $keeper;

        $this->component->join(
            $this->createMock(GroupRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );
    }

    public function testLeaveShouldRunKeepersLeave(): void
    {
        $keeper = $this->createMock(KeeperInterface::class);
        $keeper->expects(self::once())->method('leave')->willReturn(PodiumResponse::success());
        $this->component->keeperConfig = $keeper;

        $this->component->leave(
            $this->createMock(GroupRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );
    }
}
