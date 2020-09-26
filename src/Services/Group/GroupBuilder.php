<?php

declare(strict_types=1);

namespace Podium\Api\Services\Group;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\BuildEvent;
use Podium\Api\Interfaces\BuilderInterface;
use Podium\Api\Interfaces\GroupRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Throwable;
use Yii;
use yii\base\Component;

final class GroupBuilder extends Component implements BuilderInterface
{
    public const EVENT_BEFORE_CREATING = 'podium.group.creating.before';
    public const EVENT_AFTER_CREATING = 'podium.group.creating.after';
    public const EVENT_BEFORE_EDITING = 'podium.group.editing.before';
    public const EVENT_AFTER_EDITING = 'podium.group.editing.after';

    /**
     * Calls before creating a group.
     */
    public function beforeCreate(): bool
    {
        $event = new BuildEvent();
        $this->trigger(self::EVENT_BEFORE_CREATING, $event);

        return $event->canCreate;
    }

    /**
     * Creates new group.
     */
    public function create(RepositoryInterface $group, array $data = []): PodiumResponse
    {
        if (!$group instanceof GroupRepositoryInterface || !$this->beforeCreate()) {
            return PodiumResponse::error();
        }

        try {
            if (!$group->create($data)) {
                return PodiumResponse::error($group->getErrors());
            }

            $this->afterCreate($group);

            return PodiumResponse::success();
        } catch (Throwable $exc) {
            Yii::error(['Exception while creating group', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }
    }

    /**
     * Calls after creating the group successfully.
     */
    public function afterCreate(GroupRepositoryInterface $group): void
    {
        $this->trigger(self::EVENT_AFTER_CREATING, new BuildEvent(['repository' => $group]));
    }

    /**
     * Calls before editing the group.
     */
    public function beforeEdit(): bool
    {
        $event = new BuildEvent();
        $this->trigger(self::EVENT_BEFORE_EDITING, $event);

        return $event->canEdit;
    }

    /**
     * Edits the group.
     */
    public function edit(RepositoryInterface $group, array $data = []): PodiumResponse
    {
        if (!$group instanceof GroupRepositoryInterface || !$this->beforeEdit()) {
            return PodiumResponse::error();
        }

        try {
            if (!$group->edit($data)) {
                return PodiumResponse::error($group->getErrors());
            }

            $this->afterEdit($group);

            return PodiumResponse::success();
        } catch (Throwable $exc) {
            Yii::error(['Exception while editing group', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }
    }

    /**
     * Calls after editing the group successfully.
     */
    public function afterEdit(GroupRepositoryInterface $group): void
    {
        $this->trigger(self::EVENT_AFTER_EDITING, new BuildEvent(['repository' => $group]));
    }
}
