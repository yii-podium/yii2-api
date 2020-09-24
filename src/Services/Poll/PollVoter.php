<?php

declare(strict_types=1);

namespace Podium\Api\Services\Poll;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\VoteEvent;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\PollPostRepositoryInterface;
use Podium\Api\Interfaces\PollRepositoryInterface;
use Podium\Api\Interfaces\VoterInterface;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

use function count;

final class PollVoter extends Component implements VoterInterface
{
    public const EVENT_BEFORE_VOTING = 'podium.poll.voting.before';
    public const EVENT_AFTER_VOTING = 'podium.poll.voting.after';

    public function beforeVote(): bool
    {
        $event = new VoteEvent();
        $this->trigger(self::EVENT_BEFORE_VOTING, $event);

        return $event->canVote;
    }

    /**
     * Votes in the poll.
     */
    public function vote(
        PollPostRepositoryInterface $post,
        MemberRepositoryInterface $member,
        array $answers
    ): PodiumResponse {
        $answersCount = count($answers);
        if (0 === $answersCount || !$this->beforeVote()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $poll = $post->getPoll();

            if ($poll->hasMemberVoted($member)) {
                return PodiumResponse::error(['api' => Yii::t('podium.error', 'poll.already.voted')]);
            }
            if ($answersCount > 1 && $poll->isSingleChoice()) {
                return PodiumResponse::error(['api' => Yii::t('podium.error', 'poll.one.vote.allowed')]);
            }
            if (!$poll->vote($member, $answers)) {
                return PodiumResponse::error($post->getErrors());
            }

            $this->afterVote($poll);
            $transaction->commit();

            return PodiumResponse::success();
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while voting in poll', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error();
        }
    }

    public function afterVote(PollRepositoryInterface $poll): void
    {
        $this->trigger(self::EVENT_AFTER_VOTING, new VoteEvent(['repository' => $poll]));
    }
}
