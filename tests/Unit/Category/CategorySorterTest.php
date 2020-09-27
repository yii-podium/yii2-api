<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Category;

use Exception;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Category\CategorySorter;
use Podium\Tests\AppTestCase;

class CategorySorterTest extends AppTestCase
{
    private CategorySorter $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CategorySorter();
    }

    public function testBeforeReplaceShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeReplace());
    }

    public function testReplaceShouldReturnErrorWhenFirstRepositoryIsWrong(): void
    {
        $result = $this->service->replace(
            $this->createMock(RepositoryInterface::class),
            $this->createMock(CategoryRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testReplaceShouldReturnErrorWhenSecondRepositoryIsWrong(): void
    {
        $result = $this->service->replace(
            $this->createMock(CategoryRepositoryInterface::class),
            $this->createMock(RepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testReplaceShouldReturnErrorWhenSettingFirstOrderErrored(): void
    {
        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('getOrder')->willReturn(1);
        $category->method('setOrder')->willReturn(false);
        $result = $this->service->replace($category, $category);

        self::assertFalse($result->getResult());
    }

    public function testReplaceShouldReturnErrorWhenSettingSecondOrderErrored(): void
    {
        $category1 = $this->createMock(CategoryRepositoryInterface::class);
        $category1->method('getOrder')->willReturn(1);
        $category1->method('setOrder')->willReturn(true);
        $category2 = $this->createMock(CategoryRepositoryInterface::class);
        $category2->method('getOrder')->willReturn(2);
        $category2->method('setOrder')->willReturn(false);
        $result = $this->service->replace($category1, $category2);

        self::assertFalse($result->getResult());
    }

    public function testReplaceShouldReturnSuccessWhenReplacingIsDone(): void
    {
        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('getOrder')->willReturn(1);
        $category->method('setOrder')->willReturn(true);
        $result = $this->service->replace($category, $category);

        self::assertTrue($result->getResult());
    }

    public function testReplaceShouldReturnErrorWhenReplacingThrowsException(): void
    {
        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('getOrder')->willReturn(1);
        $category->method('setOrder')->willThrowException(new Exception('exc'));
        $result = $this->service->replace($category, $category);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testBeforeSortShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeSort());
    }

    public function testSortShouldReturnErrorWhenSortingErrored(): void
    {
        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('sort')->willReturn(false);
        $result = $this->service->sort($category);

        self::assertFalse($result->getResult());
    }

    public function testSortShouldReturnSuccessWhenSortingIsDone(): void
    {
        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('sort')->willReturn(true);
        $result = $this->service->sort($category);

        self::assertTrue($result->getResult());
    }

    public function testSortShouldReturnErrorWhenSortingThrowsException(): void
    {
        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('sort')->willThrowException(new Exception('exc'));
        $result = $this->service->sort($category);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
