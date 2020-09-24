<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Category;

use Exception;
use PHPUnit\Framework\TestCase;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Services\Category\CategoryBuilder;

class CategoryBuilderTest extends TestCase
{
    private CategoryBuilder $service;

    protected function setUp(): void
    {
        $this->service = new CategoryBuilder();
    }

    public function testBeforeCreateShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeCreate());
    }

    public function testCreateShouldReturnErrorWhenCreatingErrored(): void
    {
        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('create')->willReturn(false);
        $category->method('getErrors')->willReturn([1]);
        $result = $this->service->create($category, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testCreateShouldReturnSuccessWhenCreatingIsDone(): void
    {
        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('create')->willReturn(true);
        $result = $this->service->create($category, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($result->getResult());
    }

    public function testCreateShouldReturnErrorWhenCreatingThrowsException(): void
    {
        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('create')->willThrowException(new Exception('exc'));
        $result = $this->service->create($category, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testBeforeEditShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeEdit());
    }

    public function testEditShouldReturnErrorWhenEditingErrored(): void
    {
        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('edit')->willReturn(false);
        $category->method('getErrors')->willReturn([1]);
        $result = $this->service->edit($category);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testEditShouldReturnSuccessWhenEditingIsDone(): void
    {
        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('edit')->willReturn(true);
        $result = $this->service->edit($category);

        self::assertTrue($result->getResult());
    }

    public function testEditShouldReturnErrorWhenEditingThrowsException(): void
    {
        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('edit')->willThrowException(new Exception('exc'));
        $result = $this->service->edit($category);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
