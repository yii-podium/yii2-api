<?php

declare(strict_types=1);

namespace Podium\Api\Services\Post;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\BuildEvent;
use Podium\Api\Interfaces\CategorisedBuilderInterface;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
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
    public function beforeCreate(): bool
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
        if (
            !$post instanceof PostRepositoryInterface
            || !$thread instanceof ThreadRepositoryInterface
            || !$this->beforeCreate()
        ) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            /** @var ForumRepositoryInterface $threadParent */
            $threadParent = $thread->getParent();
            if (!$post->create($author, $thread, $data)) {
                return PodiumResponse::error($post->getErrors());
            }

            if (!$thread->updateCounters(1)) {
                throw new Exception('Error while updating thread counters!');
            }
            if (!$threadParent->updateCounters(0, 1)) {
                throw new Exception('Error while updating forum counters!');
            }

            $this->afterCreate($post);
            $transaction->commit();

            return PodiumResponse::success();
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while creating post', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }
    }

    /**
     * Calls after creating the post successfully.
     */
    public function afterCreate(PostRepositoryInterface $post): void
    {
        $this->trigger(self::EVENT_AFTER_CREATING, new BuildEvent(['repository' => $post]));
    }

    /**
     * Calls before editing the post.
     */
    public function beforeEdit(): bool
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
        if (!$post instanceof PostRepositoryInterface || !$this->beforeEdit()) {
            return PodiumResponse::error();
        }

        try {
            if (!$post->edit($data)) {
                return PodiumResponse::error($post->getErrors());
            }

            $this->afterEdit($post);

            return PodiumResponse::success();
        } catch (Throwable $exc) {
            Yii::error(['Exception while editing post', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }
    }

    /**
     * Calls after editing the post successfully.
     */
    public function afterEdit(PostRepositoryInterface $post): void
    {
        $this->trigger(self::EVENT_AFTER_EDITING, new BuildEvent(['repository' => $post]));
    }
}
