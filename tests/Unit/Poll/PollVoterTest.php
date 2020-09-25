<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Poll;

use Exception;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\PollPostRepositoryInterface;
use Podium\Api\Interfaces\PollRepositoryInterface;
use Podium\Api\Services\Poll\PollVoter;
use Podium\Tests\AppTestCase;
use Yii;
use yii\db\Connection;
use yii\db\Transaction;

class PollVoterTest extends AppTestCase
{
    private PollVoter $service;

    protected function setUp(): void
    {
        $this->service = new PollVoter();
        $connection = $this->createMock(Connection::class);
        $connection->method('beginTransaction')->willReturn($this->createMock(Transaction::class));
        Yii::$app->set('db', $connection);
    }

    public function testBeforeRemoveShouldReturnTrue(): void
    {
        self::assertTrue($this->service->beforeVote());
    }

    public function testVoteShouldReturnErrorWhenVotingErrored(): void
    {
        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('hasMemberVoted')->willReturn(false);
        $poll->method('vote')->willReturn(false);
        $poll->method('getErrors')->willReturn([1]);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->vote($post, $this->createMock(MemberRepositoryInterface::class), [1]);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testVoteShouldReturnErrorWhenAnswersAreEmpty(): void
    {
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($this->createMock(PollRepositoryInterface::class));
        $result = $this->service->vote($post, $this->createMock(MemberRepositoryInterface::class), []);

        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());
    }

    public function testVoteShouldReturnErrorWhenMemberAlreadyVoted(): void
    {
        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('hasMemberVoted')->willReturn(true);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->vote($post, $this->createMock(MemberRepositoryInterface::class), [1]);

        self::assertFalse($result->getResult());
        self::assertSame('poll.already.voted', $result->getErrors()['api']);
    }

    public function testVoteShouldReturnErrorWhenPollIsSingleChoiceAndThereAreMoreThanOneAnswers(): void
    {
        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('hasMemberVoted')->willReturn(false);
        $poll->method('isSingleChoice')->willReturn(true);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->vote($post, $this->createMock(MemberRepositoryInterface::class), [1, 2]);

        self::assertFalse($result->getResult());
        self::assertSame('poll.one.vote.allowed', $result->getErrors()['api']);
    }

    public function testVoteShouldReturnSuccessWhenVotingIsDoneOnSingleChoicePoll(): void
    {
        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('vote')->willReturn(true);
        $poll->method('hasMemberVoted')->willReturn(false);
        $poll->method('isSingleChoice')->willReturn(true);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->vote($post, $this->createMock(MemberRepositoryInterface::class), [1]);

        self::assertTrue($result->getResult());
    }

    public function testVoteShouldReturnSuccessWhenVotingIsDoneOnMultipleChoicePollWithOneAnswer(): void
    {
        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('vote')->willReturn(true);
        $poll->method('hasMemberVoted')->willReturn(false);
        $poll->method('isSingleChoice')->willReturn(false);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->vote($post, $this->createMock(MemberRepositoryInterface::class), [1]);

        self::assertTrue($result->getResult());
    }

    public function testVoteShouldReturnSuccessWhenVotingIsDoneOnMultipleChoicePollWithTwoAnswers(): void
    {
        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('vote')->willReturn(true);
        $poll->method('hasMemberVoted')->willReturn(false);
        $poll->method('isSingleChoice')->willReturn(false);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->vote($post, $this->createMock(MemberRepositoryInterface::class), [1, 2]);

        self::assertTrue($result->getResult());
    }

    public function testVoteShouldReturnErrorWhenVotingThrowsException(): void
    {
        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('vote')->willThrowException(new Exception('exc'));
        $poll->method('hasMemberVoted')->willReturn(false);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->vote($post, $this->createMock(MemberRepositoryInterface::class), [1]);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
