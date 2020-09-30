<?php

declare(strict_types=1);

namespace Podium\Api\Services\Thread;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\BuildEvent;
use Podium\Api\Interfaces\CategorisedBuilderInterface;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Exception;
use yii\db\Transaction;

final class ThreadBuilder extends Component implements CategorisedBuilderInterface
{
    public const EVENT_BEFORE_CREATING = 'podium.thread.creating.before';
    public const EVENT_AFTER_CREATING = 'podium.thread.creating.after';
    public const EVENT_BEFORE_EDITING = 'podium.thread.editing.before';
    public const EVENT_AFTER_EDITING = 'podium.thread.editing.after';

    /**
     * Calls before creating the thread.
     */
    private function beforeCreate(): bool
    {
        $event = new BuildEvent();
        $this->trigger(self::EVENT_BEFORE_CREATING, $event);

        return $event->canCreate;
    }

    /**
     * Creates new thread.
     */
    public function create(
        RepositoryInterface $thread,
        MemberRepositoryInterface $author,
        RepositoryInterface $forum,
        array $data = []
    ): PodiumResponse {
        if (
            !$thread instanceof ThreadRepositoryInterface
            || !$forum instanceof ForumRepositoryInterface
            || !$this->beforeCreate()
        ) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$thread->create($author, $forum, $data)) {
                throw new ServiceException($thread->getErrors());
            }

            if (!$forum->updateCounters(1, 0)) {
                throw new Exception('Error while updating forum counters!');
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while creating thread', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterCreate($thread);

        return PodiumResponse::success();
    }

    /**
     * Calls after creating the thread successfully.
     */
    private function afterCreate(ThreadRepositoryInterface $thread): void
    {
        $this->trigger(self::EVENT_AFTER_CREATING, new BuildEvent(['repository' => $thread]));
    }

    /**
     * Calls before editing the thread.
     */
    private function beforeEdit(): bool
    {
        $event = new BuildEvent();
        $this->trigger(self::EVENT_BEFORE_EDITING, $event);

        return $event->canEdit;
    }

    /**
     * Edits the thread.
     */
    public function edit(RepositoryInterface $thread, array $data = []): PodiumResponse
    {
        if (!$thread instanceof ThreadRepositoryInterface || !$this->beforeEdit()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$thread->edit($data)) {
                throw new ServiceException($thread->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while editing thread', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterEdit($thread);

        return PodiumResponse::success();
    }

    /**
     * Calls after editing the thread successfully.
     */
    private function afterEdit(ThreadRepositoryInterface $thread): void
    {
        $this->trigger(self::EVENT_AFTER_EDITING, new BuildEvent(['repository' => $thread]));
    }
}
