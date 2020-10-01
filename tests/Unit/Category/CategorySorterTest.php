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

    public function testReplaceShouldReturnSuccessWhenReplacingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('getOrder')->willReturn(1);
        $category->method('setOrder')->willReturn(true);
        $result = $this->service->replace($category, $category);

        self::assertTrue($result->getResult());
    }

    public function testReplaceShouldReturnErrorWhenReplacingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while replacing categories order' === $data[0]
                        && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('getOrder')->willReturn(1);
        $category->method('setOrder')->willThrowException(new Exception('exc'));
        $result = $this->service->replace($category, $category);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testReplaceShouldReturnErrorWhenSettingFirstCategoryOrderErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while replacing categories order' === $data[0]
                        && 'Error while setting new category order!' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $category1 = $this->createMock(CategoryRepositoryInterface::class);
        $category1->method('getOrder')->willReturn(1);
        $category1->method('setOrder')->willReturn(false);
        $category2 = $this->createMock(CategoryRepositoryInterface::class);
        $category2->method('getOrder')->willReturn(2);
        $category2->method('setOrder')->willReturn(true);
        $result = $this->service->replace($category1, $category2);

        self::assertFalse($result->getResult());
        self::assertSame('Error while setting new category order!', $result->getErrors()['exception']->getMessage());
    }

    public function testReplaceShouldReturnErrorWhenSettingSecondCategoryOrderErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while replacing categories order' === $data[0]
                        && 'Error while setting new category order!' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $category1 = $this->createMock(CategoryRepositoryInterface::class);
        $category1->method('getOrder')->willReturn(1);
        $category1->method('setOrder')->willReturn(true);
        $category2 = $this->createMock(CategoryRepositoryInterface::class);
        $category2->method('getOrder')->willReturn(2);
        $category2->method('setOrder')->willReturn(false);
        $result = $this->service->replace($category1, $category2);

        self::assertFalse($result->getResult());
        self::assertSame('Error while setting new category order!', $result->getErrors()['exception']->getMessage());
    }

    public function testSortShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->sort($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testSortShouldReturnErrorWhenSortingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('sort')->willReturn(false);
        $result = $this->service->sort($category);

        self::assertFalse($result->getResult());
    }

    public function testSortShouldReturnSuccessWhenSortingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('sort')->willReturn(true);
        $result = $this->service->sort($category);

        self::assertTrue($result->getResult());
    }

    public function testSortShouldReturnErrorWhenSortingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while sorting categories' === $data[0]
                        && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('sort')->willThrowException(new Exception('exc'));
        $result = $this->service->sort($category);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
