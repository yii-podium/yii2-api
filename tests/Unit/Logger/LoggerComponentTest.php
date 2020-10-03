<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Logger;

use PHPUnit\Framework\TestCase;
use Podium\Api\Components\Logger;
use Podium\Api\Interfaces\LogBuilderInterface;
use Podium\Api\Interfaces\LogRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\PodiumResponse;
use yii\base\InvalidConfigException;

class LoggerComponentTest extends TestCase
{
    private Logger $component;

    protected function setUp(): void
    {
        $this->component = new Logger();
    }

    public function testGetBuilderShouldThrowExceptionWhenBuilderIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->builderConfig = '';

        $this->component->getBuilder();
    }

    public function testCreateShouldRunBuildersCreate(): void
    {
        $builder = $this->createMock(LogBuilderInterface::class);
        $builder->expects(self::once())->method('create')->willReturn(PodiumResponse::success());
        $this->component->builderConfig = $builder;
        $this->component->repositoryConfig = $this->createMock(LogRepositoryInterface::class);

        $this->component->create($this->createMock(MemberRepositoryInterface::class), 'action');
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

        $this->component->remove($this->createMock(LogRepositoryInterface::class));
    }

    public function testGetRepositoryShouldThrowExceptionWhenRepositoryIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->repositoryConfig = '';

        $this->component->getRepository();
    }
}
