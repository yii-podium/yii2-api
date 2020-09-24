<?php

declare(strict_types=1);

namespace Podium\Api\Services\Forum;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\BuildEvent;
use Podium\Api\Interfaces\CategorisedBuilderInterface;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Throwable;
use Yii;
use yii\base\Component;

final class ForumBuilder extends Component implements CategorisedBuilderInterface
{
    public const EVENT_BEFORE_CREATING = 'podium.forum.creating.before';
    public const EVENT_AFTER_CREATING = 'podium.forum.creating.after';
    public const EVENT_BEFORE_EDITING = 'podium.forum.editing.before';
    public const EVENT_AFTER_EDITING = 'podium.forum.editing.after';

    /**
     * Calls before creating the forum.
     */
    public function beforeCreate(): bool
    {
        $event = new BuildEvent();
        $this->trigger(self::EVENT_BEFORE_CREATING, $event);

        return $event->canCreate;
    }

    /**
     * Creates new forum.
     */
    public function create(
        RepositoryInterface $forum,
        MemberRepositoryInterface $author,
        RepositoryInterface $category,
        array $data = []
    ): PodiumResponse {
        if (
            !$forum instanceof ForumRepositoryInterface
            || !$category instanceof CategoryRepositoryInterface
            || !$this->beforeCreate()
        ) {
            return PodiumResponse::error();
        }

        try {
            if (!$forum->create($author, $category, $data)) {
                return PodiumResponse::error($forum->getErrors());
            }

            $this->afterCreate($forum);

            return PodiumResponse::success();
        } catch (Throwable $exc) {
            Yii::error(['Exception while creating forum', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }
    }

    /**
     * Calls after creating the forum successfully.
     */
    public function afterCreate(ForumRepositoryInterface $forum): void
    {
        $this->trigger(self::EVENT_AFTER_CREATING, new BuildEvent(['repository' => $forum]));
    }

    /**
     * Calls before editing the forum.
     */
    public function beforeEdit(): bool
    {
        $event = new BuildEvent();
        $this->trigger(self::EVENT_BEFORE_EDITING, $event);

        return $event->canEdit;
    }

    /**
     * Edits the forum.
     */
    public function edit(RepositoryInterface $forum, array $data = []): PodiumResponse
    {
        if (!$forum instanceof ForumRepositoryInterface || !$this->beforeEdit()) {
            return PodiumResponse::error();
        }

        try {
            if (!$forum->edit($data)) {
                return PodiumResponse::error($forum->getErrors());
            }

            $this->afterEdit($forum);

            return PodiumResponse::success();
        } catch (Throwable $exc) {
            Yii::error(['Exception while editing forum', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }
    }

    /**
     * Calls after editing the forum successfully.
     */
    public function afterEdit(ForumRepositoryInterface $forum): void
    {
        $this->trigger(self::EVENT_AFTER_EDITING, new BuildEvent(['repository' => $forum]));
    }
}
