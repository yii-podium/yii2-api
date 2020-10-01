<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Post;

use Exception;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\Post\PostPinner;
use Podium\Tests\AppTestCase;

class PostPinnerTest extends AppTestCase
{
    private PostPinner $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PostPinner();
    }

    public function testPinShouldReturnErrorWhenPostRepositoryIsWrong(): void
    {
        $result = $this->service->pin($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Post must be instance of Podium\Api\Interfaces\PostRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testPinShouldReturnErrorWhenPinningErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('pin')->willReturn(false);
        $post->method('getErrors')->willReturn([1]);
        $result = $this->service->pin($post);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testPinShouldReturnSuccessWhenPinningIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('pin')->willReturn(true);
        $result = $this->service->pin($post);

        self::assertTrue($result->getResult());
    }

    public function testPinShouldReturnErrorWhenPinningThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while pinning post' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('pin')->willThrowException(new Exception('exc'));
        $result = $this->service->pin($post);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testUnpinShouldReturnErrorWhenPostRepositoryIsWrong(): void
    {
        $result = $this->service->unpin($this->createMock(RepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame(
            'Post must be instance of Podium\Api\Interfaces\PostRepositoryInterface!',
            $result->getErrors()['exception']->getMessage()
        );
    }

    public function testUnpinShouldReturnErrorWhenUnpinningErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('unpin')->willReturn(false);
        $post->method('getErrors')->willReturn([1]);
        $result = $this->service->unpin($post);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testUnpinShouldReturnSuccessWhenUnpinningIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('unpin')->willReturn(true);
        $result = $this->service->unpin($post);

        self::assertTrue($result->getResult());
    }

    public function testUnpinShouldReturnErrorWhenUnpinningThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while unpinning post' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('unpin')->willThrowException(new Exception('exc'));
        $result = $this->service->unpin($post);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
