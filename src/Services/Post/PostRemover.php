<?php

declare(strict_types=1);

namespace Podium\Api\Services\Post;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\RemoveEvent;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Exception;
use yii\db\Transaction;

final class PostRemover extends Component implements RemoverInterface
{
    public const EVENT_BEFORE_REMOVING = 'podium.post.removing.before';
    public const EVENT_AFTER_REMOVING = 'podium.post.removing.after';

    public function beforeRemove(): bool
    {
        $event = new RemoveEvent();
        $this->trigger(self::EVENT_BEFORE_REMOVING, $event);

        return $event->canRemove;
    }

    /**
     * Removes the post.
     */
    public function remove(RepositoryInterface $post): PodiumResponse
    {
        if (!$post instanceof PostRepositoryInterface || !$this->beforeRemove()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$post->isArchived()) {
                return PodiumResponse::error(['api' => Yii::t('podium.error', 'post.must.be.archived')]);
            }

            if (!$post->delete()) {
                return PodiumResponse::error();
            }

            /** @var ThreadRepositoryInterface $thread */
            $thread = $post->getParent();
            if (!$thread->updateCounters(-1)) {
                throw new Exception('Error while updating thread counters!');
            }

            /** @var ForumRepositoryInterface $forum */
            $forum = $thread->getParent();
            if (!$forum->updateCounters(0, -1)) {
                throw new Exception('Error while updating forum counters!');
            }

            $this->afterRemove();
            $transaction->commit();

            return PodiumResponse::success();
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while deleting post', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }
    }

    public function afterRemove(): void
    {
        $this->trigger(self::EVENT_AFTER_REMOVING);
    }
}
