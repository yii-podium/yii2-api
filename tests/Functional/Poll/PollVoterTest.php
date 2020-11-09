<?php

declare(strict_types=1);

namespace Podium\Tests\Functional\Poll;

use Podium\Api\Events\VoteEvent;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\PollPostRepositoryInterface;
use Podium\Api\Services\Poll\PollVoter;
use Podium\Tests\AppTestCase;
use yii\base\Event;

class PollVoterTest extends AppTestCase
{
    private PollVoter $service;

    private array $eventsRaised;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PollVoter();
        $this->eventsRaised = [];
    }

    public function testVoteShouldTriggerBeforeAndAfterEventsWhenVotingIsDone(): void
    {
        $beforeHandler = function ($event) {
            $this->eventsRaised[PollVoter::EVENT_BEFORE_VOTING] = $event instanceof VoteEvent;
        };
        Event::on(PollVoter::class, PollVoter::EVENT_BEFORE_VOTING, $beforeHandler);
        $afterHandler = function ($event) {
            $this->eventsRaised[PollVoter::EVENT_AFTER_VOTING] = $event instanceof VoteEvent
                && 9 === $event->repository->getId();
        };
        Event::on(PollVoter::class, PollVoter::EVENT_AFTER_VOTING, $afterHandler);

        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('votePoll')->willReturn(true);
        $post->method('hasMemberPollVoted')->willReturn(false);
        $post->method('getId')->willReturn(9);
        $post->method('arePollAnswersAcceptable')->willReturn(true);
        $this->service->vote($post, $this->createMock(MemberRepositoryInterface::class), [1]);

        self::assertTrue($this->eventsRaised[PollVoter::EVENT_BEFORE_VOTING]);
        self::assertTrue($this->eventsRaised[PollVoter::EVENT_AFTER_VOTING]);

        Event::off(PollVoter::class, PollVoter::EVENT_BEFORE_VOTING, $beforeHandler);
        Event::off(PollVoter::class, PollVoter::EVENT_AFTER_VOTING, $afterHandler);
    }

    public function testVoteShouldOnlyTriggerBeforeEventWhenVotingErrored(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[PollVoter::EVENT_BEFORE_VOTING] = true;
        };
        Event::on(PollVoter::class, PollVoter::EVENT_BEFORE_VOTING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[PollVoter::EVENT_AFTER_VOTING] = true;
        };
        Event::on(PollVoter::class, PollVoter::EVENT_AFTER_VOTING, $afterHandler);

        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('votePoll')->willReturn(false);
        $post->method('hasMemberPollVoted')->willReturn(false);
        $post->method('arePollAnswersAcceptable')->willReturn(true);
        $this->service->vote($post, $this->createMock(MemberRepositoryInterface::class), [1]);

        self::assertTrue($this->eventsRaised[PollVoter::EVENT_BEFORE_VOTING]);
        self::assertArrayNotHasKey(PollVoter::EVENT_AFTER_VOTING, $this->eventsRaised);

        Event::off(PollVoter::class, PollVoter::EVENT_BEFORE_VOTING, $beforeHandler);
        Event::off(PollVoter::class, PollVoter::EVENT_AFTER_VOTING, $afterHandler);
    }

    public function testVoteShouldOnlyTriggerBeforeEventWhenMemberAlreadyVoted(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[PollVoter::EVENT_BEFORE_VOTING] = true;
        };
        Event::on(PollVoter::class, PollVoter::EVENT_BEFORE_VOTING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[PollVoter::EVENT_AFTER_VOTING] = true;
        };
        Event::on(PollVoter::class, PollVoter::EVENT_AFTER_VOTING, $afterHandler);

        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('hasMemberPollVoted')->willReturn(true);
        $this->service->vote($post, $this->createMock(MemberRepositoryInterface::class), [1]);

        self::assertTrue($this->eventsRaised[PollVoter::EVENT_BEFORE_VOTING]);
        self::assertArrayNotHasKey(PollVoter::EVENT_AFTER_VOTING, $this->eventsRaised);

        Event::off(PollVoter::class, PollVoter::EVENT_BEFORE_VOTING, $beforeHandler);
        Event::off(PollVoter::class, PollVoter::EVENT_AFTER_VOTING, $afterHandler);
    }

    public function testVoteShouldOnlyTriggerBeforeEventWhenPollIsSingleChoiceAndTwoAnswersAreGiven(): void
    {
        $beforeHandler = function () {
            $this->eventsRaised[PollVoter::EVENT_BEFORE_VOTING] = true;
        };
        Event::on(PollVoter::class, PollVoter::EVENT_BEFORE_VOTING, $beforeHandler);
        $afterHandler = function () {
            $this->eventsRaised[PollVoter::EVENT_AFTER_VOTING] = true;
        };
        Event::on(PollVoter::class, PollVoter::EVENT_AFTER_VOTING, $afterHandler);

        $post = $this->createMock(PollPostRepositoryInterface::class);
        $post->method('hasMemberPollVoted')->willReturn(false);
        $post->method('isPollSingleChoice')->willReturn(true);
        $this->service->vote($post, $this->createMock(MemberRepositoryInterface::class), [1, 2]);

        self::assertTrue($this->eventsRaised[PollVoter::EVENT_BEFORE_VOTING]);
        self::assertArrayNotHasKey(PollVoter::EVENT_AFTER_VOTING, $this->eventsRaised);

        Event::off(PollVoter::class, PollVoter::EVENT_BEFORE_VOTING, $beforeHandler);
        Event::off(PollVoter::class, PollVoter::EVENT_AFTER_VOTING, $afterHandler);
    }

    public function testVoteShouldReturnErrorWhenEventPreventsVoting(): void
    {
        $handler = static function (VoteEvent $event) {
            $event->canVote = false;
        };
        Event::on(PollVoter::class, PollVoter::EVENT_BEFORE_VOTING, $handler);

        $result = $this->service->vote(
            $this->createMock(PollPostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class),
            []
        );
        self::assertFalse($result->getResult());
        self::assertEmpty($result->getErrors());

        Event::off(PollVoter::class, PollVoter::EVENT_BEFORE_VOTING, $handler);
    }
}
