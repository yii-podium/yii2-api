<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Category;

use Exception;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Category\CategoryHider;
use Podium\Tests\AppTestCase;

use function count;

class CategoryHiderTest extends AppTestCase
{
    private CategoryHider $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CategoryHider();
    }

    public function testHideShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->hide($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Category must be instance of Podium\Api\Interfaces\CategoryRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testHideShouldReturnErrorWhenHidingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('isHidden')->willReturn(false);
        $category->method('hide')->willReturn(false);
        $category->method('getErrors')->willReturn([1]);
        $result = $this->service->hide($category);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testHideShouldReturnErrorWhenCategoryIsAlreadyHidden(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('isHidden')->willReturn(true);
        $result = $this->service->hide($category);

        self::assertFalse($result->getResult());
        self::assertSame('category.already.hidden', $result->getErrors()['api']);
    }

    public function testHideShouldReturnSuccessWhenHidingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('isHidden')->willReturn(false);
        $category->method('hide')->willReturn(true);
        $result = $this->service->hide($category);

        self::assertTrue($result->getResult());
    }

    public function testHideShouldReturnErrorWhenHidingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while hiding category' === $data[0]
                        && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('isHidden')->willReturn(false);
        $category->method('hide')->willThrowException(new Exception('exc'));
        $result = $this->service->hide($category);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testRevealShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->reveal($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Category must be instance of Podium\Api\Interfaces\CategoryRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testRevealShouldReturnErrorWhenRevealingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('isHidden')->willReturn(true);
        $category->method('reveal')->willReturn(false);
        $category->method('getErrors')->willReturn([1]);
        $result = $this->service->reveal($category);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testRevealShouldReturnErrorWhenCategoryIsNotHidden(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('isHidden')->willReturn(false);
        $result = $this->service->reveal($category);

        self::assertFalse($result->getResult());
        self::assertSame('category.not.hidden', $result->getErrors()['api']);
    }

    public function testRevealShouldReturnSuccessWhenRevealingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('isHidden')->willReturn(true);
        $category->method('reveal')->willReturn(true);
        $result = $this->service->reveal($category);

        self::assertTrue($result->getResult());
    }

    public function testRevealShouldReturnErrorWhenRevealingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while revealing category' === $data[0]
                        && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('isHidden')->willReturn(true);
        $category->method('reveal')->willThrowException(new Exception('exc'));
        $result = $this->service->reveal($category);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
