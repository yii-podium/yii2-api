<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Post;

use Exception;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\ThumbRepositoryInterface;
use Podium\Api\Services\Post\PostLiker;
use Podium\Tests\AppTestCase;

class PostLikerTest extends AppTestCase
{
    private PostLiker $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PostLiker();
    }

    public function testThumbUpShouldReturnErrorWhenUpErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('isUp')->willReturn(false);
        $thumb->method('up')->willReturn(false);
        $thumb->method('getErrors')->willReturn([1]);
        $result = $this->service->thumbUp(
            $thumb,
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testThumbUpShouldReturnErrorWhenIsUpIsTrue(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('isUp')->willReturn(true);
        $result = $this->service->thumbUp(
            $thumb,
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame('post.already.liked', $result->getErrors()['api']);
    }

    public function testThumbUpShouldReturnSuccessWhenUpIsDoneWithAlreadyRated(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('isUp')->willReturn(false);
        $thumb->method('up')->willReturn(true);
        $thumb->expects(self::never())->method('prepare');
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('updateCounters')->with(1, -1)->willReturn(true);
        $result = $this->service->thumbUp($thumb, $post, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($result->getResult());
    }

    public function testThumbUpShouldReturnSuccessWhenUpIsDoneWithNotPreviouslyRated(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('fetchOne')->willReturn(false);
        $thumb->method('isUp')->willReturn(false);
        $thumb->method('up')->willReturn(true);
        $thumb->expects(self::once())->method('prepare');
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('updateCounters')->with(1, 0)->willReturn(true);
        $result = $this->service->thumbUp($thumb, $post, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($result->getResult());
    }

    public function testThumbUpShouldReturnErrorWhenUpThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('isUp')->willReturn(false);
        $thumb->method('up')->willThrowException(new Exception('exc'));
        $result = $this->service->thumbUp(
            $thumb,
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testThumbUpShouldReturnErrorWhenUpdateCountersErroredWhileThumbIsOld(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('isUp')->willReturn(false);
        $thumb->method('up')->willReturn(true);
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('updateCounters')->willReturn(false);
        $result = $this->service->thumbUp($thumb, $post, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('Error while updating post counters!', $result->getErrors()['exception']->getMessage());
    }

    public function testThumbUpShouldReturnErrorWhenUpdateCountersErroredWhileThumbIsNew(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('fetchOne')->willReturn(false);
        $thumb->method('isUp')->willReturn(false);
        $thumb->method('up')->willReturn(true);
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('updateCounters')->willReturn(false);
        $result = $this->service->thumbUp($thumb, $post, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('Error while updating post counters!', $result->getErrors()['exception']->getMessage());
    }

    public function testThumbDownShouldReturnErrorWhenDownErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('isDown')->willReturn(false);
        $thumb->method('down')->willReturn(false);
        $thumb->method('getErrors')->willReturn([1]);
        $result = $this->service->thumbDown(
            $thumb,
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testThumbDownShouldReturnErrorWhenIsDownIsTrue(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('isDown')->willReturn(true);
        $result = $this->service->thumbDown(
            $thumb,
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame('post.already.disliked', $result->getErrors()['api']);
    }

    public function testThumbDownShouldReturnSuccessWhenDownIsDoneWithAlreadyRated(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('isDown')->willReturn(false);
        $thumb->method('down')->willReturn(true);
        $thumb->expects(self::never())->method('prepare');
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('updateCounters')->with(-1, 1)->willReturn(true);
        $result = $this->service->thumbDown($thumb, $post, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($result->getResult());
    }

    public function testThumbDownShouldReturnSuccessWhenDownIsDoneWithNotPreviouslyRated(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('fetchOne')->willReturn(false);
        $thumb->method('isDown')->willReturn(false);
        $thumb->method('down')->willReturn(true);
        $thumb->expects(self::once())->method('prepare');
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('updateCounters')->with(0, 1)->willReturn(true);
        $result = $this->service->thumbDown($thumb, $post, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($result->getResult());
    }

    public function testThumbDownShouldReturnErrorWhenDownThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('isDown')->willReturn(false);
        $thumb->method('down')->willThrowException(new Exception('exc'));
        $result = $this->service->thumbDown(
            $thumb,
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testThumbDownShouldReturnErrorWhenUpdateCountersErroredWhileThumbIsOld(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('isDown')->willReturn(false);
        $thumb->method('down')->willReturn(true);
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('updateCounters')->willReturn(false);
        $result = $this->service->thumbDown($thumb, $post, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('Error while updating post counters!', $result->getErrors()['exception']->getMessage());
    }

    public function testThumbDownShouldReturnErrorWhenUpdateCountersErroredWhileThumbIsNew(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('fetchOne')->willReturn(false);
        $thumb->method('isDown')->willReturn(false);
        $thumb->method('down')->willReturn(true);
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('updateCounters')->willReturn(false);
        $result = $this->service->thumbDown($thumb, $post, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('Error while updating post counters!', $result->getErrors()['exception']->getMessage());
    }

    public function testThumbResetShouldReturnErrorWhenResetErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('reset')->willReturn(false);
        $result = $this->service->thumbReset(
            $thumb,
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
    }

    public function testThumbResetShouldReturnErrorWhenPostIsNotRated(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('fetchOne')->willReturn(false);
        $result = $this->service->thumbReset(
            $thumb,
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame('post.not.rated', $result->getErrors()['api']);
    }

    public function testThumbResetShouldReturnSuccessWhenResetIsDoneWithPostPreviouslyUp(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('reset')->willReturn(true);
        $thumb->method('isUp')->willReturn(true);
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('updateCounters')->with(-1, 0)->willReturn(true);
        $result = $this->service->thumbReset($thumb, $post, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($result->getResult());
    }

    public function testThumbResetShouldReturnSuccessWhenResetIsDoneWithPostPreviouslyDown(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('reset')->willReturn(true);
        $thumb->method('isUp')->willReturn(false);
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('updateCounters')->with(0, -1)->willReturn(true);
        $result = $this->service->thumbReset($thumb, $post, $this->createMock(MemberRepositoryInterface::class));

        self::assertTrue($result->getResult());
    }

    public function testThumbResetShouldReturnErrorWhenResetThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('reset')->willThrowException(new Exception('exc'));
        $result = $this->service->thumbReset(
            $thumb,
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testThumbResetShouldReturnErrorWhenUpdateCountersErroredWhileThumbWasUp(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('reset')->willReturn(true);
        $thumb->method('isUp')->willReturn(true);
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('updateCounters')->willReturn(false);
        $result = $this->service->thumbReset($thumb, $post, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('Error while updating post counters!', $result->getErrors()['exception']->getMessage());
    }

    public function testThumbResetShouldReturnErrorWhenUpdateCountersErroredWhileThumbWasDown(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $thumb = $this->createMock(ThumbRepositoryInterface::class);
        $thumb->method('fetchOne')->willReturn(true);
        $thumb->method('reset')->willReturn(true);
        $thumb->method('isUp')->willReturn(false);
        $post = $this->createMock(PostRepositoryInterface::class);
        $post->method('updateCounters')->willReturn(false);
        $result = $this->service->thumbReset($thumb, $post, $this->createMock(MemberRepositoryInterface::class));

        self::assertFalse($result->getResult());
        self::assertSame('Error while updating post counters!', $result->getErrors()['exception']->getMessage());
    }
}
