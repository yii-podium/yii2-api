<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Poll;

use Exception;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\PollPostRepositoryInterface;
use Podium\Api\Interfaces\PollRepositoryInterface;
use Podium\Api\Services\Poll\PollVoter;
use Podium\Tests\AppTestCase;

class PollVoterTest extends AppTestCase
{
    private PollVoter $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PollVoter();
    }

    public function testVoteShouldReturnErrorWhenVotingErrored(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('hasMemberVoted')->willReturn(false);
        $poll->method('vote')->willReturn(false);
        $poll->method('getErrors')->willReturn([1]);
        $poll->method('areAnswersAcceptable')->willReturn(true);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->vote($post, $member, [1]);

        self::assertFalse($result->getResult());
        self::assertSame([1], $result->getErrors());
    }

    public function testVoteShouldReturnErrorWhenMemberIsBanned(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(true);
        $result = $this->service->vote($this->createMock(PollPostRepositoryInterface::class), $member, [1]);

        self::assertFalse($result->getResult());
        self::assertSame(['api' => 'member.banned'], $result->getErrors());
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
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('hasMemberVoted')->willReturn(true);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->vote($post, $member, [1]);

        self::assertFalse($result->getResult());
        self::assertSame('poll.already.voted', $result->getErrors()['api']);
    }

    public function testVoteShouldReturnErrorWhenPollIsSingleChoiceAndThereAreMoreThanOneAnswers(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('hasMemberVoted')->willReturn(false);
        $poll->method('isSingleChoice')->willReturn(true);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->vote($post, $member, [1, 2]);

        self::assertFalse($result->getResult());
        self::assertSame('poll.one.vote.allowed', $result->getErrors()['api']);
    }

    public function testVoteShouldReturnErrorWhenAnswersAreNotAcceptable(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('hasMemberVoted')->willReturn(false);
        $poll->method('getErrors')->willReturn([1]);
        $poll->method('areAnswersAcceptable')->willReturn(false);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->vote($post, $member, [1]);

        self::assertFalse($result->getResult());
        self::assertSame('poll.wrong.answer', $result->getErrors()['api']);
    }

    public function testVoteShouldReturnSuccessWhenVotingIsDoneOnSingleChoicePoll(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('vote')->willReturn(true);
        $poll->method('hasMemberVoted')->willReturn(false);
        $poll->method('isSingleChoice')->willReturn(true);
        $poll->method('areAnswersAcceptable')->willReturn(true);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->vote($post, $member, [1]);

        self::assertTrue($result->getResult());
    }

    public function testVoteShouldReturnSuccessWhenVotingIsDoneOnMultipleChoicePollWithOneAnswer(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('vote')->willReturn(true);
        $poll->method('hasMemberVoted')->willReturn(false);
        $poll->method('isSingleChoice')->willReturn(false);
        $poll->method('areAnswersAcceptable')->willReturn(true);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->vote($post, $member, [1]);

        self::assertTrue($result->getResult());
    }

    public function testVoteShouldReturnSuccessWhenVotingIsDoneOnMultipleChoicePollWithTwoAnswers(): void
    {
        $this->transaction->expects(self::once())->method('commit');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('vote')->willReturn(true);
        $poll->method('hasMemberVoted')->willReturn(false);
        $poll->method('isSingleChoice')->willReturn(false);
        $poll->method('areAnswersAcceptable')->willReturn(true);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->vote($post, $member, [1, 2]);

        self::assertTrue($result->getResult());
    }

    public function testVoteShouldReturnErrorWhenVotingThrowsException(): void
    {
        $this->transaction->expects(self::once())->method('rollBack');
        $this->logger->expects(self::once())->method('log')->with(
            self::callback(
                static function (array $data) {
                    return 3 === count($data) && 'Exception while voting in poll' === $data[0] && 'exc' === $data[1];
                }
            ),
            1,
            'podium'
        );

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('isBanned')->willReturn(false);
        $poll = $this->createMock(PollRepositoryInterface::class);
        $poll->method('vote')->willThrowException(new Exception('exc'));
        $poll->method('hasMemberVoted')->willReturn(false);
        $poll->method('areAnswersAcceptable')->willReturn(true);
        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('getPoll')->willReturn($poll);
        $result = $this->service->vote($post, $member, [1]);

        self::assertFalse($result->getResult());
        self::assertSame('exc', $result->getErrors()['exception']->getMessage());
    }
}
