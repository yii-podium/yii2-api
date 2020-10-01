<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Category;

use Exception;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Category\CategoryArchiver;
use Podium\Tests\AppTestCase;

use function count;

class CategoryArchiverTest extends AppTestCase
{
    private CategoryArchiver $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CategoryArchiver();
    }

    public function testArchiveShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->archive($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Category must be instance of Podium\Api\Interfaces\CategoryRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testArchiveShouldReturnErrorWhenArchivingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('isArchived')->willReturn(false);
        $category->method('archive')->willReturn(false);
        $category->method('getErrors')->willReturn([1]);
        $result = $this->service->archive($category);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testArchiveShouldReturnErrorWhenCategoryIsAlreadyArchived(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('isArchived')->willReturn(true);
        $result = $this->service->archive($category);

        self::assertFalse($result->getResult());
        self::assertSame('category.already.archived', $result->getErrors()['api']);
    }

    public function testArchiveShouldReturnSuccessWhenArchivingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('isArchived')->willReturn(false);
        $category->method('archive')->willReturn(true);
        $result = $this->service->archive($category);

        self::assertTrue($result->getResult());
    }

    public function testArchiveShouldReturnErrorWhenArchivingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data)
                        && 'Exception while archiving category' === $data[0]
                        && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('isArchived')->willReturn(false);
        $category->method('archive')->willThrowException(new Exception('exc'));
        $result = $this->service->archive($category);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testReviveShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->revive($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Category must be instance of Podium\Api\Interfaces\CategoryRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testReviveShouldReturnErrorWhenRevivingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('isArchived')->willReturn(true);
        $category->method('revive')->willReturn(false);
        $category->method('getErrors')->willReturn([1]);
        $result = $this->service->revive($category);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testReviveShouldReturnErrorWhenCategoryIsNotArchived(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('isArchived')->willReturn(false);
        $result = $this->service->revive($category);

        self::assertFalse($result->getResult());
        self::assertSame('category.not.archived', $result->getErrors()['api']);
    }

    public function testReviveShouldReturnSuccessWhenRevivingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('isArchived')->willReturn(true);
        $category->method('revive')->willReturn(true);
        $result = $this->service->revive($category);

        self::assertTrue($result->getResult());
    }

    public function testReviveShouldReturnErrorWhenRevivingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while reviving category' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('isArchived')->willReturn(true);
        $category->method('revive')->willThrowException(new Exception('exc'));
        $result = $this->service->revive($category);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
