<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Category;

use Exception;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Services\Category\CategoryBuilder;
use Podium\Tests\AppTestCase;

class CategoryBuilderTest extends AppTestCase
{
    private CategoryBuilder $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CategoryBuilder();
    }

    public function testCreateShouldReturnErrorWhenCreatingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(false);
        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('create')->willReturn(false);
        $category->method('getErrors')->willReturn([1]);
        $result = $this->service->create($category, $author);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testCreateShouldReturnErrorWhenAuthorIsBanned(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(true);
        $result = $this->service->create($this->createMock(CategoryRepositoryInterface::class), $author);

        self::assertFalse($result->getResult());
        self::assertSame(['api' => 'member.banned'], $result->getErrors());
    }

    public function testCreateShouldReturnSuccessWhenCreatingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(false);
        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('create')->willReturn(true);
        $result = $this->service->create($category, $author);

        self::assertTrue($result->getResult());
    }

    public function testCreateShouldReturnErrorWhenCreatingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while creating category' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('isBanned')->willReturn(false);
        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('create')->willThrowException(new Exception('exc'));
        $result = $this->service->create($category, $author);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testEditShouldReturnErrorWhenEditingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('edit')->willReturn(false);
        $category->method('getErrors')->willReturn([1]);
        $result = $this->service->edit($category);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testEditShouldReturnSuccessWhenEditingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('edit')->willReturn(true);
        $result = $this->service->edit($category);

        self::assertTrue($result->getResult());
    }

    public function testEditShouldReturnErrorWhenEditingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while editing category' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $category = $this->createMock(CategoryRepositoryInterface::class);
        $category->method('edit')->willThrowException(new Exception('exc'));
        $result = $this->service->edit($category);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
