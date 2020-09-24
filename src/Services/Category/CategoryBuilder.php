<?php

declare(strict_types=1);

namespace Podium\Api\Services\Category;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\BuildEvent;
use Podium\Api\Interfaces\CategoryBuilderInterface;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Throwable;
use Yii;
use yii\base\Component;

final class CategoryBuilder extends Component implements CategoryBuilderInterface
{
    public const EVENT_BEFORE_CREATING = 'podium.category.creating.before';
    public const EVENT_AFTER_CREATING = 'podium.category.creating.after';
    public const EVENT_BEFORE_EDITING = 'podium.category.editing.before';
    public const EVENT_AFTER_EDITING = 'podium.category.editing.after';

    /**
     * Calls before creating a category.
     */
    public function beforeCreate(): bool
    {
        $event = new BuildEvent();
        $this->trigger(self::EVENT_BEFORE_CREATING, $event);

        return $event->canCreate;
    }

    /**
     * Creates new category.
     */
    public function create(
        CategoryRepositoryInterface $category,
        MemberRepositoryInterface $author,
        array $data = []
    ): PodiumResponse {
        if (!$this->beforeCreate()) {
            return PodiumResponse::error();
        }

        try {
            if (!$category->create($author, $data)) {
                return PodiumResponse::error($category->getErrors());
            }

            $this->afterCreate($category);

            return PodiumResponse::success();
        } catch (Throwable $exc) {
            Yii::error(['Exception while creating category', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }
    }

    /**
     * Calls after creating the category successfully.
     */
    public function afterCreate(CategoryRepositoryInterface $category): void
    {
        $this->trigger(self::EVENT_AFTER_CREATING, new BuildEvent(['repository' => $category]));
    }

    /**
     * Calls before editing the category.
     */
    public function beforeEdit(): bool
    {
        $event = new BuildEvent();
        $this->trigger(self::EVENT_BEFORE_EDITING, $event);

        return $event->canEdit;
    }

    /**
     * Edits the category.
     */
    public function edit(CategoryRepositoryInterface $category, array $data = []): PodiumResponse
    {
        if (!$this->beforeEdit()) {
            return PodiumResponse::error();
        }

        try {
            if (!$category->edit($data)) {
                return PodiumResponse::error($category->getErrors());
            }

            $this->afterEdit($category);

            return PodiumResponse::success();
        } catch (Throwable $exc) {
            Yii::error(['Exception while editing category', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }
    }

    /**
     * Calls after editing the category successfully.
     */
    public function afterEdit(CategoryRepositoryInterface $category): void
    {
        $this->trigger(self::EVENT_AFTER_EDITING, new BuildEvent(['repository' => $category]));
    }
}
