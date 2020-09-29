<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Post;

use Exception;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Post\PostArchiver;
use Podium\Tests\AppTestCase;

class PostArchiverTest extends AppTestCase
{
    private PostArchiver $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PostArchiver();
    }

    public function testBeforeArchiveShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeArchive());
    }

    public function testArchiveShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->archive($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testArchiveShouldReturnErrorWhenArchivingErrored(): void
    {
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('isArchived')->willReturn(false);
        $post->method('archive')->willReturn(false);
        $post->method('getErrors')->willReturn([1]);
        $result = $this->service->archive($post);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testArchiveShouldReturnErrorWhenPostIsAlreadyArchived(): void
    {
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('isArchived')->willReturn(true);
        $result = $this->service->archive($post);

        self::assertFalse($result->getResult());
        self::assertSame('post.already.archived', $result->getErrors()['api']);
    }

    public function testArchiveShouldReturnSuccessWhenArchivingIsDone(): void
    {
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('isArchived')->willReturn(false);
        $post->method('archive')->willReturn(true);
        $result = $this->service->archive($post);

        self::assertTrue($result->getResult());
    }

    public function testArchiveShouldReturnErrorWhenArchivingThrowsException(): void
    {
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('isArchived')->willReturn(false);
        $post->method('archive')->willThrowException(new Exception('exc'));
        $result = $this->service->archive($post);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testBeforeReviveShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeRevive());
    }

    public function testReviveShouldReturnErrorWhenRepositoryIsWrong(): void
    {
        $result = $this->service->revive($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testReviveShouldReturnErrorWhenRevivingErrored(): void
    {
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('isArchived')->willReturn(true);
        $post->method('revive')->willReturn(false);
        $post->method('getErrors')->willReturn([1]);
        $result = $this->service->revive($post);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testReviveShouldReturnErrorWhenPostIsNotArchived(): void
    {
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('isArchived')->willReturn(false);
        $result = $this->service->revive($post);

        self::assertFalse($result->getResult());
        self::assertSame('post.not.archived', $result->getErrors()['api']);
    }

    public function testReviveShouldReturnSuccessWhenRevivingIsDone(): void
    {
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('isArchived')->willReturn(true);
        $post->method('revive')->willReturn(true);
        $result = $this->service->revive($post);

        self::assertTrue($result->getResult());
    }

    public function testReviveShouldReturnErrorWhenRevivingThrowsException(): void
    {
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('isArchived')->willReturn(true);
        $post->method('revive')->willThrowException(new Exception('exc'));
        $result = $this->service->revive($post);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
