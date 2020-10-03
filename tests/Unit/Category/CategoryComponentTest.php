<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Category;

use PHPUnit\Framework\TestCase;
use Podium\Api\Components\Category;
use Podium\Api\Interfaces\ArchiverInterface;
use Podium\Api\Interfaces\CategoryBuilderInterface;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\Interfaces\SorterInterface;
use Podium\Api\PodiumResponse;
use yii\base\InvalidConfigException;

class CategoryComponentTest extends TestCase
{
    private Category $component;

    protected function setUp(): void
    {
        $this->component = new Category();
    }

    public function testGetBuilderShouldThrowExceptionWhenBuilderIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->builderConfig = '';

        $this->component->getBuilder();
    }

    public function testCreateShouldRunBuildersCreate(): void
    {
        $builder = $this->createMock(CategoryBuilderInterface::class);
        $builder->expects(self::once())->method('create')->willReturn(PodiumResponse::success());
        $this->component->builderConfig = $builder;
        $this->component->repositoryConfig = $this->createMock(CategoryRepositoryInterface::class);

        $this->component->create($this->createMock(MemberRepositoryInterface::class));
    }

    public function testEditShouldRunBuildersEdit(): void
    {
        $builder = $this->createMock(CategoryBuilderInterface::class);
        $builder->expects(self::once())->method('edit')->willReturn(PodiumResponse::success());
        $this->component->builderConfig = $builder;

        $this->component->edit($this->createMock(CategoryRepositoryInterface::class));
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

        $this->component->remove($this->createMock(CategoryRepositoryInterface::class));
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

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $this->component->replace($category, $category);
    }

    public function testSortShouldRunSortersSort(): void
    {
        $sorter = $this->createMock(SorterInterface::class);
        $sorter->expects(self::once())->method('sort')->willReturn(PodiumResponse::success());
        $this->component->sorterConfig = $sorter;
        $this->component->repositoryConfig = $this->createMock(CategoryRepositoryInterface::class);

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

        $this->component->archive($this->createMock(CategoryRepositoryInterface::class));
    }

    public function testReviveShouldRunArchiversRevive(): void
    {
        $archiver = $this->createMock(ArchiverInterface::class);
        $archiver->expects(self::once())->method('revive')->willReturn(PodiumResponse::success());
        $this->component->archiverConfig = $archiver;

        $this->component->revive($this->createMock(CategoryRepositoryInterface::class));
    }
}
