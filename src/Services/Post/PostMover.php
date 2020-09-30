<?php

declare(strict_types=1);

namespace Podium\Api\Services\Post;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\MoveEvent;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\MoverInterface;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Exception;
use yii\db\Transaction;

final class PostMover extends Component implements MoverInterface
{
    public const EVENT_BEFORE_MOVING = 'podium.post.moving.before';
    public const EVENT_AFTER_MOVING = 'podium.post.moving.after';

    /**
     * Calls before moving the post.
     */
    private function beforeMove(): bool
    {
        $event = new MoveEvent();
        $this->trigger(self::EVENT_BEFORE_MOVING, $event);

        return $event->canMove;
    }

    /**
     * Moves the post to another thread.
     */
    public function move(RepositoryInterface $post, RepositoryInterface $thread): PodiumResponse
    {
        if (
            !$post instanceof PostRepositoryInterface
            || !$thread instanceof ThreadRepositoryInterface
            || !$this->beforeMove()
        ) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            /** @var ForumRepositoryInterface $threadParent */
            $threadParent = $thread->getParent();
            if (!$post->move($thread)) {
                throw new ServiceException($post->getErrors());
            }

            /** @var ThreadRepositoryInterface $postParent */
            $postParent = $post->getParent();
            if (!$postParent->updateCounters(-1)) {
                throw new Exception('Error while updating old thread counters!');
            }
            /** @var ForumRepositoryInterface $postGrandParent */
            $postGrandParent = $postParent->getParent();
            if (!$postGrandParent->updateCounters(0, -1)) {
                throw new Exception('Error while updating old forum counters!');
            }
            if (!$thread->updateCounters(1)) {
                throw new Exception('Error while updating new thread counters!');
            }
            if (!$threadParent->updateCounters(0, 1)) {
                throw new Exception('Error while updating new forum counters!');
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while moving post', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterMove($post);

        return PodiumResponse::success();
    }

    /**
     * Calls after moving the post successfully.
     */
    private function afterMove(PostRepositoryInterface $post): void
    {
        $this->trigger(self::EVENT_AFTER_MOVING, new MoveEvent(['repository' => $post]));
    }
}
