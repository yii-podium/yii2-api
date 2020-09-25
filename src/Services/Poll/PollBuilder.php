<?php

declare(strict_types=1);

namespace Podium\Api\Services\Poll;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\BuildEvent;
use Podium\Api\Interfaces\PollBuilderInterface;
use Podium\Api\Interfaces\PollPostRepositoryInterface;
use Podium\Api\Interfaces\PollRepositoryInterface;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class PollBuilder extends Component implements PollBuilderInterface
{
    public const EVENT_BEFORE_CREATING = 'podium.poll.creating.before';
    public const EVENT_AFTER_CREATING = 'podium.poll.creating.after';
    public const EVENT_BEFORE_EDITING = 'podium.poll.editing.before';
    public const EVENT_AFTER_EDITING = 'podium.poll.editing.after';

    public function beforeCreate(): bool
    {
        $event = new BuildEvent();
        $this->trigger(self::EVENT_BEFORE_CREATING, $event);

        return $event->canCreate;
    }

    /**
     * Creates new poll.
     */
    public function create(PollPostRepositoryInterface $post, array $answers, array $data = []): PodiumResponse
    {
        if (!$this->beforeCreate()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $poll = $post->getPoll();
            if (!$poll->create($answers, $data)) {
                return PodiumResponse::error($poll->getErrors());
            }

            $this->afterCreate($poll);
            $transaction->commit();

            return PodiumResponse::success();
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while creating poll', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }
    }

    public function afterCreate(PollRepositoryInterface $poll): void
    {
        $this->trigger(self::EVENT_AFTER_CREATING, new BuildEvent(['repository' => $poll]));
    }

    public function beforeEdit(): bool
    {
        $event = new BuildEvent();
        $this->trigger(self::EVENT_BEFORE_EDITING, $event);

        return $event->canEdit;
    }

    /**
     * Edits the thread.
     */
    public function edit(PollPostRepositoryInterface $post, array $answers = [], array $data = []): PodiumResponse
    {
        if (!$this->beforeEdit()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $poll = $post->getPoll();
            if (!$poll->edit($answers, $data)) {
                return PodiumResponse::error($poll->getErrors());
            }

            $this->afterEdit($poll);
            $transaction->commit();

            return PodiumResponse::success();
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while editing poll', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }
    }

    public function afterEdit(PollRepositoryInterface $poll): void
    {
        $this->trigger(self::EVENT_AFTER_EDITING, new BuildEvent(['repository' => $poll]));
    }
}
