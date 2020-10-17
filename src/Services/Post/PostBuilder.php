<?php

declare(strict_types=1);

namespace Podium\Api\Services\Post;

use InvalidArgumentException;
use Podium\Api\Events\BuildEvent;
use Podium\Api\Interfaces\CategorisedBuilderInterface;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\PodiumResponse;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Exception;
use yii\db\Transaction;

final class PostBuilder extends Component implements CategorisedBuilderInterface
{
    public const EVENT_BEFORE_CREATING = 'podium.post.creating.before';
    public const EVENT_AFTER_CREATING = 'podium.post.creating.after';
    public const EVENT_BEFORE_EDITING = 'podium.post.editing.before';
    public const EVENT_AFTER_EDITING = 'podium.post.editing.after';

    /**
     * Calls before creating the post.
     */
    private function beforeCreate(): bool
    {
        $event = new BuildEvent();
        $this->trigger(self::EVENT_BEFORE_CREATING, $event);

        return $event->canCreate;
    }

    /**
     * Creates new post.
     */
    public function create(
        RepositoryInterface $post,
        MemberRepositoryInterface $author,
        RepositoryInterface $thread,
        array $data = []
    ): PodiumResponse {
        if (!$post instanceof PostRepositoryInterface) {
            return PodiumResponse::error(
                [
                    'exception' => new InvalidArgumentException(
                        'Post must be instance of Podium\Api\Interfaces\PostRepositoryInterface!'
                    ),
                ]
            );
        }

        if (!$thread instanceof ThreadRepositoryInterface) {
            return PodiumResponse::error(
                [
                    'exception' => new InvalidArgumentException(
                        'Thread must be instance of Podium\Api\Interfaces\ThreadRepositoryInterface!'
                    ),
                ]
            );
        }

        if (!$this->beforeCreate()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($author->isBanned()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'member.banned')]);
            }

            if ($thread->isLocked()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'thread.locked')]);
            }

            /** @var ForumRepositoryInterface $threadParent */
            $threadParent = $thread->getParent();
            if (!$post->create($author, $thread, $data)) {
                throw new ServiceException($post->getErrors());
            }

            if (!$thread->updateCounters(1)) {
                throw new Exception('Error while updating thread counters!');
            }
            if (!$threadParent->updateCounters(0, 1)) {
                throw new Exception('Error while updating forum counters!');
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while creating post', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterCreate($post);

        return PodiumResponse::success();
    }

    /**
     * Calls after creating the post successfully.
     */
    private function afterCreate(PostRepositoryInterface $post): void
    {
        $this->trigger(self::EVENT_AFTER_CREATING, new BuildEvent(['repository' => $post]));
    }

    /**
     * Calls before editing the post.
     */
    private function beforeEdit(): bool
    {
        $event = new BuildEvent();
        $this->trigger(self::EVENT_BEFORE_EDITING, $event);

        return $event->canEdit;
    }

    /**
     * Edits the post.
     */
    public function edit(RepositoryInterface $post, array $data = []): PodiumResponse
    {
        if (!$post instanceof PostRepositoryInterface) {
            return PodiumResponse::error(
                [
                    'exception' => new InvalidArgumentException(
                        'Post must be instance of Podium\Api\Interfaces\PostRepositoryInterface!'
                    ),
                ]
            );
        }

        if (!$this->beforeEdit()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$post->edit($data)) {
                throw new ServiceException($post->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while editing post', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterEdit($post);

        return PodiumResponse::success();
    }

    /**
     * Calls after editing the post successfully.
     */
    private function afterEdit(PostRepositoryInterface $post): void
    {
        $this->trigger(self::EVENT_AFTER_EDITING, new BuildEvent(['repository' => $post]));
    }
}
