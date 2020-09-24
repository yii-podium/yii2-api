<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Forum;

use PHPUnit\Framework\TestCase;
use Podium\Api\Components\Forum;
use Podium\Api\Components\PodiumResponse;
use Podium\Api\Interfaces\ArchiverInterface;
use Podium\Api\Interfaces\CategorisedBuilderInterface;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\MoverInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\Interfaces\SorterInterface;
use yii\base\InvalidConfigException;

class ForumComponentTest extends TestCase
{
    private Forum $component;

    protected function setUp(): void
    {
        $this->component = new Forum();
    }

    public function testGetBuilderShouldThrowExceptionWhenBuilderIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->builderConfig = '';

        $this->component->getBuilder();
    }

    public function testCreateShouldRunBuildersCreate(): void
    {
        $builder = $this->createMock(CategorisedBuilderInterface::class);
        $builder->expects(self::once())->method('create')->willReturn(PodiumResponse::success());
        $this->component->builderConfig = $builder;
        $this->component->repositoryConfig = $this->createMock(ForumRepositoryInterface::class);

        $this->component->create(
            $this->createMock(MemberRepositoryInterface::class),
            $this->createMock(CategoryRepositoryInterface::class)
        );
    }

    public function testEditShouldRunBuildersEdit(): void
    {
        $builder = $this->createMock(CategorisedBuilderInterface::class);
        $builder->expects(self::once())->method('edit')->willReturn(PodiumResponse::success());
        $this->component->builderConfig = $builder;

        $this->component->edit($this->createMock(ForumRepositoryInterface::class));
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

        $this->component->remove($this->createMock(ForumRepositoryInterface::class));
    }

    public function testGetSorterShouldThrowExceptionWhenSorterIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->sorterConfig = '';

        $this->component->getSorter();
    }

    public function testReplaceShouldRunSortersReplace(): void
    {
        $sorter = $this->createMock(SorterInterface::class);
        $sorter->expects(self::once())->method('replace')->willReturn(PodiumResponse::success());
        $this->component->sorterConfig = $sorter;

        $category = $this->createMock(ForumRepositoryInterface::class);
        $this->component->replace($category, $category);
    }

    public function testSortShouldRunSortersSort(): void
    {
        $sorter = $this->createMock(SorterInterface::class);
        $sorter->expects(self::once())->method('sort')->willReturn(PodiumResponse::success());
        $this->component->sorterConfig = $sorter;
        $this->component->repositoryConfig = $this->createMock(ForumRepositoryInterface::class);

        $this->component->sort();
    }

    public function testGetArchiverShouldThrowExceptionWhenArchiverIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->archiverConfig = '';

        $this->component->getArchiver();
    }

    public function testArchiveShouldRunArchiversArchive(): void
    {
        $archiver = $this->createMock(ArchiverInterface::class);
        $archiver->expects(self::once())->method('archive')->willReturn(PodiumResponse::success());
        $this->component->archiverConfig = $archiver;

        $this->component->archive($this->createMock(ForumRepositoryInterface::class));
    }

    public function testReviveShouldRunArchiversRevive(): void
    {
        $archiver = $this->createMock(ArchiverInterface::class);
        $archiver->expects(self::once())->method('revive')->willReturn(PodiumResponse::success());
        $this->component->archiverConfig = $archiver;

        $this->component->revive($this->createMock(ForumRepositoryInterface::class));
    }

    public function testGetMoverShouldThrowExceptionWhenMoverIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->moverConfig = '';

        $this->component->getMover();
    }

    public function testMoveShouldRunMoversMove(): void
    {
        $mover = $this->createMock(MoverInterface::class);
        $mover->expects(self::once())->method('move')->willReturn(PodiumResponse::success());
        $this->component->moverConfig = $mover;

        $this->component->move(
            $this->createMock(ForumRepositoryInterface::class),
            $this->createMock(CategoryRepositoryInterface::class)
        );
    }
}
