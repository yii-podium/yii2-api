<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Permit;

use PHPUnit\Framework\TestCase;
use Podium\Api\Components\Permit;
use Podium\Api\Interfaces\BuilderInterface;
use Podium\Api\Interfaces\CheckerInterface;
use Podium\Api\Interfaces\DeciderInterface;
use Podium\Api\Interfaces\GranterInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\Interfaces\RoleRepositoryInterface;
use Podium\Api\PodiumDecision;
use Podium\Api\PodiumResponse;
use yii\base\InvalidConfigException;

class PermitComponentTest extends TestCase
{
    private Permit $component;

    protected function setUp(): void
    {
        $this->component = new Permit();
    }

    public function testGetBuilderShouldThrowExceptionWhenBuilderIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->builderConfig = '';

        $this->component->getBuilder();
    }

    public function testCreateRoleShouldRunBuildersCreate(): void
    {
        $builder = $this->createMock(BuilderInterface::class);
        $builder->expects(self::once())->method('create')->willReturn(PodiumResponse::success());
        $this->component->builderConfig = $builder;
        $this->component->repositoryConfig = $this->createMock(RoleRepositoryInterface::class);

        $this->component->createRole([]);
    }

    public function testEditRoleShouldRunBuildersEdit(): void
    {
        $builder = $this->createMock(BuilderInterface::class);
        $builder->expects(self::once())->method('edit')->willReturn(PodiumResponse::success());
        $this->component->builderConfig = $builder;

        $this->component->editRole($this->createMock(RoleRepositoryInterface::class));
    }

    public function testGetRemoverShouldThrowExceptionWhenRemoverIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->removerConfig = '';

        $this->component->getRemover();
    }

    public function testRemoveRoleShouldRunRemoversRemove(): void
    {
        $remover = $this->createMock(RemoverInterface::class);
        $remover->expects(self::once())->method('remove')->willReturn(PodiumResponse::success());
        $this->component->removerConfig = $remover;

        $this->component->removeRole($this->createMock(RoleRepositoryInterface::class));
    }

    public function testGetGranterShouldThrowExceptionWhenGranterIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->granterConfig = '';

        $this->component->getGranter();
    }

    public function testGrantRoleShouldRunGrantersGrant(): void
    {
        $granter = $this->createMock(GranterInterface::class);
        $granter->expects(self::once())->method('grant')->willReturn(PodiumResponse::success());
        $this->component->granterConfig = $granter;

        $this->component->grantRole(
            $this->createMock(MemberRepositoryInterface::class),
            $this->createMock(RoleRepositoryInterface::class)
        );
    }

    public function testRevokeRoleShouldRunGrantersRevoke(): void
    {
        $granter = $this->createMock(GranterInterface::class);
        $granter->expects(self::once())->method('revoke')->willReturn(PodiumResponse::success());
        $this->component->granterConfig = $granter;

        $this->component->revokeRole(
            $this->createMock(MemberRepositoryInterface::class),
            $this->createMock(RoleRepositoryInterface::class)
        );
    }

    public function testGetCheckerShouldThrowExceptionWhenCheckerIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->checkerConfig = '';

        $this->component->getChecker();
    }

    public function testCheckShouldRunCheckersCheck(): void
    {
        $checker = $this->createMock(CheckerInterface::class);
        $checker->expects(self::once())->method('check')->willReturn(PodiumDecision::allow());
        $this->component->checkerConfig = $checker;

        $this->component->check($this->createMock(DeciderInterface::class), '');
    }
}
