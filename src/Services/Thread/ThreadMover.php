<?php

declare(strict_types=1);

namespace Podium\Api\Services\Thread;

use InvalidArgumentException;
use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\MoveEvent;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\MoverInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Exception;
use yii\db\Transaction;

final class ThreadMover extends Component implements MoverInterface
{
    public const EVENT_BEFORE_MOVING = 'podium.thread.moving.before';
    public const EVENT_AFTER_MOVING = 'podium.thread.moving.after';

    /**
     * Calls before moving the thread.
     */
    private function beforeMove(): bool
    {
        $event = new MoveEvent();
        $this->trigger(self::EVENT_BEFORE_MOVING, $event);

        return $event->canMove;
    }

    /**
     * Moves the thread to another forum.
     */
    public function move(RepositoryInterface $thread, RepositoryInterface $forum): PodiumResponse
    {
        if (!$thread instanceof ThreadRepositoryInterface) {
            return PodiumResponse::error(
                [
                    'exception' => new InvalidArgumentException(
                        'Thread must be instance of Podium\Api\Interfaces\ThreadRepositoryInterface!'
                    ),
                ]
            );
        }

        if (!$forum instanceof ForumRepositoryInterface) {
            return PodiumResponse::error(
                [
                    'exception' => new InvalidArgumentException(
                        'Forum must be instance of Podium\Api\Interfaces\ForumRepositoryInterface!'
                    ),
                ]
            );
        }

        if (!$this->beforeMove()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$thread->move($forum)) {
                throw new ServiceException($thread->getErrors());
            }

            $postsCount = $thread->getPostsCount();

            /** @var ForumRepositoryInterface $threadParent */
            $threadParent = $thread->getParent();
            if (!$threadParent->updateCounters(-1, -$postsCount)) {
                throw new Exception('Error while updating old forum counters!');
            }
            if (!$forum->updateCounters(1, $postsCount)) {
                throw new Exception('Error while updating new forum counters!');
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while moving thread', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterMove($thread);

        return PodiumResponse::success();
    }

    /**
     * Calls after moving the thread successfully.
     */
    private function afterMove(ThreadRepositoryInterface $thread): void
    {
        $this->trigger(self::EVENT_AFTER_MOVING, new MoveEvent(['repository' => $thread]));
    }
}
