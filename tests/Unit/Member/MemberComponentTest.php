<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Member;

use PHPUnit\Framework\TestCase;
use Podium\Api\Components\Member;
use Podium\Api\Components\PodiumResponse;
use Podium\Api\Interfaces\AcquaintanceInterface;
use Podium\Api\Interfaces\AcquaintanceRepositoryInterface;
use Podium\Api\Interfaces\BanisherInterface;
use Podium\Api\Interfaces\MemberBuilderInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use yii\base\InvalidConfigException;

class MemberComponentTest extends TestCase
{
    private Member $component;

    protected function setUp(): void
    {
        $this->component = new Member();
    }

    public function testGetBuilderShouldThrowExceptionWhenBuilderIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->builderConfig = '';

        $this->component->getBuilder();
    }

    public function testCreateShouldRunBuildersCreate(): void
    {
        $builder = $this->createMock(MemberBuilderInterface::class);
        $builder->expects(self::once())->method('register')->willReturn(PodiumResponse::success());
        $this->component->builderConfig = $builder;
        $this->component->memberRepositoryConfig = $this->createMock(MemberRepositoryInterface::class);

        $this->component->register(1);
    }

    public function testEditShouldRunBuildersEdit(): void
    {
        $builder = $this->createMock(MemberBuilderInterface::class);
        $builder->expects(self::once())->method('edit')->willReturn(PodiumResponse::success());
        $this->component->builderConfig = $builder;

        $this->component->edit($this->createMock(MemberRepositoryInterface::class));
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

        $this->component->remove($this->createMock(MemberRepositoryInterface::class));
    }

    public function testGetAcquaintanceShouldThrowExceptionWhenAcquaintanceIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->acquaintanceConfig = '';

        $this->component->getAcquaintance();
    }

    public function testGetAcquaintanceRepositoryShouldThrowExceptionWhenAcquaintanceRepositoryIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->acquaintanceRepositoryConfig = '';

        $this->component->getAcquaintanceRepository();
    }

    public function testBefriendShouldRunAcquaintanceBefriend(): void
    {
        $acquaintance = $this->createMock(AcquaintanceInterface::class);
        $acquaintance->expects(self::once())->method('befriend')->willReturn(PodiumResponse::success());
        $this->component->acquaintanceConfig = $acquaintance;
        $this->component->acquaintanceRepositoryConfig = $this->createMock(AcquaintanceRepositoryInterface::class);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $this->component->befriend($member, $member);
    }

    public function testUnfriendShouldRunAcquaintanceUnfriend(): void
    {
        $acquaintance = $this->createMock(AcquaintanceInterface::class);
        $acquaintance->expects(self::once())->method('unfriend')->willReturn(PodiumResponse::success());
        $this->component->acquaintanceConfig = $acquaintance;
        $this->component->acquaintanceRepositoryConfig = $this->createMock(AcquaintanceRepositoryInterface::class);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $this->component->unfriend($member, $member);
    }

    public function testIgnoreShouldRunAcquaintanceIgnore(): void
    {
        $acquaintance = $this->createMock(AcquaintanceInterface::class);
        $acquaintance->expects(self::once())->method('ignore')->willReturn(PodiumResponse::success());
        $this->component->acquaintanceConfig = $acquaintance;
        $this->component->acquaintanceRepositoryConfig = $this->createMock(AcquaintanceRepositoryInterface::class);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $this->component->ignore($member, $member);
    }

    public function testUnignoreShouldRunAcquaintanceUnignore(): void
    {
        $acquaintance = $this->createMock(AcquaintanceInterface::class);
        $acquaintance->expects(self::once())->method('unignore')->willReturn(PodiumResponse::success());
        $this->component->acquaintanceConfig = $acquaintance;
        $this->component->acquaintanceRepositoryConfig = $this->createMock(AcquaintanceRepositoryInterface::class);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $this->component->unignore($member, $member);
    }

    public function testGetBanisherShouldThrowExceptionWhenBanisherIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->banisherConfig = '';

        $this->component->getBanisher();
    }

    public function testBanShouldRunBanisherBan(): void
    {
        $banisher = $this->createMock(BanisherInterface::class);
        $banisher->expects(self::once())->method('ban')->willReturn(PodiumResponse::success());
        $this->component->banisherConfig = $banisher;

        $this->component->ban($this->createMock(MemberRepositoryInterface::class));
    }

    public function testUnbanShouldRunBanisherUnban(): void
    {
        $banisher = $this->createMock(BanisherInterface::class);
        $banisher->expects(self::once())->method('unban')->willReturn(PodiumResponse::success());
        $this->component->banisherConfig = $banisher;

        $this->component->unban($this->createMock(MemberRepositoryInterface::class));
    }
}
