<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Poll;

use Exception;
use Podium\Api\Interfaces\PollPostRepositoryInterface;
use Podium\Api\Services\Poll\PollBuilder;
use Podium\Tests\AppTestCase;

class PollBuilderTest extends AppTestCase
{
    private PollBuilder $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PollBuilder();
    }

    public function testCreateShouldReturnErrorWhenCreatingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('addPoll')->willReturn(false);
        $post->method('getErrors')->willReturn([1]);
        $result = $this->service->create($post, []);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testCreateShouldReturnSuccessWhenCreatingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('addPoll')->willReturn(true);
        $result = $this->service->create($post, []);

        self::assertTrue($result->getResult());
    }

    public function testCreateShouldReturnErrorWhenCreatingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while creating poll' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('addPoll')->willThrowException(new Exception('exc'));
        $result = $this->service->create($post, []);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }

    public function testEditShouldReturnErrorWhenEditingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('editPoll')->willReturn(false);
        $post->method('getErrors')->willReturn([1]);
        $result = $this->service->edit($post);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testEditShouldReturnSuccessWhenEditingIsDone(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('editPoll')->willReturn(true);
        $result = $this->service->edit($post);

        self::assertTrue($result->getResult());
    }

    public function testEditShouldReturnErrorWhenEditingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while editing poll' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('editPoll')->willThrowException(new Exception('exc'));
        $result = $this->service->edit($post);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
